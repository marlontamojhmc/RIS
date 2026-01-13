
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

    <div class="header">
        <img src="{{ public_path('storage/loctr/T67wirLP4lcwOKlYZlLOaS8OUgDGppKwKgFxudVL.png') }}" alt="Logo" height="40">
        <div class="title">{{$title}}</div>
        <div><code>{{$status}}</code></div>
    </div>

    <div class="section">
        <table>
            <tr>
                <td colspan="2">Document Code: N/A</td>
                <td>Control No. {{ $control_no ?? '2025-09-085' }}</td>
            </tr>
            <tr>
                <td colspan="2">Effectivity Date: {{ $effectivity_date ?? '8 August 2025' }}</td>
                <td>GP No. {{ $gp_no ?? '24075' }}</td>
            </tr>
            <tr>
                <td colspan="3">
                    Please allow <strong>{{ $company ?? 'Street Smart Magic Gen. Merch.' }}</strong>
                    with vehicle plate no. <strong>{{ $plate_no ?? 'N/A' }}</strong> to pass the JHSEZ Gate with the following articles:
                </td>
            </tr>
        </table>
    </div>

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
                <tr>
                    <td>N/A</td>
                    <td>4 Units<br>469 Pcs</td>
                    <td>Full Game Arcade<br>Desktop Monitor<br>(Nothing Follows)</td>
                    <td>N/A<br>N/A</td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <table>
            <tr>
                <td>SI NUMBER: September 26, 2025</td>
                <td>FULLY PAID UNDER: September 26, 2025</td>
                <td>AMOUNT: ₱20.00</td>
            </tr>
            <tr>
                <td>DATE OF DELIVERY: September 26, 2025</td>
                <td>DATE: September 26, 2025</td>
                <td>EXPIRATION DATE: September 26, 2025</td>
            </tr>
        </table>
    </div>

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
                        [X] Delivery Receipt<br>
                        [ ] Inventory List<br>
                        [X] Purchase Order<br>
                        [X] N/A (Local Articles)
                    </td>
                    <td>
                        [X] P10,000.00 and below, 1 Time Validity<br>
                        [ ] P10,000.01 to P50,000.00, 1 Time Validity<br>
                        [ ] More than P50,000.00, 1 Time Validity<br>
                        [ ] Medium construction vehicles, 1 Day Validity<br>
                        [ ] Heavy construction vehicles, 1 Day Validity<br>
                        <br>
                        [ ] P10,000.00 and below, 5 Day Validity<br>
                        [ ] P10,000.01 to P50,000.00, 5 Day Validity<br>
                        [ ] More than P50,000.00, 5 Day Validity<br>
                        <br>
                        [ ] P10,000.00 and below, 20 Day Validity<br>
                        [ ] P10,000.01 to P50,000.00, 20 Day Validity<br>
                        [ ] More than P50,000.00, 20 Day Validity
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="section">
        <div class="note">IMPORTANT NOTE:</div>
        <p>
            This document serves as your official ENTRY PASS and must be presented to the security personnel on duty
            at the sentry gate upon arrival at JHSEZ. Failure to produce this pass may result in denied access.
        </p>
    </div>

    <div class="signature-area">
        <div class="signature-block">
            <div class="signature-line">Customs Representative</div>
            Permit No: {{ $gp_no ?? '24075' }}
        </div>
        <div class="signature-block">
            <div class="signature-line">GERALD B. DUAGAN</div>
            SEZ/OSAC Manager
        </div>
    </div>

    <p>Validity Date: September 26, 2025 to September 26, 2025</p>
</div>

</body>
</html>
