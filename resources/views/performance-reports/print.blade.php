<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Report - {{ $performanceReport->report_number }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 12pt;
            line-height: 1.5;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 15px;
            margin-bottom: 25px;
        }
        .header h1 {
            margin: 0;
            color: #007bff;
        }
        .header .subtitle {
            font-size: 14pt;
            color: #666;
        }
        .section {
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        .section-title {
            font-size: 14pt;
            font-weight: bold;
            color: #007bff;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
            margin-bottom: 10px;
        }
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
        }
        .info-item {
            margin-bottom: 10px;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        .metrics-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        .metrics-table th, .metrics-table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        .metrics-table th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        .score-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: bold;
            background-color: #007bff;
            color: white;
        }
        .text-right {
            text-align: right;
        }
        .footer {
            margin-top: 40px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            font-size: 10pt;
            color: #777;
            text-align: center;
        }
        @media print {
            body {
                padding: 0;
            }
            .no-print {
                display: none;
            }
            .section {
                page-break-inside: avoid;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <h1>PERFORMANCE REPORT</h1>
        <div class="subtitle">{{ $performanceReport->report_number }}</div>
        <div class="subtitle">Generated on {{ now()->format('d/m/Y H:i') }}</div>
    </div>

    <!-- Basic Information -->
    <div class="section">
        <div class="section-title">Report Details</div>
        <div class="info-grid">
            <div class="info-item">
                <span class="info-label">Employee:</span>
                {{ $performanceReport->user->name ?? 'N/A' }}
            </div>
            <div class="info-item">
                <span class="info-label">Department:</span>
                {{ $performanceReport->department->name ?? 'N/A' }}
            </div>
            <div class="info-item">
                <span class="info-label">Period:</span>
                {{ $performanceReport->period_display }}
            </div>
            <div class="info-item">
                <span class="info-label">Report Date:</span>
                {{ $performanceReport->report_date->format('d/m/Y') }}
            </div>
            <div class="info-item">
                <span class="info-label">Status:</span>
                <span class="score-badge">{{ $performanceReport->status_display }}</span>
            </div>
            <div class="info-item">
                <span class="info-label">Overall Score:</span>
                @if($performanceReport->total_score)
                    <strong>{{ $performanceReport->total_score }}</strong> / 100
                @else
                    N/A
                @endif
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="section">
        <div class="section-title">Performance Metrics</div>
        <table class="metrics-table">
            <thead>
                <tr>
                    <th>Metric</th>
                    <th>Score</th>
                    <th>Comments</th>
                </tr>
            </thead>
            <tbody>
                @foreach($performanceReport->getMetricsWithLabels() as $metric)
                    <tr>
                        <td>{{ $metric['label'] }}</td>
                        <td>
                            @if($metric['score'] !== null)
                                {{ $metric['score'] }} / 100
                            @else
                                N/A
                            @endif
                        </td>
                        <td>{{ $metric['comments'] ?? '-' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Summary -->
    <div class="section">
        <div class="section-title">Summary</div>
        <p>{{ $performanceReport->summary }}</p>
    </div>

    <!-- Strengths -->
    <div class="section">
        <div class="section-title">Strengths</div>
        <p>{{ $performanceReport->strengths ?: 'Not specified.' }}</p>
    </div>

    <!-- Areas for Improvement -->
    <div class="section">
        <div class="section-title">Areas for Improvement</div>
        <p>{{ $performanceReport->improvements ?: 'Not specified.' }}</p>
    </div>

    <!-- Recommendations -->
    <div class="section">
        <div class="section-title">Recommendations</div>
        <p>{{ $performanceReport->recommendations ?: 'Not specified.' }}</p>
    </div>

    <!-- Review & Approval -->
    @if($performanceReport->isSubmitted() || $performanceReport->isReviewed() || $performanceReport->isApproved() || $performanceReport->isRejected())
        <div class="section">
            <div class="section-title">Review & Approval</div>
            <div class="info-grid">
                @if($performanceReport->reviewer)
                    <div class="info-item">
                        <span class="info-label">Reviewed By:</span>
                        {{ $performanceReport->reviewer->name }}
                        @if($performanceReport->reviewed_at)
                            ({{ $performanceReport->reviewed_at->format('d/m/Y H:i') }})
                        @endif
                    </div>
                @endif
                @if($performanceReport->approver)
                    <div class="info-item">
                        <span class="info-label">Approved By:</span>
                        {{ $performanceReport->approver->name }}
                        @if($performanceReport->approved_at)
                            ({{ $performanceReport->approved_at->format('d/m/Y H:i') }})
                        @endif
                    </div>
                @endif
            </div>
            @if($performanceReport->review_notes)
                <div class="info-item">
                    <span class="info-label">Review Notes:</span>
                    <p>{{ $performanceReport->review_notes }}</p>
                </div>
            @endif
            @if($performanceReport->approval_notes)
                <div class="info-item">
                    <span class="info-label">Approval Notes:</span>
                    <p>{{ $performanceReport->approval_notes }}</p>
                </div>
            @endif
            @if($performanceReport->isRejected() && $performanceReport->rejection_reason)
                <div class="info-item">
                    <span class="info-label">Rejection Reason:</span>
                    <p>{{ $performanceReport->rejection_reason }}</p>
                </div>
            @endif
        </div>
    @endif

    <!-- Additional Notes -->
    @if($performanceReport->notes)
        <div class="section">
            <div class="section-title">Additional Notes</div>
            <p>{{ $performanceReport->notes }}</p>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>This document was generated automatically by the Remei Farm OS.</p>
        <p>Confidential – For internal use only.</p>
    </div>

    <!-- Print button (hidden when printing) -->
    <div class="no-print" style="text-align: center; margin-top: 30px;">
        <button onclick="window.print()" class="btn btn-primary">Print this report</button>
        <button onclick="window.close()" class="btn btn-outline-secondary">Close</button>
    </div>

    <script>
        // Auto‑print option (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>