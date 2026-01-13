<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Gate Clearance</title>
    <style>
        body { font-family: sans-serif; margin: 20px; font-size: 10px; }
        .container { width: 100%; }
        .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid black; padding-bottom: 10px; }
        .title { font-size: 14px; font-weight: bold; }
        .section { margin-top: 15px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid black; padding: 5px; text-align: left; vertical-align: top; }
        .note { font-weight: bold; margin-top: 15px; }
        .signature-area { margin-top: 50px; display: flex; justify-content: space-between; }
        .signature-block { text-align: center; width: 30%; }
        .signature-line { border-top: 1px solid black; margin-top: 5px; padding-top: 3px; }
    </style>
</head>
<body>

<div class="container">

    {{-- HEADER --}}
    <div class="header">
        
            <img src="{{asset('storage/loctr/T67wirLP4lcwOKlYZlLOaS8OUgDGppKwKgFxudVL.png')}}" height="40">
       
        <div class="title">{{ $application->form_title }}</div>
        <div><code>{{ $application->status }}</code></div>
    </div>

    {{-- FORM INFO --}}
    <div class="section">
        <table>
            <tr>
                <td colspan="2">Document Code: N/A</td>
                <td>Control No. {{ $application->control_number ?? 'N/A' }}</td>
            </tr>
            <tr>
                <td colspan="2">Effectivity Date: {{ $application->created_at->format('F d, Y') }}</td>
                <td>GP No. {{ $application->form_number }}</td>
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
                @foreach($application->articleDetails as $item)
                <tr>
                    <td>{{ $item->marks_and_number }}</td>
                    <td>{{ $item->qty }}</td>
                    <td>{{ $item->detailed_description_of_article }}</td>
                    <td>{{ $item->gross_weight }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- PAYMENT & DATES --}}
    @php 
        $selection = $application->selections->first();
    @endphp

    <div class="section">
        <table>
            <tr>
                <td>SI NUMBER: {{ $application->created_at->format('F d, Y') }}</td>
                <td>FULLY PAID UNDER: {{ $application->created_at->format('F d, Y') }}</td>
                <td>AMOUNT: ₱{{ $selection->amount ?? '0.00' }}</td>
            </tr>
            <tr>
                <td>DATE OF DELIVERY: {{ $application->created_at->format('F d, Y') }}</td>
                <td>DATE: {{ $application->created_at->format('F d, Y') }}</td>
                <td>EXPIRATION DATE: {{ $selection->Expired_at ?? 'N/A' }}</td>
            </tr>
        </table>
    </div>

    {{-- STATIC CHECKLIST AND NOTES --}}
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
                        [ ] Invoice/s<br>
                        [ ] Packing List<br>
                        [] Delivery Receipt<br>
                        [ ] Inventory List<br>
                        [] Purchase Order<br>
                        [] N/A (Local Articles)
                    </td>
                    <td>
                        @php
                         $option= $application->options->first();
                         @endphp
                        {{ $option->name }}
                        {{ $option->validity }}
                        
                        {{--application name validity
                         [] P10,000.00 and below, 1 Time Validity<br>
                        [ ] P10,000.01 to P50,000.00, 1 Time Validity<br>
                        [ ] More than P50,000.00, 1 Time Validity<br><br>

                        [ ] P10,000.00 and below, 5 Day Validity<br>
                        [ ] P10,000.01 to P50,000.00, 5 Day Validity<br><br>

                        [ ] P10,000.00 and below, 20 Day Validity<br>
                        [ ] P10,000.01 to P50,000.00, 20 Day Validity --}}
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
            Permit No: {{ $application->form_number }}
        </div>
        <div class="signature-block">
            <div class="signature-line">GERALD B. DUAGAN</div>
            SEZ/OSAC Manager
        </div>
    </div>
</div>

</body>
</html>
