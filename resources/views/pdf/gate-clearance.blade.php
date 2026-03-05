<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Gate Clearance</title>
    <style>
        body { font-family: sans-serif; margin: 20px; font-size: 10px; }
        .container { width: 100%; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid green; padding-bottom: 10px; }
        .title { font-size: 14px; font-weight: bold; }
        .section { margin-top: 15px; border-bottom: 1px solid green; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 5px; text-align: left; vertical-align: top; }
        .note { font-weight: bold; margin-top: 15px; }
        .signature-area { margin-top: 50px; display: flex; justify-content: space-between; }
        .signature-block { text-align: center; width: 30%; }
        .signature-line { border-top: 1px solid black; margin-top: 5px; padding-top: 3px; }
        input[type=checkbox] { transform: scale(1.2); margin-right: 5px; }
    </style>
</head>
<body>

<div class="container">

    {{-- HEADER --}}
    <div class="header">
        <img src="https://www.jhmc.com.ph/wp-content/uploads/2025/08/Your-paragraph-text-4.png" height="150">
        <div class="title">{{ $application->form_title ?? 'Gate Pass' }}</div>
        <div><code>{{ $application->status ?? 'N/A' }}</code></div>
    </div>

    {{-- FORM INFO --}}
    <div class="section">
        <table>
            <tr>
                <td colspan="2">Document Code: N/A</td>
                <td>Control No.: {{ $application->control_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td colspan="2">Effectivity Date: {{ optional($application->updated_at)->format('F d, Y') ?? 'N/A' }}</td>
                <td>GP No.: {{ $application->form_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td colspan="3">
                    Please allow <strong>{{ $application->company_name ?? 'N/A' }}</strong>
                    with vehicle plate no. <strong>{{ $application->plate_no ?? 'N/A' }}</strong>
                    to pass the JHSEZ Gate with the following articles:
                </td>
            </tr>
        </table>
    </div>

    {{-- ARTICLE DETAILS --}}
    <div class="section">
        <table>
            <thead>
                <tr>
                    <th>MARKS AND NUMBER</th>
                    <th>QUANTITY</th>
                    <th>DETAILED DESCRIPTION OF ARTICLES</th>
                    <th>GROSS WEIGHT / CONTAINER NO.</th>
                </tr>
            </thead>
            <tbody>
                @forelse($application->articleDetails ?? [] as $item)
                <tr>
                    <td>{{ $item->marks_and_number ?? '' }}</td>
                    <td>{{ $item->qty ?? '' }}</td>
                    <td>{{ $item->detailed_description_of_article ?? '' }}</td>
                    <td>{{ $item->gross_weight ?? '' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" style="text-align: center;">No articles found</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PAYMENT & DATES --}}
    @php
        $selection = $application->userAppSelection ?? collect();
        $selection = $selection->first();
        $approval = $application->approval ?? collect();
        $approval = $approval->first();
        $uploads = $application->uploads ?? collect();
    @endphp

    <div class="section">
        <table>
            <tr>
                <td>SI NUMBER: {{ $approval->IS_Number ?? 'N/A' }}</td>
                <td>PAYMENT STATUS: {{ $approval->payment_status ?? 'N/A' }}</td>
                <td>AMOUNT: ₱{{ $selection->amount ?? '0.00' }}</td>
            </tr>
            <tr>
                <td>DATE OF DELIVERY: {{ $selection && $selection->selected_at ? \Carbon\Carbon::parse($selection->selected_at)->format('F d, Y') : 'N/A' }}</td>
                <td>DATE: {{ $selection && $selection->created_at ? \Carbon\Carbon::parse($selection->created_at)->format('F d, Y') : 'N/A' }}</td>
                <td>EXPIRATION DATE: {{ $selection && $selection->Expired_at ? \Carbon\Carbon::parse($selection->Expired_at)->format('F d, Y') : 'N/A' }}</td>
            </tr>
        </table>
    </div>

    {{-- SUPPORTING DOCUMENTS & FEES --}}
    <div class="section">
        <table>
            <thead>
                <tr>
                    <th>SUBMITTED SUPPORTING DOCUMENTS</th>
                    <th>DECLARED VALUE AND VALIDITY</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <label><input type="checkbox" {{ $uploads->contains(fn($u) => str_contains(strtolower($u->file_name), 'invoice')) ? 'checked' : '' }}> Invoice/s</label><br>
                        <label><input type="checkbox" {{ $uploads->contains(fn($u) => str_contains(strtolower($u->file_name), 'packing')) ? 'checked' : '' }}> Packing List</label><br>
                        <label><input type="checkbox" {{ $uploads->contains(fn($u) => str_contains(strtolower($u->file_name), 'delivery')) ? 'checked' : '' }}> Delivery Receipt</label><br>
                        <label><input type="checkbox" {{ $uploads->contains(fn($u) => str_contains(strtolower($u->file_name), 'inventory')) ? 'checked' : '' }}> Inventory List</label><br>
                        <label><input type="checkbox" {{ $uploads->contains(fn($u) => str_contains(strtolower($u->file_name), 'purchase')) ? 'checked' : '' }}> Purchase Order</label><br>
                        <label><input type="checkbox" {{ $uploads->contains(fn($u) => str_contains(strtolower($u->file_name), 'local')) ? 'checked' : '' }}> N/A (Local Articles)</label>
                    </td>
                    <td>
                        Selected Option: {{ optional($selection->feeOption)->title ?? 'N/A' }}<br>
                        Validity: {{ optional($selection->feeOption)->validity ?? 'N/A' }}<br>
                        Price: {{ optional($selection->feeOption)->price ?? '0.00' }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- IMPORTANT NOTE --}}
    <div class="section">
        <div class="note">IMPORTANT NOTE:</div>
        <p>
            This document serves as your official ENTRY PASS and must be presented to the
            security personnel at the JHSEZ gate. Failure to present this pass may result in
            denied access.
        </p>
    </div>

    {{-- SIGNATURE --}}
    <div class="signature-area">
        <div class="signature-block">
            <div class="signature-line">Customs Representative</div>
            Permit No: {{ $application->form_number ?? 'N/A' }}
        </div>
        <div class="signature-block">
            <div class="signature-line">GERALD B. DUAGAN</div>
            SEZ/OSAC Manager
        </div>
    </div>

</div>
</body>
</html>