<div class="modal fade" id="exportCertificationsModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Export Certifications</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="GET" action="{{ route('certifications.index') }}" class="export-form" target="_blank">
                <input type="hidden" name="export" value="certifications">
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
                                <option value="certified">Certified</option>
                                <option value="in-conversion">In Conversion</option>
                                <option value="pending">Pending</option>
                                <option value="suspended">Suspended</option>
                                <option value="revoked">Revoked</option>
                                <option value="expired">Expired</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="standard" class="form-label">Compliance Standard</label>
                            <select class="form-select" id="standard" name="standard">
                                <option value="">All Standards</option>
                                <option value="ics">Internal Control System (ICS)</option>
                                <option value="globalgap">GlobalG.A.P.</option>
                                <option value="organic">Organic Certification</option>
                                <option value="fairtrade">Fair Trade</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="expiry_status" class="form-label">Expiry Status</label>
                            <select class="form-select" id="expiry_status" name="expiry_status">
                                <option value="">Any Expiry</option>
                                <option value="expiring_soon">Expiring Soon (30 days)</option>
                                <option value="expired">Expired</option>
                                <option value="valid">Valid (Not expired)</option>
                                <option value="no_expiry">No Expiry Date</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="date_range" class="form-label">Certification Date Range</label>
                            <select class="form-select" id="date_range" name="date_range">
                                <option value="all">All Time</option>
                                <option value="this_year">This Year</option>
                                <option value="last_year">Last Year</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="region" class="form-label">Region</label>
                            <select class="form-select" id="region" name="region">
                                <option value="">All Regions</option>
                                <option value="north">Northern Zone</option>
                                <option value="south">Southern Zone</option>
                                <option value="east">Eastern Zone</option>
                                <option value="west">Western Zone</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="include_inspection_history" name="include_inspection_history" value="1" checked>
                                <label class="form-check-label" for="include_inspection_history">
                                    Include Inspection History
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="include_farmer_details" name="include_farmer_details" value="1">
                                <label class="form-check-label" for="include_farmer_details">
                                    Include Farmer Details
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="include_notes" name="include_notes" value="1">
                                <label class="form-check-label" for="include_notes">
                                    Include Notes & Comments
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">
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