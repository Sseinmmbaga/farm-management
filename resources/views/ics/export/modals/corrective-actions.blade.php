<div class="modal fade" id="exportCorrectiveActionsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Corrective Actions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="GET" action="{{ route('corrective-actions.index') }}" class="export-form" target="_blank">
                <input type="hidden" name="export" value="corrective_actions">
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
                            <label for="status" class="form-label">Status Filter</label>
                            <select class="form-select" id="status" name="status">
                                <option value="">All Statuses</option>
                                <option value="planned">Planned</option>
                                <option value="in_progress">In Progress</option>
                                <option value="completed">Completed</option>
                                <option value="verified">Verified</option>
                                <option value="ineffective">Ineffective</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="priority" class="form-label">Priority Filter</label>
                            <select class="form-select" id="priority" name="priority">
                                <option value="">All Priorities</option>
                                <option value="critical">Critical</option>
                                <option value="high">High</option>
                                <option value="medium">Medium</option>
                                <option value="low">Low</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="responsible_person" class="form-label">Responsible Person</label>
                            <select class="form-select" id="responsible_person" name="responsible_person">
                                <option value="">All Persons</option>
                                <option value="1">John Doe</option>
                                <option value="2">Jane Smith</option>
                                <option value="3">Robert Johnson</option>
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
                            <label for="due_date_range" class="form-label">Due Date Range</label>
                            <select class="form-select" id="due_date_range" name="due_date_range">
                                <option value="">Any Due Date</option>
                                <option value="overdue">Overdue</option>
                                <option value="due_this_week">Due This Week</option>
                                <option value="due_next_week">Due Next Week</option>
                                <option value="due_this_month">Due This Month</option>
                                <option value="future">Future</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="include_finding_details" name="include_finding_details" value="1" checked>
                                <label class="form-check-label" for="include_finding_details">
                                    Include Finding Details
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="include_timeline" name="include_timeline" value="1">
                                <label class="form-check-label" for="include_timeline">
                                    Include Timeline & Updates
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="include_verification" name="include_verification" value="1">
                                <label class="form-check-label" for="include_verification">
                                    Include Verification Details
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
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

        if (dateRangeSelect) {
            dateRangeSelect.addEventListener('change', toggleCustomDates);
            toggleCustomDates();
        }
    });
</script>