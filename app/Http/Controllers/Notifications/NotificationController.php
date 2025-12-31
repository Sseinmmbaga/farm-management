<?php

namespace App\Http\Controllers\Notifications;

use App\Http\Controllers\Controller;
use App\Models\Notifications\NotificationPreference;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display the notification center.
     */
    public function index(Request $request): View
    {
        $user = auth()->user();
        $filter = $request->get('filter', 'all'); // all, unread, read
        $category = $request->get('category'); // certification, document, training, service_request, system

        $query = $user->notifications();

        // Apply filter
        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        }

        // Apply category filter
        if ($category) {
            $query->where('data->category', $category);
        }

        $notifications = $query->orderBy('created_at', 'desc')->paginate(20);

        $counts = [
            'all' => $user->notifications()->count(),
            'unread' => $user->unreadNotifications()->count(),
            'read' => $user->readNotifications()->count(),
        ];

        $categories = [
            'certification' => 'Certifications',
            'document' => 'Documents',
            'training' => 'Training',
            'service_request' => 'Service Requests',
            'system' => 'System',
        ];

        return view('notifications.index', compact('notifications', 'filter', 'category', 'counts', 'categories'));
    }

    /**
     * Get notifications for header dropdown (AJAX).
     */
    public function getDropdown(): JsonResponse
    {
        $user = auth()->user();

        $notifications = $user->unreadNotifications()
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->data['type'] ?? 'notification',
                    'title' => $notification->data['title'] ?? 'Notification',
                    'message' => $notification->data['message'] ?? '',
                    'icon' => $notification->data['icon'] ?? 'fa-bell',
                    'color' => $notification->data['color'] ?? 'primary',
                    'action_url' => $notification->data['action_url'] ?? null,
                    'created_at' => $notification->created_at->diffForHumans(),
                ];
            });

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $user->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark a notification as read.
     */
    public function markAsRead(string $id): JsonResponse
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json([
            'success' => true,
            'unread_count' => auth()->user()->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function markAllAsRead(): JsonResponse|RedirectResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => 0,
            ]);
        }

        return redirect()->back()->with('success', 'All notifications marked as read.');
    }

    /**
     * Delete a notification.
     */
    public function destroy(string $id): JsonResponse|RedirectResponse
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->delete();

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'unread_count' => auth()->user()->unreadNotifications()->count(),
            ]);
        }

        return redirect()->back()->with('success', 'Notification deleted.');
    }

    /**
     * Delete all read notifications.
     */
    public function deleteAllRead(): JsonResponse|RedirectResponse
    {
        auth()->user()->readNotifications()->delete();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'All read notifications deleted.');
    }

    /**
     * Show notification preferences.
     */
    public function preferences(): View
    {
        $preferences = NotificationPreference::getOrCreateForUser(auth()->user());

        return view('notifications.preferences', compact('preferences'));
    }

    /**
     * Update notification preferences.
     */
    public function updatePreferences(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email_enabled' => 'boolean',
            'sms_enabled' => 'boolean',
            'in_app_enabled' => 'boolean',
            'certification_alerts' => 'boolean',
            'document_alerts' => 'boolean',
            'training_reminders' => 'boolean',
            'service_request_updates' => 'boolean',
            'system_announcements' => 'boolean',
            'certification_reminder_days' => 'integer|min:1|max:90',
            'document_reminder_days' => 'integer|min:1|max:90',
            'training_reminder_days' => 'integer|min:1|max:30',
        ]);

        // Convert checkboxes to boolean
        $validated['email_enabled'] = $request->has('email_enabled');
        $validated['sms_enabled'] = $request->has('sms_enabled');
        $validated['in_app_enabled'] = $request->has('in_app_enabled');
        $validated['certification_alerts'] = $request->has('certification_alerts');
        $validated['document_alerts'] = $request->has('document_alerts');
        $validated['training_reminders'] = $request->has('training_reminders');
        $validated['service_request_updates'] = $request->has('service_request_updates');
        $validated['system_announcements'] = $request->has('system_announcements');

        $preferences = NotificationPreference::getOrCreateForUser(auth()->user());
        $preferences->update($validated);

        return redirect()->back()->with('success', 'Notification preferences updated successfully.');
    }

    /**
     * Get unread notification count (for polling).
     */
    public function getUnreadCount(): JsonResponse
    {
        return response()->json([
            'unread_count' => auth()->user()->unreadNotifications()->count(),
        ]);
    }
}
