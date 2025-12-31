<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Farmers\Farmer;
use App\Models\Farmers\FarmerDocument;
use App\Models\Farms\Farm;
use App\Models\Farms\FarmVisit;
use App\Models\Logs\ActivityLog;
use App\Models\Logs\HarvestLog;
use App\Models\ServiceRequest;
use App\Models\Stock\StockDistribution;
use App\Models\Stock\StockItem;
use App\Models\Tasks\Task;
use App\Models\Training\TrainingAttendance;
use App\Models\Training\TrainingCertificate;
use App\Models\Training\TrainingSession;
use Illuminate\Http\Request;

class FarmerDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        // Get the farmer record linked to this user (if farmer role)
        $farmer = $this->getFarmerForUser($user);

        if (!$farmer) {
            return view('dashboard.farmer.index', [
                'activeFarms' => 0,
                'plantedAcres' => 0,
                'pendingTasks' => 0,
                'trainings' => 0,
                'recentActivity' => collect([]),
                'upcomingEvents' => collect([]),
                'farmer' => null,
            ]);
        }

        // Get stats
        $activeFarms = $farmer->farms()->active()->count();
        $plantedAcres = $farmer->farms()->sum('cultivated_area') ?? 0;
        $pendingTasks = Task::forFarmer($farmer->id)->active()->count();
        $trainings = TrainingAttendance::where('farmer_id', $farmer->id)->attended()->count();

        // Get recent activity (combining different activities)
        $recentActivity = $this->getRecentActivity($farmer);

        // Get upcoming events
        $upcomingEvents = $this->getUpcomingEvents($farmer);

        return view('dashboard.farmer.index', compact(
            'activeFarms',
            'plantedAcres',
            'pendingTasks',
            'trainings',
            'recentActivity',
            'upcomingEvents',
            'farmer'
        ));
    }

    public function myFarms(Request $request)
    {
        $user = auth()->user();
        $farmer = $this->getFarmerForUser($user);

        if (!$farmer) {
            return view('dashboard.farmer.my-farms', [
                'stats' => [
                    'total_farms' => 0,
                    'active_farms' => 0,
                    'total_area' => 0,
                    'organic_certified' => 0,
                ],
                'farms' => collect([]),
            ]);
        }

        $stats = [
            'total_farms' => $farmer->farms()->count(),
            'active_farms' => $farmer->farms()->active()->count(),
            'total_area' => $farmer->farms()->sum('total_area') ?? 0,
            'organic_certified' => $farmer->farms()->organic()->count(),
        ];

        $farms = $farmer->farms()
            ->with(['region', 'district', 'village'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dashboard.farmer.my-farms', compact('stats', 'farms', 'farmer'));
    }

    public function payments(Request $request)
    {
        $user = auth()->user();
        $farmer = $this->getFarmerForUser($user);

        if (!$farmer) {
            return view('dashboard.farmer.payments', [
                'stats' => [
                    'pending' => 0,
                    'received_this_year' => 0,
                    'total_amount' => 0,
                ],
                'payments' => collect([]),
            ]);
        }

        // Since there's no Payment model, we'll track stock distributions as "received items"
        // and show them as a form of "payment in kind"
        $distributions = StockDistribution::where('farmer_id', $farmer->id)
            ->with(['stockItem', 'distributedBy'])
            ->orderBy('distribution_date', 'desc')
            ->paginate(10);

        $stats = [
            'pending' => 0, // No payment tracking yet
            'received_this_year' => StockDistribution::where('farmer_id', $farmer->id)
                ->whereYear('distribution_date', now()->year)
                ->count(),
            'total_amount' => 0, // No monetary tracking yet
        ];

        return view('dashboard.farmer.payments', compact('stats', 'distributions', 'farmer'));
    }

    public function myTrainings(Request $request)
    {
        $user = auth()->user();
        $farmer = $this->getFarmerForUser($user);

        if (!$farmer) {
            return view('dashboard.farmer.trainings', [
                'stats' => [
                    'attended' => 0,
                    'upcoming' => 0,
                    'certificates' => 0,
                ],
                'attendances' => collect([]),
                'upcomingSessions' => collect([]),
            ]);
        }

        $stats = [
            'attended' => TrainingAttendance::where('farmer_id', $farmer->id)->attended()->count(),
            'upcoming' => TrainingSession::where('status', 'scheduled')
                ->where('scheduled_date', '>=', now())
                ->count(),
            'certificates' => TrainingAttendance::where('farmer_id', $farmer->id)
                ->certificateIssued()
                ->count(),
        ];

        $attendances = TrainingAttendance::where('farmer_id', $farmer->id)
            ->with(['session.program'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $upcomingSessions = TrainingSession::where('status', 'scheduled')
            ->where('scheduled_date', '>=', now())
            ->with('program')
            ->orderBy('scheduled_date')
            ->take(5)
            ->get();

        return view('dashboard.farmer.trainings', compact('stats', 'attendances', 'upcomingSessions', 'farmer'));
    }

    public function myDistributions(Request $request)
    {
        $user = auth()->user();
        $farmer = $this->getFarmerForUser($user);

        if (!$farmer) {
            return view('dashboard.farmer.distributions', [
                'stats' => [
                    'total_distributions' => 0,
                    'this_year' => 0,
                    'items_received' => 0,
                ],
                'distributions' => collect([]),
            ]);
        }

        $stats = [
            'total_distributions' => StockDistribution::where('farmer_id', $farmer->id)->count(),
            'this_year' => StockDistribution::where('farmer_id', $farmer->id)
                ->whereYear('distribution_date', now()->year)
                ->count(),
            'items_received' => StockDistribution::where('farmer_id', $farmer->id)->sum('quantity'),
        ];

        $distributions = StockDistribution::where('farmer_id', $farmer->id)
            ->with(['stockItem', 'distributedBy'])
            ->orderBy('distribution_date', 'desc')
            ->paginate(10);

        return view('dashboard.farmer.distributions', compact('stats', 'distributions', 'farmer'));
    }

    public function serviceRequests(Request $request)
    {
        $user = auth()->user();
        $farmer = $this->getFarmerForUser($user);

        if (!$farmer) {
            return redirect()->route('service-requests.index');
        }

        $stats = [
            'open' => ServiceRequest::forFarmer($farmer->id)->open()->count(),
            'in_progress' => ServiceRequest::forFarmer($farmer->id)->inProgress()->count(),
            'resolved' => ServiceRequest::forFarmer($farmer->id)->resolved()->count(),
            'total' => ServiceRequest::forFarmer($farmer->id)->count(),
        ];

        $requests = ServiceRequest::forFarmer($farmer->id)
            ->with(['assignedTo'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dashboard.farmer.service-requests', compact('stats', 'requests', 'farmer'));
    }

    public function viewProfile(Request $request)
    {
        $user = auth()->user();
        $farmer = $this->getFarmerForUser($user);

        return view('dashboard.farmer.profile.view', compact('farmer'));
    }

    public function editProfile(Request $request)
    {
        $user = auth()->user();
        $farmer = $this->getFarmerForUser($user);

        return view('dashboard.farmer.profile.edit', compact('farmer'));
    }

    public function farmRecords(Request $request)
    {
        $user = auth()->user();
        $farmer = $this->getFarmerForUser($user);

        if (!$farmer) {
            return view('dashboard.farmer.farm-records', [
                'stats' => [
                    'total_records' => 0,
                    'active_tasks' => 0,
                    'harvests_recorded' => 0,
                    'pending_actions' => 0,
                ],
                'records' => collect([]),
                'farmer' => null,
            ]);
        }

        // Get activity logs for farm records (seeding, inputs, observations, harvests)
        // Filter to only cotton and sesame crops
        $records = ActivityLog::where('farmer_id', $farmer->id)
            ->with(['farm', 'season', 'seedingLog', 'harvestLog', 'inputLog', 'observationLog'])
            ->whereIn('type', ['seeding', 'harvest', 'input', 'observation'])
            ->orderBy('log_date', 'desc')
            ->paginate(10);

        $stats = [
            'total_records' => ActivityLog::where('farmer_id', $farmer->id)->count(),
            'active_tasks' => ActivityLog::where('farmer_id', $farmer->id)->pending()->count(),
            'harvests_recorded' => ActivityLog::where('farmer_id', $farmer->id)->harvests()->count(),
            'pending_actions' => ActivityLog::where('farmer_id', $farmer->id)->pending()->count(),
        ];

        return view('dashboard.farmer.farm-records', compact('stats', 'records', 'farmer'));
    }

    public function myActivityLog(Request $request)
    {
        $user = auth()->user();
        $farmer = $this->getFarmerForUser($user);

        if (!$farmer) {
            return view('dashboard.farmer.activity-log', [
                'stats' => [
                    'today' => 0,
                    'this_week' => 0,
                    'this_month' => 0,
                    'total' => 0,
                ],
                'activities' => collect([]),
                'farmer' => null,
            ]);
        }

        // Get activity logs with details for cotton and sesame tracking
        $activities = ActivityLog::where('farmer_id', $farmer->id)
            ->with(['farm', 'season', 'seedingLog', 'harvestLog', 'inputLog', 'observationLog'])
            ->orderBy('log_date', 'desc')
            ->paginate(15);

        $stats = [
            'today' => ActivityLog::where('farmer_id', $farmer->id)
                ->whereDate('log_date', today())
                ->count(),
            'this_week' => ActivityLog::where('farmer_id', $farmer->id)
                ->whereBetween('log_date', [now()->startOfWeek(), now()->endOfWeek()])
                ->count(),
            'this_month' => ActivityLog::where('farmer_id', $farmer->id)
                ->whereMonth('log_date', now()->month)
                ->whereYear('log_date', now()->year)
                ->count(),
            'total' => ActivityLog::where('farmer_id', $farmer->id)->count(),
        ];

        return view('dashboard.farmer.activity-log', compact('stats', 'activities', 'farmer'));
    }

    public function harvestHistory(Request $request)
    {
        $user = auth()->user();
        $farmer = $this->getFarmerForUser($user);

        if (!$farmer) {
            return view('dashboard.farmer.harvest-history', [
                'stats' => [
                    'total_harvests' => 0,
                    'total_volume' => 0,
                    'avg_per_harvest' => 0,
                    'pending_weighing' => 0,
                ],
                'harvests' => collect([]),
                'farmer' => null,
            ]);
        }

        // Get harvest logs through activity logs - filter for cotton and sesame only
        $harvestActivityIds = ActivityLog::where('farmer_id', $farmer->id)
            ->harvests()
            ->pluck('id');

        $harvests = HarvestLog::whereIn('activity_log_id', $harvestActivityIds)
            ->whereIn('crop_type', ['cotton', 'sesame'])
            ->with(['activityLog.farm'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $totalVolume = HarvestLog::whereIn('activity_log_id', $harvestActivityIds)
            ->whereIn('crop_type', ['cotton', 'sesame'])
            ->sum('quantity_harvested');
        $totalHarvests = HarvestLog::whereIn('activity_log_id', $harvestActivityIds)
            ->whereIn('crop_type', ['cotton', 'sesame'])
            ->count();

        $stats = [
            'total_harvests' => $totalHarvests,
            'total_volume' => $totalVolume,
            'avg_per_harvest' => $totalHarvests > 0 ? round($totalVolume / $totalHarvests, 2) : 0,
            'pending_weighing' => ActivityLog::where('farmer_id', $farmer->id)
                ->harvests()
                ->pending()
                ->count(),
        ];

        return view('dashboard.farmer.harvest-history', compact('stats', 'harvests', 'farmer'));
    }

    public function certificates(Request $request)
    {
        $user = auth()->user();
        $farmer = $this->getFarmerForUser($user);

        if (!$farmer) {
            return view('dashboard.farmer.certificates', [
                'stats' => [
                    'valid' => 0,
                    'pending_renewal' => 0,
                    'expired' => 0,
                    'total' => 0,
                ],
                'certificates' => collect([]),
                'trainingCertificates' => collect([]),
                'documentCertificates' => collect([]),
                'farmer' => null,
            ]);
        }

        // Get training certificates
        $trainingCertificates = TrainingCertificate::where('farmer_id', $farmer->id)
            ->with(['program', 'session'])
            ->orderBy('issue_date', 'desc')
            ->get();

        // Get farmer documents that are certificates
        $documentCertificates = FarmerDocument::where('farmer_id', $farmer->id)
            ->whereIn('type', [
                FarmerDocument::TYPE_CERTIFICATE,
                FarmerDocument::TYPE_ORGANIC_CERTIFICATE,
                FarmerDocument::TYPE_TRAINING_CERTIFICATE,
            ])
            ->orderBy('issue_date', 'desc')
            ->get();

        // Combine certificates
        $allCertificates = $trainingCertificates->merge($documentCertificates);

        $stats = [
            'valid' => $trainingCertificates->where('status', 'active')->count() +
                       $documentCertificates->filter(fn($d) => !$d->is_expired)->count(),
            'pending_renewal' => $trainingCertificates->filter(fn($c) => $c->days_to_expiry <= 60 && $c->days_to_expiry > 0)->count() +
                                 $documentCertificates->filter(fn($d) => $d->is_expiring)->count(),
            'expired' => $trainingCertificates->where('status', 'expired')->count() +
                         $documentCertificates->filter(fn($d) => $d->is_expired)->count(),
            'total' => $allCertificates->count(),
        ];

        return view('dashboard.farmer.certificates', compact('stats', 'trainingCertificates', 'documentCertificates', 'farmer'));
    }

    public function seedsReceived(Request $request)
    {
        $user = auth()->user();
        $farmer = $this->getFarmerForUser($user);

        if (!$farmer) {
            return view('dashboard.farmer.seeds-received', [
                'stats' => [
                    'total_deliveries' => 0,
                    'total_quantity' => 0,
                    'pending_deliveries' => 0,
                    'total_value' => 0,
                ],
                'distributions' => collect([]),
                'upcomingDeliveries' => collect([]),
                'farmer' => null,
            ]);
        }

        // Get seed distributions (filter by seed-related stock items)
        $seedDistributions = StockDistribution::where('farmer_id', $farmer->id)
            ->whereHas('transaction.stock_item', function ($query) {
                $query->where('name', 'like', '%seed%')
                    ->orWhere('name', 'like', '%cotton%')
                    ->orWhere('name', 'like', '%sesame%');
            })
            ->with(['transaction.stock_item', 'farm', 'season'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get all seed distributions for stats
        $allSeedDistributions = StockDistribution::where('farmer_id', $farmer->id)
            ->whereHas('transaction.stock_item', function ($query) {
                $query->where('name', 'like', '%seed%')
                    ->orWhere('name', 'like', '%cotton%')
                    ->orWhere('name', 'like', '%sesame%');
            });

        $stats = [
            'total_deliveries' => (clone $allSeedDistributions)->count(),
            'total_quantity' => (clone $allSeedDistributions)->sum('quantity'),
            'pending_deliveries' => (clone $allSeedDistributions)->where('distribution_type', 'credit')->unrepaid()->count(),
            'total_value' => (clone $allSeedDistributions)->sum('value'),
        ];

        // Upcoming deliveries from stock requests
        $upcomingDeliveries = \App\Models\Stock\StockRequest::where('farmer_id', $farmer->id)
            ->whereIn('status', ['approved', 'submitted'])
            ->with('items.stockItem')
            ->orderBy('needed_by')
            ->take(5)
            ->get();

        return view('dashboard.farmer.seeds-received', compact('stats', 'distributions', 'upcomingDeliveries', 'farmer'))
            ->with('distributions', $seedDistributions);
    }

    /**
     * Get the farmer record for the current user
     */
    private function getFarmerForUser($user): ?Farmer
    {
        // First check if user has farmer role and try to find linked farmer
        if ($user->isFarmer()) {
            // Try to find farmer by phone or email
            return Farmer::where('phone', $user->phone)
                ->orWhere('email', $user->email)
                ->first();
        }

        return null;
    }

    /**
     * Get recent activity for the farmer
     */
    private function getRecentActivity(Farmer $farmer): \Illuminate\Support\Collection
    {
        $activities = collect();

        // Get recent activity logs
        $activityLogs = ActivityLog::where('farmer_id', $farmer->id)
            ->orderBy('created_at', 'desc')
            ->take(3)
            ->get()
            ->map(function ($log) {
                return [
                    'title' => ucfirst($log->activity_type) . ' Activity',
                    'description' => $log->description ?? 'Activity recorded',
                    'time' => $log->created_at,
                    'icon' => 'fas fa-clipboard-list',
                    'color' => 'primary',
                ];
            });

        // Get recent farm visits
        $farmVisits = FarmVisit::where('farmer_id', $farmer->id)
            ->orderBy('created_at', 'desc')
            ->take(2)
            ->get()
            ->map(function ($visit) {
                return [
                    'title' => 'Farm Visit - ' . $visit->purpose_label,
                    'description' => $visit->notes ?? 'Visit scheduled',
                    'time' => $visit->scheduled_date ?? $visit->created_at,
                    'icon' => 'fas fa-calendar-check',
                    'color' => 'success',
                ];
            });

        // Get recent training attendance
        $trainings = TrainingAttendance::where('farmer_id', $farmer->id)
            ->with('session.program')
            ->orderBy('created_at', 'desc')
            ->take(2)
            ->get()
            ->map(function ($attendance) {
                return [
                    'title' => 'Training: ' . ($attendance->session?->program?->name ?? 'Training Session'),
                    'description' => $attendance->attended ? 'Attended' : 'Registered',
                    'time' => $attendance->check_in_time ?? $attendance->created_at,
                    'icon' => 'fas fa-chalkboard-teacher',
                    'color' => 'info',
                ];
            });

        // Get recent stock distributions
        $distributions = StockDistribution::where('farmer_id', $farmer->id)
            ->with('stockItem')
            ->orderBy('distribution_date', 'desc')
            ->take(2)
            ->get()
            ->map(function ($dist) {
                return [
                    'title' => 'Received: ' . ($dist->stockItem?->name ?? 'Items'),
                    'description' => 'Quantity: ' . $dist->quantity,
                    'time' => $dist->distribution_date ?? $dist->created_at,
                    'icon' => 'fas fa-box',
                    'color' => 'warning',
                ];
            });

        // Merge and sort by time
        return $activities
            ->merge($activityLogs)
            ->merge($farmVisits)
            ->merge($trainings)
            ->merge($distributions)
            ->sortByDesc('time')
            ->take(5)
            ->values();
    }

    /**
     * Get upcoming events for the farmer
     */
    private function getUpcomingEvents(Farmer $farmer): \Illuminate\Support\Collection
    {
        $events = collect();

        // Upcoming farm visits
        $upcomingVisits = FarmVisit::where('farmer_id', $farmer->id)
            ->scheduled()
            ->where('scheduled_date', '>=', now())
            ->orderBy('scheduled_date')
            ->take(3)
            ->get()
            ->map(function ($visit) {
                return [
                    'title' => 'Farm Visit - ' . $visit->purpose_label,
                    'date' => $visit->scheduled_date,
                    'type' => 'visit',
                    'color' => 'success',
                ];
            });

        // Upcoming training sessions (check if farmer is registered or general trainings)
        $upcomingTrainings = TrainingSession::where('status', 'scheduled')
            ->where('scheduled_date', '>=', now())
            ->orderBy('scheduled_date')
            ->take(3)
            ->get()
            ->map(function ($session) {
                return [
                    'title' => $session->program?->name ?? 'Training Session',
                    'date' => $session->scheduled_date,
                    'type' => 'training',
                    'color' => 'info',
                ];
            });

        // Upcoming tasks
        $upcomingTasks = Task::forFarmer($farmer->id)
            ->active()
            ->whereNotNull('planned_start_date')
            ->where('planned_start_date', '>=', now())
            ->orderBy('planned_start_date')
            ->take(3)
            ->get()
            ->map(function ($task) {
                return [
                    'title' => $task->title,
                    'date' => $task->planned_start_date,
                    'type' => 'task',
                    'color' => 'warning',
                ];
            });

        return $events
            ->merge($upcomingVisits)
            ->merge($upcomingTrainings)
            ->merge($upcomingTasks)
            ->sortBy('date')
            ->take(5)
            ->values();
    }
}
