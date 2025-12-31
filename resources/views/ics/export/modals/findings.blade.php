<div class="modal fade" id="exportFindingsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Findings</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="GET" action="{{ route('ics-reports.findings') }}" class="export-form" target="_blank">
                <input type="hidden" name="export" value="findings">
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="format" class="form-label">Export Format *</label>
                            <select class="form-select" id="format" name="format" required>
                                <option value="">Select Format</option>
                                <option value="pdf">PDF Document</option>
                                <option value="excel">Excel (XLSX)</option>
                                <option value="csv">CSV (Comma Separated)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="severity" class="form-label">Severity Filter</label>
                            <select class="form-select" id="severity" name="severity">
                                <option value="">All Severities</option>
                                <option value="critical">Critical</option>
                                <option value="major">Major</option>
                                <option value="minor">Minor</option>
                                <option value="observation">Observation</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="status" class="form-label">Status Filter</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Statuses</option>
                                <option value="open">Open</option>
                                <option value="in_progress">In Progress</option>
                                <option value="resolved">Resolved</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="date_range" class="form-label">Date Range</label>
                            <select class="form-select" id="date_range" name="date_range">
                                <option value="all">All Time</option>
                                <option value="this_month">This Month</option>
                                <option value="last_month">Last Month</option>
                                <option value="this_quarter">This Quarter</option>
                                <option value="this_year">This Year</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="from_date" class="form-label">From Date</label>
                            <input type="date" class="form-control" id="from_date" name="from_date">
                        </div>
                        <div class="col-md-6">
                            <label for="to_date" class="form-label">To Date</label>
                            <input type="date" class="form-control" id="to_date" name="to_date">
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="include_corrective_actions" name="include_corrective_actions" value="1" checked>
                                <label class="form-check-label" for="include_corrective_actions">
                                    Include Corrective Actions
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="include_evidence" name="include_evidence" value="1">
                                <label class="form-check-label" for="include_evidence">
                                    Include Evidence Details
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="include_comments" name="include_comments" value="1">
                                <label class="form-check-label" for="include_comments">
                                    Include Comments & Notes
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="fas fa-download me-2"></i> Generate Export
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const dateRangeSelect = document.getElementById('date_range');
        const fromDateInput = document.getElementById('from_date');
        const toDateInput = document.getElementById('to_date');

        function toggleCustomDates() {
            if (dateRangeSelect.value === 'custom') {
                fromDateInput.disabled = false;
                toDateInput.disabled = false;
            } else {
                fromDateInput.disabled = true;
                toDateInput.disabled = true;
                fromDateInput.value = '';
                toDateInput.value = '';
            }
        }

        dateRangeSelect.addEventListener('change', toggleCustomDates);
        toggleCustomDates();
    });
</script>