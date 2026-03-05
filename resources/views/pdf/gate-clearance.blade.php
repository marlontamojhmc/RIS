<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Gate Clearance</title>

<style>

body{
    font-family: sans-serif;
    font-size: 10px;
    margin:20px;
}

.container{
    width:100%;
}

/* HEADER */

.header{
    display:flex;
    align-items:center;
    justify-content:space-between;
    border-bottom:3px solid #1fa34a;
    padding-bottom:10px;
}

.logo{
    height:100px;
}

.title{
    font-size:16px;
    font-weight:bold;
}

.status{
    font-size:10px;
}

/* SECTIONS */

.section{
    margin-top:15px;
}

.separator{
    border-top:2px solid #1fa34a;
    margin:15px 0;
}

/* TABLE */

table{
    width:100%;
    border-collapse:collapse;
}

th,td{
    border:1px solid black;
    padding:5px;
    vertical-align:top;
}

th{
    background:#1fa34a;
    color:white;
}

/* NOTES */

.note-title{
    font-weight:bold;
}

/* SIGNATURE */

.signature-area{
    margin-top:50px;
    display:flex;
    justify-content:space-between;
}

.signature-block{
    text-align:center;
    width:30%;
}

.signature-line{
    border-top:1px solid black;
    margin-top:25px;
    padding-top:3px;
}

</style>

@php

$approval  = $application->approval->first();
$selection = $application->userAppSelection->first();
$feeOption = $selection?->feeOption;

$selectedDate = $selection?->selected_at
    ? \Carbon\Carbon::parse($selection->selected_at)->format('F d, Y')
    : '';

$createdDate = $selection?->created_at
    ? \Carbon\Carbon::parse($selection->created_at)->format('F d, Y')
    : '';

$expiredDate = $selection?->Expired_at
    ? \Carbon\Carbon::parse($selection->Expired_at)->format('F d, Y')
    : '';

$documents = [
    'invoice'   => 'Invoice/s',
    'packing'   => 'Packing List',
    'delivery'  => 'Delivery Receipt',
    'inventory' => 'Inventory List',
    'purchase'  => 'Purchase Order',
    'local'     => 'N/A (Local Articles)'
];

@endphp

</head>

<body>

<div class="container">

{{-- HEADER --}}

<div class="header">

<img class="logo"
src="https://www.jhmc.com.ph/wp-content/uploads/2025/08/Your-paragraph-text-4.png">

<div class="title">
{{ $application->form_title }}
</div>

<div class="status">
<code>{{ $application->status }}</code>
</div>

</div>


{{-- FORM INFORMATION --}}

<div class="section">

<table>

<tr>
<td colspan="2">Document Code: N/A</td>
<td>Control No.</td>
</tr>

<tr>
<td colspan="2">
Effectivity Date:
{{ $application->updated_at->format('F d, Y') }}
</td>

<td>
GP No. {{ $application->form_number }}
</td>
</tr>

<tr>
<td colspan="3">

Please allow
<strong>COMPANY NAME</strong>
with vehicle plate no.
<strong>PLATE NUMBER</strong>

to pass the JHSEZ Gate with the following articles:

</td>
</tr>

</table>

</div>

<div class="separator"></div>


{{-- ARTICLE DETAILS --}}

<div class="section">

<table>

<thead>
<tr>
<th>MARKS AND NUMBER</th>
<th>QUANTITY</th>
<th>DETAILED DESCRIPTION</th>
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

<div class="separator"></div>


{{-- PAYMENT INFORMATION --}}

<div class="section">

<table>

<tr>

<td>
SI NUMBER:
{{ $approval?->IS_Number }}
</td>

<td>
PAYMENT STATUS:
{{ $approval?->payment_status }}
</td>

<td>
AMOUNT:
₱{{ $selection?->amount ?? '0.00' }}
</td>

</tr>

<tr>

<td>
DATE OF DELIVERY:
{{ $selectedDate }}
</td>

<td>
DATE:
{{ $createdDate }}
</td>

<td>
EXPIRATION DATE:
{{ $expiredDate }}
</td>

</tr>

</table>

</div>

<div class="separator"></div>


{{-- DOCUMENT CHECKLIST --}}

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

@foreach($documents as $keyword => $label)

<input type="checkbox"
{{ $application->uploads->contains(fn($u)=>str_contains(strtolower($u->file_name),$keyword)) ? 'checked':'' }}>
{{ $label }}
<br>

@endforeach

</td>

<td>

<strong>Selected Option</strong><br>
{{ $feeOption?->title }}<br><br>

Validity:
{{ $feeOption?->validity }}<br>

Price:
{{ $feeOption?->price }}

</td>

</tr>

</tbody>

</table>

</div>


{{-- IMPORTANT NOTE --}}

<div class="section">

<div class="note-title">
IMPORTANT NOTE:
</div>

<p>

This document serves as your official
<strong>ENTRY PASS</strong> and must be presented
to the security personnel at the JHSEZ gate.

Failure to present this pass may result
in denied access.

</p>

</div>


{{-- SIGNATURE AREA --}}

<div class="signature-area">

<div class="signature-block">

<div class="signature-line">
Customs Representative
</div>

Permit No:
{{ $application->form_number }}

</div>


<div class="signature-block">

<div class="signature-line">
GERALD B. DUAGAN
</div>

SEZ / OSAC Manager

</div>

</div>


</div>

</body>
</html>