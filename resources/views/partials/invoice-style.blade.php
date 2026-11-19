<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Source+Sans+3:wght@400;500;600;700&family=Source+Serif+4:opsz,wght@8..60,500;8..60,600;8..60,700&display=swap" rel="stylesheet">
<link href="{{ asset('assets/vendor/bootstrap-icons/bootstrap-icons.css') }}" rel="stylesheet">
<style>
    :root {
        --inv-navy: #0c3d78;
        --inv-navy-deep: #08284f;
        --inv-red: #b42318;
        --inv-ink: #172033;
        --inv-muted: #5d6b80;
        --inv-line: #e3e9f2;
        --inv-paper: #ffffff;
        --inv-wash: #f4f7fb;
        --inv-screen: #dfe6f0;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    body {
        font-family: "Source Sans 3", "Segoe UI", sans-serif;
        color: var(--inv-ink);
        background: var(--inv-screen);
        line-height: 1.45;
        -webkit-font-smoothing: antialiased;
    }

    @page {
        size: A4;
        margin: 10mm;
    }

    @media print {
        body {
            background: #fff;
        }

        .no-print {
            display: none !important;
        }

        .inv-page {
            max-width: none;
            margin: 0;
            box-shadow: none;
        }

        .inv-table thead {
            display: table-header-group;
        }

        .inv-header,
        .print-phones,
        .print-magasin,
        .inv-watermark,
        .inv-party,
        .inv-status-pill,
        .inv-totals-grand,
        .inv-doc-block,
        .inv-table thead th,
        .inv-logo {
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .inv-table tr,
        .inv-signatures,
        .inv-totals {
            break-inside: avoid;
        }
    }

    .inv-toolbar {
        max-width: 860px;
        margin: 22px auto 0;
        display: flex;
        justify-content: flex-end;
        gap: 8px;
    }

    .inv-page-wide ~ .inv-toolbar,
    .inv-toolbar.wide {
        max-width: 1100px;
    }

    .inv-toolbar button,
    .inv-toolbar a {
        padding: 9px 16px;
        border-radius: 5px;
        cursor: pointer;
        font-family: inherit;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .inv-btn-print {
        background: var(--inv-navy);
        color: #fff;
        border: 1px solid var(--inv-navy);
    }

    .inv-btn-back {
        background: #fff;
        color: var(--inv-red);
        border: 1px solid #e7c4c0;
    }

    .inv-page {
        max-width: 860px;
        margin: 14px auto 48px;
        background: var(--inv-paper);
        box-shadow: 0 16px 40px rgba(12, 32, 64, 0.12);
        overflow: hidden;
    }

    .inv-page-wide {
        max-width: 1100px;
    }

    .inv-header {
        position: relative;
        overflow: hidden;
        color: #fff;
        background:
            radial-gradient(ellipse at 100% 0%, rgba(255, 255, 255, 0.16), transparent 42%),
            linear-gradient(118deg, #06182f 0%, #0a3268 46%, #14508f 100%);
        border-bottom: 4px solid #b42318;
    }

    .inv-header::before {
        content: "";
        position: absolute;
        inset: 0;
        background: repeating-linear-gradient(-28deg, transparent, transparent 13px, rgba(255, 255, 255, 0.045) 13px, rgba(255, 255, 255, 0.045) 14px);
        pointer-events: none;
    }

    .inv-watermark {
        position: absolute;
        right: 172px;
        top: 50%;
        width: 196px;
        height: 74px;
        overflow: hidden;
        transform: translateY(-50%);
        pointer-events: none;
        z-index: 0;
    }

    .inv-watermark svg {
        width: 100%;
        height: 100%;
        display: block;
    }

    .inv-header-inner {
        position: relative;
        z-index: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 18px;
        padding: 18px 28px 16px;
    }

    .inv-brand-block {
        display: flex;
        gap: 14px;
        align-items: center;
        min-width: 0;
    }

    .inv-logo {
        width: 148px;
        height: 52px;
        border: none;
        overflow: hidden;
        flex-shrink: 0;
        background: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 4px 8px;
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.18);
    }

    .inv-logo img {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .inv-company-name {
        font-family: "Source Serif 4", Georgia, serif;
        font-size: 22px;
        font-weight: 700;
        letter-spacing: 0.01em;
        color: #fff;
        line-height: 1.1;
    }

    .inv-company-sub {
        margin-top: 2px;
        font-size: 12px;
        font-weight: 600;
        color: #f0c7c3;
    }

    .inv-company-contact {
        margin-top: 6px;
        font-size: 11px;
        color: rgba(255, 255, 255, 0.82);
        display: flex;
        flex-wrap: wrap;
        gap: 3px 12px;
    }

    .print-phones,
    .print-magasin {
        flex-basis: 100%;
        margin-top: 2px;
        font-size: 11.5px;
        font-weight: 700;
        letter-spacing: 0.01em;
        color: #fff;
    }

    .inv-doc-block {
        text-align: left;
        flex: 0 0 auto;
        width: max-content;
        max-width: 156px;
        background: #fff;
        color: var(--inv-navy-deep);
        padding: 7px 10px 8px;
        border-left: 3px solid #b42318;
        box-shadow: 0 8px 18px rgba(0, 0, 0, 0.16);
    }

    .inv-doc-kicker {
        font-size: 9px;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: #b42318;
    }

    .inv-doc-title {
        margin-top: 1px;
        font-family: "Source Serif 4", Georgia, serif;
        font-size: 16px;
        font-weight: 700;
        line-height: 1.1;
        letter-spacing: -0.02em;
        color: #08284f;
    }

    .inv-doc-number {
        margin-top: 4px;
        font-size: 11px;
        font-weight: 700;
        line-height: 1.25;
        color: #08284f;
        overflow-wrap: anywhere;
    }

    .inv-doc-number span {
        font-weight: 600;
        color: #5d6b80;
        margin-right: 4px;
    }

    .inv-doc-date {
        margin-top: 1px;
        font-size: 11px;
        color: #5d6b80;
    }

    .inv-doc-author {
        margin-top: 4px;
        font-size: 10px;
        line-height: 1.25;
        color: #5d6b80;
    }

    .inv-status-pill {
        margin-top: 6px;
        display: inline-block;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        padding: 2px 8px;
        border: 1px solid currentColor;
        background: #f4f7fb;
    }

    .inv-status-paid {
        color: var(--inv-navy);
    }

    .inv-status-partial {
        color: var(--inv-red);
    }

    .inv-parties {
        display: flex;
        gap: 16px;
        padding: 22px 40px 6px;
    }

    .inv-party {
        flex: 1;
        min-width: 0;
        padding: 14px 16px 16px;
        background: var(--inv-wash);
        border-top: 3px solid var(--inv-navy);
    }

    .inv-party + .inv-party {
        border-left: none;
        border-top-color: var(--inv-red);
    }

    .inv-party-label {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.14em;
        text-transform: uppercase;
        color: var(--inv-muted);
        margin-bottom: 6px;
    }

    .inv-party-name {
        font-family: "Source Serif 4", Georgia, serif;
        font-size: 17px;
        font-weight: 600;
        color: var(--inv-ink);
        line-height: 1.25;
        margin-bottom: 4px;
    }

    .inv-party-line {
        font-size: 12.5px;
        color: var(--inv-muted);
        margin-top: 2px;
    }

    .inv-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 0;
        margin: 16px 40px 0;
        border-top: 1px solid var(--inv-line);
        border-bottom: 1px solid var(--inv-line);
    }

    .inv-meta-item {
        flex: 1;
        min-width: 120px;
        padding: 10px 14px;
    }

    .inv-meta-item + .inv-meta-item {
        border-left: 1px solid var(--inv-line);
    }

    .inv-meta-label {
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--inv-muted);
    }

    .inv-meta-value {
        margin-top: 2px;
        font-size: 13px;
        font-weight: 600;
        color: var(--inv-ink);
    }

    .inv-body {
        padding: 22px 40px 8px;
    }

    .inv-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
    }

    .inv-table thead th {
        text-align: left;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
        color: #fff;
        background: var(--inv-navy);
        padding: 8px 8px;
        border-bottom: none;
    }

    .inv-table thead th.inv-num,
    .inv-table thead th.inv-center {
        color: #fff;
    }

    .inv-table tbody td {
        padding: 11px 8px;
        border-bottom: 1px solid var(--inv-line);
        vertical-align: top;
    }

    .inv-table tbody tr:last-child td {
        border-bottom: 1.5px solid var(--inv-navy);
    }

    .inv-num {
        text-align: right;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    .inv-center {
        text-align: center;
    }

    .inv-muted-cell {
        color: var(--inv-muted);
        font-size: 11.5px;
    }

    .inv-designation {
        font-weight: 600;
    }

    .inv-totals-wrap {
        display: flex;
        justify-content: flex-end;
        padding: 18px 0 4px;
    }

    .inv-totals {
        width: 280px;
    }

    .inv-totals-row {
        display: flex;
        justify-content: space-between;
        gap: 16px;
        font-size: 13px;
        padding: 5px 0;
        color: var(--inv-muted);
        border-bottom: none;
        background: transparent;
    }

    .inv-totals-row span:last-child {
        font-weight: 600;
        color: var(--inv-ink);
        font-variant-numeric: tabular-nums;
    }

    .inv-totals-row.strong span:last-child {
        color: var(--inv-navy);
    }

    .inv-totals-row.due,
    .inv-totals-row.due span:last-child {
        color: var(--inv-red);
        font-weight: 700;
    }

    .inv-totals-grand {
        display: flex;
        justify-content: space-between;
        align-items: baseline;
        gap: 16px;
        margin-top: 8px;
        padding: 10px 0 2px;
        border-top: 2px solid var(--inv-navy);
        background: transparent;
        color: var(--inv-ink);
    }

    .inv-totals-grand .lbl {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    .inv-totals-grand .val {
        font-family: "Source Serif 4", Georgia, serif;
        font-size: 22px;
        font-weight: 700;
        color: var(--inv-navy);
        font-variant-numeric: tabular-nums;
    }

    .inv-notes {
        margin: 18px 40px 0;
        padding: 12px 14px;
        background: var(--inv-wash);
        border-left: 3px solid var(--inv-red);
        font-size: 12px;
        color: var(--inv-muted);
    }

    .inv-signatures {
        display: flex;
        gap: 28px;
        margin: 36px 40px 8px;
    }

    .inv-signature {
        flex: 1;
    }

    .inv-signature .cap {
        font-size: 10.5px;
        font-weight: 700;
        letter-spacing: 0.1em;
        text-transform: uppercase;
        color: var(--inv-muted);
    }

    .inv-signature .who {
        margin-top: 10px;
        font-size: 13px;
        font-weight: 700;
        color: var(--inv-ink, #10233f);
    }

    .inv-signature .line {
        margin-top: 36px;
        border-top: 1px solid #9aa8bc;
    }

    .inv-signature .who + .line {
        margin-top: 28px;
    }

    .inv-footer {
        margin-top: 28px;
        padding: 14px 40px 18px;
        border-top: 1px solid var(--inv-line);
        text-align: center;
        font-size: 11px;
        color: var(--inv-muted);
    }

    .inv-footer strong {
        color: var(--inv-navy);
        font-weight: 700;
    }

    .inv-footer p + p {
        margin-top: 3px;
    }
</style>
