<div class="modal fade" id="bulkExportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Bulk Data Export</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('export.bulk') }}" class="export-form" target="_blank">
                @csrf
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        This will export all ICS data into a single archive file. Large exports may take several minutes to generate.
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="export_format" class="form-label">Export Format *</label>
                            <select class="form-select" id="export_format" name="export_format" required>
                                <option value="">Select Format</option>
                                <option value="zip">ZIP Archive (Multiple Files)</option>
                                <option value="json">JSON (Single File)</option>
                                <option value="sql">SQL Dump</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="compression" class="form-label">Compression Level</label>
                            <select class="form-select" id="compression" name="compression">
                                <option value="normal">Normal</option>
                                <option value="high">High (Smaller file)</option>
                                <option value="none">No Compression</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <h6 class="border-bottom pb-2 mb-3">Select Data to Include</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="include_inspections" name="include[]" value="inspections" checked>
                                        <label class="form-check-label" for="include_inspections">
                                            <i class="fas fa-clipboard-list me-2"></i> Inspections
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="include_findings" name="include[]" value="findings" checked>
                                        <label class="form-check-label" for="include_findings">
                                            <i class="fas fa-exclamation-triangle me-2"></i> Findings
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="include_actions" name="include[]" value="corrective_actions" checked>
                                        <label class="form-check-label" for="include_actions">
                                            <i class="fas fa-tools me-2"></i> Corrective Actions
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="include_certifications" name="include[]" value="certifications" checked>
                                        <label class="form-check-label" for="include_certifications">
                                            <i class="fas fa-certificate me-2"></i> Certifications
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="include_checklists" name="include[]" value="checklists">
                                        <label class="form-check-label" for="include_checklists">
                                            <i class="fas fa-tasks me-2"></i> Checklists
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="include_standards" name="include[]" value="compliance_standards">
                                        <label class="form-check-label" for="include_standards">
                                            <i class="fas fa-gavel me-2"></i> Compliance Standards
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="include_farmers" name="include[]" value="farmers">
                                        <label class="form-check-label" for="include_farmers">
                                            <i class="fas fa-user-tie me-2"></i> Farmers
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="include_users" name="include[]" value="users">
                                        <label class="form-check-label" for="include_users">
                                            <i class="fas fa-users me-2"></i> Users
                                        </label>
                                    </div>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="include_metadata" name="include[]" value="metadata" checked>
                                        <label class="form-check-label" for="include_metadata">
                                            <i class="fas fa-database me-2"></i> Metadata
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="date_range" class="form-label">Date Range</label>
                            <select class="form-select" id="date_range" name="date_range">
                                <option value="all">All Time</option>
                                <option value="this_year">This Year</option>
                                <option value="last_year">Last Year</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="record_limit" class="form-label">Record Limit</label>
                            <select class="form-select" id="record_limit" name="record_limit">
                                <option value="0">No Limit (All Records)</option>
                                <option value="1000">1,000 Records per Table</option>
                                <option value="5000">5,000 Records per Table</option>
                                <option value="10000">10,000 Records per Table</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="include_attachments" name="include_attachments" value="1">
                                <label class="form-check-label" for="include_attachments">
                                    Include File Attachments (Evidence, Documents)
                                </label>
                                <small class="text-muted d-block">This will significantly increase file size.</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-download me-2"></i> Generate Bulk Export
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const exportFormat = document.getElementById('export_format');
        const compression = document.getElementById('compression');
        const includeAttachments = document.getElementById('include_attachments');
        
        // Disable compression for non-ZIP formats
        exportFormat.addEventListener('change', function() {
            if (this.value === 'zip') {
                compression.disabled = false;
            } else {
                compression.disabled = true;
            }
        });
        
        // Warn about large attachments
        includeAttachments.addEventListener('change', function() {
            if (this.checked) {
                alert('Warning: Including attachments may create a very large export file and take longer to generate.');
            }
        });
    });
</script>