<?php

namespace App\Channels;

use App\Models\Notifications\NotificationLog;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsChannel
{
    /**
     * Send the given notification via SMS.
     */
    public function send(object $notifiable, Notification $notification): void
    {
        // Get the phone number from the notifiable
        $phone = $notifiable->routeNotificationFor('sms', $notification);

        if (!$phone) {
            Log::warning('SMS notification skipped: No phone number for user ' . ($notifiable->id ?? 'unknown'));
            return;
        }

        // Get the SMS message from the notification
        if (!method_exists($notification, 'toSms')) {
            Log::warning('SMS notification skipped: No toSms method on notification');
            return;
        }

        $message = $notification->toSms($notifiable);

        if (!$message) {
            return;
        }

        // Send SMS based on configured provider
        $provider = config('services.sms.provider', 'log');

        try {
            $result = match ($provider) {
                'twilio' => $this->sendViaTwilio($phone, $message),
                'africastalking' => $this->sendViaAfricasTalking($phone, $message),
                'nexmo' => $this->sendViaNexmo($phone, $message),
                'log' => $this->logSms($phone, $message),
                default => $this->logSms($phone, $message),
            };

            // Log successful send
            NotificationLog::log(
                type: method_exists($notification, 'getType') ? $notification->getType() : 'sms',
                channel: NotificationLog::CHANNEL_SMS,
                status: NotificationLog::STATUS_SENT,
                userId: $notifiable->id ?? null,
                recipientPhone: $phone,
                message: $message,
                metadata: ['provider' => $provider]
            );

        } catch (\Exception $e) {
            Log::error("SMS notification failed: " . $e->getMessage());

            NotificationLog::log(
                type: method_exists($notification, 'getType') ? $notification->getType() : 'sms',
                channel: NotificationLog::CHANNEL_SMS,
                status: NotificationLog::STATUS_FAILED,
                userId: $notifiable->id ?? null,
                recipientPhone: $phone,
                message: $message,
                errorMessage: $e->getMessage()
            );
        }
    }

    /**
     * Send SMS via Twilio.
     */
    protected function sendViaTwilio(string $phone, string $message): bool
    {
        $sid = config('services.sms.twilio.sid');
        $token = config('services.sms.twilio.token');
        $from = config('services.sms.twilio.from');

        $response = Http::withBasicAuth($sid, $token)
            ->asForm()
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$sid}/Messages.json", [
                'From' => $from,
                'To' => $phone,
                'Body' => $message,
            ]);

        if (!$response->successful()) {
            throw new \Exception("Twilio API error: " . $response->body());
        }

        return true;
    }

    /**
     * Send SMS via Africa's Talking.
     */
    protected function sendViaAfricasTalking(string $phone, string $message): bool
    {
        $username = config('services.sms.africastalking.username');
        $apiKey = config('services.sms.africastalking.api_key');
        $from = config('services.sms.africastalking.from');

        $response = Http::withHeaders([
            'apiKey' => $apiKey,
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Accept' => 'application/json',
        ])->asForm()->post('https://api.africastalking.com/version1/messaging', [
            'username' => $username,
            'to' => $phone,
            'message' => $message,
            'from' => $from,
        ]);

        if (!$response->successful()) {
            throw new \Exception("Africa's Talking API error: " . $response->body());
        }

        return true;
    }

    /**
     * Send SMS via Nexmo/Vonage.
     */
    protected function sendViaNexmo(string $phone, string $message): bool
    {
        $apiKey = config('services.sms.nexmo.api_key');
        $apiSecret = config('services.sms.nexmo.api_secret');
        $from = config('services.sms.nexmo.from');

        $response = Http::post('https://rest.nexmo.com/sms/json', [
            'api_key' => $apiKey,
            'api_secret' => $apiSecret,
            'from' => $from,
            'to' => $phone,
            'text' => $message,
        ]);

        if (!$response->successful()) {
            throw new \Exception("Nexmo API error: " . $response->body());
        }

        return true;
    }

    /**
     * Log SMS for development/testing.
     */
    protected function logSms(string $phone, string $message): bool
    {
        Log::channel('daily')->info("SMS Notification", [
            'to' => $phone,
            'message' => $message,
        ]);

        return true;
    }
}
