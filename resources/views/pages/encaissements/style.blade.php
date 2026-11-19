<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;600;700&family=Source+Serif+4:opsz,wght@8..60,600;8..60,700&display=swap" rel="stylesheet">
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: "Source Sans 3", "Segoe UI", sans-serif;
        color: #172033;
        background: #dfe6f0;
        line-height: 1.5;
    }

    @media print {
        body {
            background: white;
            margin: 0;
            padding: 0;
        }

        .no-print {
            display: none !important;
        }

        .print-container {
            margin: 0;
            padding: 0 12px 8px;
            box-shadow: none;
            border: none;
        }

        .print-container::before {
            display: none;
        }

        .header,
        .header::before,
        .header::after,
        .header-receipt,
        .header-logo,
        .table thead {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .header {
            margin: 0 -12px 16px;
            box-shadow: none;
        }
    }

    .print-container {
        max-width: 900px;
        margin: 16px auto 40px;
        padding: 28px 32px 24px;
        background: #fff;
        border: none;
        box-shadow: 0 16px 40px rgba(12, 32, 64, 0.12);
    }

    .print-container::before {
        display: none;
    }

    .header {
        position: relative;
        overflow: hidden;
        color: #fff;
        padding: 16px 24px 14px;
        border-radius: 0;
        margin: 12px -32px 22px;
        display: flex;
        align-items: center;
        gap: 16px;
        background:
            radial-gradient(ellipse at 100% 0%, rgba(255, 255, 255, 0.16), transparent 42%),
            linear-gradient(118deg, #06182f 0%, #0a3268 46%, #14508f 100%);
        border-bottom: 4px solid #b42318;
        box-shadow: none;
    }

    .header::before {
        content: "";
        position: absolute;
        inset: 0;
        background: repeating-linear-gradient(-28deg, transparent, transparent 13px, rgba(255, 255, 255, 0.045) 13px, rgba(255, 255, 255, 0.045) 14px);
        pointer-events: none;
    }

    .header-watermark {
        position: absolute;
        right: 176px;
        top: 50%;
        width: 190px;
        height: 70px;
        transform: translateY(-50%);
        overflow: hidden;
        pointer-events: none;
        z-index: 0;
    }

    .header-watermark svg {
        width: 100%;
        height: 100%;
        display: block;
    }

    .header-logo,
    .header-info,
    .header-receipt {
        position: relative;
        z-index: 1;
    }

    .header-logo {
        flex-shrink: 0;
        width: 148px;
        height: 50px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #fff;
        border: none;
        border-radius: 0;
        overflow: hidden;
        padding: 3px 8px;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.18);
    }

    .header-logo img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .header-info {
        flex-grow: 1;
        border-right: none;
        padding-right: 0;
        min-width: 0;
    }

    .header-title {
        font-family: "Source Serif 4", Georgia, serif;
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 4px;
        text-transform: none;
        letter-spacing: -0.02em;
        text-shadow: none;
        color: #fff;
    }

    .header-subtitle {
        font-size: 12px;
        color: #f0c7c3;
        opacity: 1;
        margin-bottom: 6px;
        font-weight: 600;
    }

    .header-description {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.78);
        opacity: 1;
        margin-bottom: 6px;
        line-height: 1.45;
    }

    .header-contact {
        font-size: 11px;
        color: rgba(255, 255, 255, 0.84);
        opacity: 1;
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }

    .header-contact-item {
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .header-contact-item::before {
        content: "•";
        opacity: 0.6;
    }

    .header-receipt {
        flex: 0 0 auto;
        text-align: left;
        background: #fff;
        color: #08284f;
        padding: 7px 10px 8px;
        width: max-content;
        max-width: 156px;
        border-left: 3px solid #b42318;
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.16);
    }

    .header-receipt-no {
        font-size: 9px;
        letter-spacing: 0.12em;
        text-transform: uppercase;
        color: #b42318;
        opacity: 1;
        margin-bottom: 2px;
    }

    .header-receipt-id {
        font-family: "Source Serif 4", Georgia, serif;
        font-size: 14px;
        font-weight: 700;
        line-height: 1.2;
        background: transparent;
        color: #08284f;
        padding: 0;
        border-radius: 0;
        overflow-wrap: anywhere;
    }

    .receipt-date {
        font-size: 11px;
        color: #5d6b80;
        opacity: 1;
        margin-top: 2px;
    }

    .receipt-author {
        margin-top: 4px;
        font-size: 10px;
        line-height: 1.25;
        color: #5d6b80;
    }

    .document-title {
        text-align: center;
        font-family: "Source Serif 4", Georgia, serif;
        font-size: 18px;
        font-weight: 700;
        margin: 18px 0 22px;
        color: #08284f;
        border-bottom: 1px solid #b42318;
        border-top: none;
        padding: 0 0 8px;
        background: transparent;
        text-transform: uppercase;
        letter-spacing: 0.08em;
    }

    .info-section {
        margin-bottom: 25px;
    }

    .info-section-title {
        background: #f4f7fb;
        border-left: 3px solid #0c3d78;
        padding: 8px 12px;
        font-weight: 700;
        font-size: 11px;
        letter-spacing: 0.08em;
        margin-bottom: 12px;
        text-transform: uppercase;
        color: #08284f;
    }

    .info-row {
        display: flex;
        margin-bottom: 10px;
        border-bottom: 1px dotted #ddd;
        padding-bottom: 8px;
    }

    .info-label {
        font-weight: 600;
        width: 35%;
        color: #555;
        font-size: 13px;
    }

    .info-value {
        width: 65%;
        text-align: left;
        color: #333;
        font-size: 13px;
    }

    .two-columns {
        display: flex;
        gap: 30px;
        margin-bottom: 25px;
    }

    .column {
        flex: 1;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
        margin: 20px 0;
        font-size: 13px;
    }

    .table thead {
        background: #0c3d78;
        border-top: none;
        border-bottom: none;
    }

    .table th {
        padding: 10px 12px;
        text-align: left;
        font-weight: 700;
        font-size: 11px;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        color: #fff;
    }

    .table td {
        padding: 12px;
        border-bottom: 1px solid #ddd;
    }

    .table tbody tr:hover {
        background-color: #f5f5f5;
    }

    .amount-section {
        margin-top: 25px;
        padding: 16px 18px;
        background: #f7f9fc;
        border: 1px solid #d5deea;
        border-top: 3px solid #0c3d78;
        border-radius: 0;
    }

    .amount-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 12px;
        font-size: 14px;
        padding: 8px 0;
        border-bottom: 1px dotted #ddd;
    }

    .amount-row.total {
        font-weight: 700;
        font-size: 16px;
        background-color: #08284f;
        color: #fff;
        border-bottom: none;
        padding: 12px 10px;
        margin-bottom: 0;
        border-radius: 0;
    }

    .amount-label {
        font-weight: 600;
        color: #555;
    }

    .amount-value {
        font-weight: 700;
        color: #08284f;
        text-align: right;
    }

    .amount-row.total .amount-label,
    .amount-row.total .amount-value {
        color: #fff;
    }

    .badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 5px;
        font-size: 12px;
        font-weight: 700;
    }

    .badge-primary {
        background-color: #1565c0;
        color: white;
    }

    .status-badge {
        padding: 10px 15px;
        border-radius: 5px;
        font-weight: 700;
        font-size: 13px;
        display: inline-block;
    }

    .status-paid {
        background-color: #d4edda;
        color: #155724;
    }

    .status-partial {
        background-color: #fff3cd;
        color: #856404;
    }

    .footer {
        margin-top: 40px;
        padding-top: 20px;
        border-top: 2px solid #ddd;
        text-align: center;
        font-size: 11px;
        color: #999;
    }

    .signature-section {
        display: flex;
        justify-content: space-around;
        margin-top: 50px;
        text-align: center;
    }

    .signature-box {
        width: 150px;
        border-top: 2px solid #333;
        padding-top: 20px;
        font-size: 12px;
        font-weight: 600;
    }

    .print-buttons {
        text-align: center;
        margin-bottom: 20px;
        gap: 10px;
    }

    .print-buttons button,
    .print-buttons a {
        padding: 10px 20px;
        margin: 5px;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        text-decoration: none;
        display: inline-block;
    }

    .btn-print {
        background-color: #1565c0;
        color: white;
    }

    .btn-print:hover {
        background-color: #0d47a1;
    }

    .btn-back {
        background-color: #6c757d;
        color: white;
    }

    .btn-back:hover {
        background-color: #545b62;
    }

    .divider {
        height: 1px;
        background-color: #ddd;
        margin: 20px 0;
    }
</style>
