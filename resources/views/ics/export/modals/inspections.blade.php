<div class="modal fade" id="exportInspectionsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Inspections</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="GET" action="{{ route('ics-reports.summary') }}" class="export-form" target="_blank">
                <input type="hidden" name="export" value="inspections">
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
                        <div class="col-md-6">
                            <label for="status" class="form-label">Inspection Status</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Statuses</option>
                                <option value="scheduled">Scheduled</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="inspector" class="form-label">Inspector</label>
                            <select class="form-select" id="inspector" name="inspector">
                                <option value="">All Inspectors</option>
                                <option value="1">John Doe</option>
                                <option value="2">Jane Smith</option>
                                <option value="3">Robert Johnson</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="include_responses" name="include_responses" value="1">
                                <label class="form-check-label" for="include_responses">
                                    Include Checklist Responses
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="include_findings" name="include_findings" value="1">
                                <label class="form-check-label" for="include_findings">
                                    Include Associated Findings
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="include_signatures" name="include_signatures" value="1">
                                <label class="form-check-label" for="include_signatures">
                                    Include Digital Signatures
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
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
        toggleCustomDates(); // Initial call
    });
</script>