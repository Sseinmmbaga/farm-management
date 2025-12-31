<?php

namespace App\Http\Controllers;

use App\Models\PerformanceReport;
use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;

class PerformanceReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $this->authorize('viewAny', PerformanceReport::class);

        $query = PerformanceReport::with(['user', 'department', 'reviewer', 'approver'])
            ->latest();

        // Filters
        if ($request->has('status') && $request->status) {
            $query->status($request->status);
        }

        if ($request->has('user_id') && $request->user_id) {
            $query->forUser($request->user_id);
        }

        if ($request->has('department_id') && $request->department_id) {
            $query->forDepartment($request->department_id);
        }

        if ($request->has('period_year') && $request->period_year) {
            $query->forPeriod($request->period_year, $request->period_month);
        }

        // If user is not admin, limit to own reports or department reports
        if (!Gate::allows('manage_performance_reports')) {
            $user = Auth::user();
            if (Gate::allows('review_performance_reports')) {
                // Reviewer can see reports from their department or those they review
                $query->where(function ($q) use ($user) {
                    $q->where('department_id', $user->department_id)
                        ->orWhere('reviewer_id', $user->id);
                });
            } else {
                // Regular user can only see their own reports
                $query->forUser($user);
            }
        }

        $reports = $query->paginate(20);

        $users = User::orderBy('name')->pluck('name', 'id');
        $departments = Department::orderBy('name')->pluck('name', 'id');
        $statuses = [
            PerformanceReport::STATUS_DRAFT => 'Draft',
            PerformanceReport::STATUS_SUBMITTED => 'Submitted',
            PerformanceReport::STATUS_REVIEWED => 'Reviewed',
            PerformanceReport::STATUS_APPROVED => 'Approved',
            PerformanceReport::STATUS_REJECTED => 'Rejected',
        ];
        $periodTypes = [
            PerformanceReport::PERIOD_MONTHLY => 'Monthly',
            PerformanceReport::PERIOD_QUARTERLY => 'Quarterly',
            PerformanceReport::PERIOD_YEARLY => 'Yearly',
        ];

        return view('performance-reports.index', compact(
            'reports',
            'users',
            'departments',
            'statuses',
            'periodTypes'
        ));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $this->authorize('create', PerformanceReport::class);

        $users = User::orderBy('name')->pluck('name', 'id');
        $departments = Department::orderBy('name')->pluck('name', 'id');
        $periodTypes = [
            PerformanceReport::PERIOD_MONTHLY => 'Monthly',
            PerformanceReport::PERIOD_QUARTERLY => 'Quarterly',
            PerformanceReport::PERIOD_YEARLY => 'Yearly',
        ];
        $currentMonth = date('n');
        $currentYear = date('Y');

        return view('performance-reports.create', compact(
            'users',
            'departments',
            'periodTypes',
            'currentMonth',
            'currentYear'
        ));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $this->authorize('create', PerformanceReport::class);

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'period_type' => ['required', Rule::in([
                PerformanceReport::PERIOD_MONTHLY,
                PerformanceReport::PERIOD_QUARTERLY,
                PerformanceReport::PERIOD_YEARLY,
            ])],
            'period_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'period_year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'report_date' => ['required', 'date'],
            'summary' => ['required', 'string', 'max:2000'],
            'strengths' => ['nullable', 'string', 'max:2000'],
            'improvements' => ['nullable', 'string', 'max:2000'],
            'recommendations' => ['nullable', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        // Auto‑generate metrics if not provided (for demo)
        $metrics = [
            'productivity' => ['score' => rand(60, 100), 'comments' => ''],
            'quality' => ['score' => rand(60, 100), 'comments' => ''],
            'timeliness' => ['score' => rand(60, 100), 'comments' => ''],
            'collaboration' => ['score' => rand(60, 100), 'comments' => ''],
            'innovation' => ['score' => rand(60, 100), 'comments' => ''],
        ];

        $validated['metrics'] = $metrics;
        $validated['total_score'] = (new PerformanceReport())->calculateTotalScore($metrics);
        $validated['status'] = PerformanceReport::STATUS_DRAFT;

        $report = PerformanceReport::create($validated);

        return redirect()
            ->route('performance-reports.show', $report)
            ->with('success', 'Performance report created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\PerformanceReport  $performanceReport
     * @return \Illuminate\View\View
     */
    public function show(PerformanceReport $performanceReport)
    {
        $this->authorize('view', $performanceReport);

        $performanceReport->load(['user', 'department', 'reviewer', 'approver']);

        return view('performance-reports.show', compact('performanceReport'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PerformanceReport  $performanceReport
     * @return \Illuminate\View\View
     */
    public function edit(PerformanceReport $performanceReport)
    {
        $this->authorize('update', $performanceReport);

        if (!$performanceReport->isEditable()) {
            abort(403, 'This report cannot be edited.');
        }

        $users = User::orderBy('name')->pluck('name', 'id');
        $departments = Department::orderBy('name')->pluck('name', 'id');
        $periodTypes = [
            PerformanceReport::PERIOD_MONTHLY => 'Monthly',
            PerformanceReport::PERIOD_QUARTERLY => 'Quarterly',
            PerformanceReport::PERIOD_YEARLY => 'Yearly',
        ];

        return view('performance-reports.edit', compact(
            'performanceReport',
            'users',
            'departments',
            'periodTypes'
        ));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PerformanceReport  $performanceReport
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, PerformanceReport $performanceReport)
    {
        $this->authorize('update', $performanceReport);

        if (!$performanceReport->isEditable()) {
            abort(403, 'This report cannot be edited.');
        }

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'department_id' => ['required', 'exists:departments,id'],
            'period_type' => ['required', Rule::in([
                PerformanceReport::PERIOD_MONTHLY,
                PerformanceReport::PERIOD_QUARTERLY,
                PerformanceReport::PERIOD_YEARLY,
            ])],
            'period_month' => ['nullable', 'integer', 'min:1', 'max:12'],
            'period_year' => ['required', 'integer', 'min:2020', 'max:2100'],
            'report_date' => ['required', 'date'],
            'summary' => ['required', 'string', 'max:2000'],
            'strengths' => ['nullable', 'string', 'max:2000'],
            'improvements' => ['nullable', 'string', 'max:2000'],
            'recommendations' => ['nullable', 'string', 'max:2000'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        // If metrics are provided, validate them
        if ($request->has('metrics')) {
            $metricRules = [];
            foreach ($request->metrics as $key => $metric) {
                $metricRules["metrics.{$key}.score"] = ['nullable', 'numeric', 'min:0', 'max:100'];
                $metricRules["metrics.{$key}.comments"] = ['nullable', 'string', 'max:500'];
            }
            $metricValidated = $request->validate($metricRules);
            $validated['metrics'] = $metricValidated['metrics'];
            $validated['total_score'] = (new PerformanceReport())->calculateTotalScore($validated['metrics']);
        }

        $performanceReport->update($validated);

        return redirect()
            ->route('performance-reports.show', $performanceReport)
            ->with('success', 'Performance report updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PerformanceReport  $performanceReport
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(PerformanceReport $performanceReport)
    {
        $this->authorize('delete', $performanceReport);

        if (!$performanceReport->isDraft()) {
            abort(403, 'Only draft reports can be deleted.');
        }

        $performanceReport->delete();

        return redirect()
            ->route('performance-reports.index')
            ->with('success', 'Performance report deleted successfully.');
    }

    /**
     * Submit a draft report for review.
     *
     * @param  \App\Models\PerformanceReport  $performanceReport
     * @return \Illuminate\Http\RedirectResponse
     */
    public function submit(PerformanceReport $performanceReport)
    {
        $this->authorize('submit', $performanceReport);

        if (!$performanceReport->canBeSubmitted()) {
            abort(403, 'This report cannot be submitted.');
        }

        $performanceReport->update([
            'status' => PerformanceReport::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ]);

        // TODO: Notify reviewer

        return redirect()
            ->route('performance-reports.show', $performanceReport)
            ->with('success', 'Performance report submitted for review.');
    }

    /**
     * Review a submitted report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PerformanceReport  $performanceReport
     * @return \Illuminate\Http\RedirectResponse
     */
    public function review(Request $request, PerformanceReport $performanceReport)
    {
        $this->authorize('review', $performanceReport);

        if (!$performanceReport->canBeReviewed()) {
            abort(403, 'This report cannot be reviewed at this stage.');
        }

        $validated = $request->validate([
            'review_notes' => ['required', 'string', 'max:2000'],
            'total_score' => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $performanceReport->update([
            'status' => PerformanceReport::STATUS_REVIEWED,
            'reviewer_id' => Auth::id(),
            'reviewed_at' => now(),
            'review_notes' => $validated['review_notes'],
            'total_score' => $validated['total_score'] ?? $performanceReport->calculateTotalScore(),
        ]);

        // TODO: Notify approver

        return redirect()
            ->route('performance-reports.show', $performanceReport)
            ->with('success', 'Performance report reviewed successfully.');
    }

    /**
     * Approve a reviewed report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PerformanceReport  $performanceReport
     * @return \Illuminate\Http\RedirectResponse
     */
    public function approve(Request $request, PerformanceReport $performanceReport)
    {
        $this->authorize('approve', $performanceReport);

        if (!$performanceReport->canBeApproved()) {
            abort(403, 'This report cannot be approved at this stage.');
        }

        $validated = $request->validate([
            'approval_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        $performanceReport->update([
            'status' => PerformanceReport::STATUS_APPROVED,
            'approved_by' => Auth::id(),
            'approved_at' => now(),
            'approval_notes' => $validated['approval_notes'],
        ]);

        // TODO: Notify user

        return redirect()
            ->route('performance-reports.show', $performanceReport)
            ->with('success', 'Performance report approved successfully.');
    }

    /**
     * Reject a report.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PerformanceReport  $performanceReport
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reject(Request $request, PerformanceReport $performanceReport)
    {
        $this->authorize('reject', $performanceReport);

        if (!$performanceReport->canBeRejected()) {
            abort(403, 'This report cannot be rejected at this stage.');
        }

        $validated = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:2000'],
        ]);

        $performanceReport->update([
            'status' => PerformanceReport::STATUS_REJECTED,
            'rejection_reason' => $validated['rejection_reason'],
        ]);

        // TODO: Notify user

        return redirect()
            ->route('performance-reports.show', $performanceReport)
            ->with('success', 'Performance report rejected.');
    }

    /**
     * Print/export a report.
     *
     * @param  \App\Models\PerformanceReport  $performanceReport
     * @return \Illuminate\View\View
     */
    public function print(PerformanceReport $performanceReport)
    {
        $this->authorize('view', $performanceReport);

        $performanceReport->load(['user', 'department', 'reviewer', 'approver']);

        return view('performance-reports.print', compact('performanceReport'));
    }
}