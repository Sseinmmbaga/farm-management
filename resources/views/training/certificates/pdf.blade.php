<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Certificate - {{ $certificate->certificate_number }}</title>
    <style>
        /* PDF-specific styles */
        @page {
            margin: 0;
            size: A4 landscape;
        }
        body {
            font-family: 'Helvetica', 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            background-color: #fff;
        }
        .certificate-container {
            width: 29.7cm; /* A4 landscape width */
            height: 21cm; /* A4 landscape height */
            position: relative;
            border: 2px solid #ddd;
            margin: 0 auto;
            padding: 2cm;
            box-sizing: border-box;
            background: #fff;
            background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" width="400" height="400" opacity="0.05"><path d="M200,0 L400,200 L200,400 L0,200 Z" fill="%23007b33"/></svg>');
            background-repeat: repeat;
            background-size: 80px;
        }
        .certificate-header {
            text-align: center;
            margin-bottom: 2cm;
        }
        .logo {
            max-width: 120px;
            margin: 0 auto 1cm;
        }
        .certificate-title {
            font-size: 36px;
            font-weight: bold;
            color: #007b33;
            text-transform: uppercase;
            letter-spacing: 2px;
            margin-bottom: 0.5cm;
        }
        .certificate-subtitle {
            font-size: 18px;
            color: #555;
            margin-bottom: 1.5cm;
        }
        .certificate-body {
            text-align: center;
            margin-bottom: 2cm;
        }
        .certificate-label {
            font-size: 20px;
            color: #666;
            margin-bottom: 0.5cm;
        }
        .farmer-name {
            font-size: 42px;
            font-weight: bold;
            color: #000;
            margin: 0.5cm 0;
            padding: 0.2cm 0;
            border-bottom: 3px solid #007b33;
            display: inline-block;
        }
        .program-name {
            font-size: 28px;
            color: #007b33;
            margin: 0.5cm 0;
            font-weight: bold;
        }
        .session-details {
            font-size: 18px;
            color: #555;
            margin-bottom: 1cm;
        }
        .certificate-details {
            display: flex;
            justify-content: space-between;
            margin-top: 2cm;
            border-top: 2px solid #ccc;
            padding-top: 1cm;
        }
        .detail-column {
            flex: 1;
            text-align: center;
        }
        .detail-label {
            font-size: 16px;
            color: #666;
            margin-bottom: 0.3cm;
        }
        .detail-value {
            font-size: 18px;
            font-weight: bold;
            color: #333;
        }
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 2cm;
            padding-top: 1cm;
            border-top: 2px solid #ccc;
        }
        .signature {
            flex: 1;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #000;
            width: 80%;
            margin: 0 auto 0.5cm;
            padding-top: 0.5cm;
        }
        .signature-name {
            font-size: 16px;
            font-weight: bold;
        }
        .signature-title {
            font-size: 14px;
            color: #666;
        }
        .footer {
            margin-top: 1cm;
            text-align: center;
            font-size: 12px;
            color: #999;
        }
        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 120px;
            color: rgba(0, 123, 51, 0.1);
            font-weight: bold;
            z-index: -1;
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <div class="certificate-container">
        <!-- Watermark -->
        <div class="watermark">REMEI FARM OS</div>

        <!-- Header -->
        <div class="certificate-header">
            <div class="certificate-title">Certificate of Completion</div>
            <div class="certificate-subtitle">This certifies that the following farmer has successfully completed the training program</div>
        </div>

        <!-- Body -->
        <div class="certificate-body">
            <div class="certificate-label">Awarded to</div>
            <div class="farmer-name">{{ $certificate->farmer->first_name }} {{ $certificate->farmer->last_name }}</div>
            <div class="certificate-label">for completion of</div>
            <div class="program-name">{{ $certificate->program->name }}</div>
            <div class="session-details">
                Training Session: {{ $certificate->session->title }}<br>
                Date: {{ $certificate->session->scheduled_date->format('F j, Y') }}<br>
                Venue: {{ $certificate->session->venue }}
            </div>
        </div>

        <!-- Details -->
        <div class="certificate-details">
            <div class="detail-column">
                <div class="detail-label">Certificate Number</div>
                <div class="detail-value">{{ $certificate->certificate_number }}</div>
            </div>
            <div class="detail-column">
                <div class="detail-label">Issue Date</div>
                <div class="detail-value">{{ $certificate->issue_date->format('F j, Y') }}</div>
            </div>
            <div class="detail-column">
                <div class="detail-label">Expiry Date</div>
                <div class="detail-value">
                    @if($certificate->expiry_date)
                        {{ $certificate->expiry_date->format('F j, Y') }}
                    @else
                        No expiry
                    @endif
                </div>
            </div>
        </div>

        <!-- Signatures -->
        <div class="signature-section">
            <div class="signature">
                <div class="signature-line"></div>
                <div class="signature-name">{{ $certificate->issuedBy->name ?? 'Training Coordinator' }}</div>
                <div class="signature-title">Training Coordinator</div>
            </div>
            <div class="signature">
                <div class="signature-line"></div>
                <div class="signature-name">Quality Assurance Officer</div>
                <div class="signature-title">Quality Assurance</div>
            </div>
            <div class="signature">
                <div class="signature-line"></div>
                <div class="signature-name">Remei Farm OS</div>
                <div class="signature-title">Issued By</div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            This certificate is issued electronically and is valid without a signature.<br>
            Verification code: {{ $certificate->certificate_number }} | Issued on {{ $certificate->issue_date->format('Y-m-d') }}
        </div>
    </div>
</body>
</html>