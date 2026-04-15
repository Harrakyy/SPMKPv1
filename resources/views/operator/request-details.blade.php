@extends('layouts.app')

@section('content')

<style>

.page-title{
font-size:24px;
font-weight:600;
margin-bottom:4px;
}

.page-sub{
color:#6b7280;
margin-bottom:20px;
}

.back-link{
display:flex;
align-items:center;
gap:6px;
color:#6b7280;
text-decoration:none;
margin-bottom:10px;
}

.detail-box{
background:white;
padding:25px;
border-radius:14px;
box-shadow:0 2px 10px rgba(0,0,0,0.07);
margin-bottom:25px;
}

.detail-grid{
display:grid;
grid-template-columns:1fr 1fr;
gap:25px;
margin-bottom:20px;
}

.label{
font-size:13px;
color:#6b7280;
margin-bottom:5px;
}

.value{
font-size:16px;
}

.status-blue{
color:#2563eb;
font-weight:600;
}

table{
width:100%;
border-collapse:collapse;
}

th{
text-align:left;
padding:12px;
font-size:13px;
color:#6b7280;
border-bottom:1px solid #e5e7eb;
}

td{
padding:12px;
border-bottom:1px solid #f3f4f6;
}

</style>

<a href="{{ route('machining.request') }}" class="back-link">
<i class="bi bi-arrow-left"></i>
Back to Request Management
</a>

<div class="page-title">
Request Details
</div>

<div class="page-sub">
Request ID: {{ $id }}
</div>

<div class="detail-box">

<div class="detail-grid">

<div>
<div class="label">Machine Name</div>
<div class="value">Custom Gear Box Machine</div>
</div>

<div>
<div class="label">Quantity</div>
<div class="value">2 unit</div>
</div>

<div>
<div class="label">Due Date</div>
<div class="value">15/2/2025</div>
</div>

<div>
<div class="label">Status</div>
<div class="value status-blue">Part Listing Completed</div>
</div>

</div>

<div class="label">Description</div>
<div class="value">
Mesin gear box untuk packaging line dengan kapasitas 100 unit/jam
</div>

</div>

<div class="detail-box">

<div style="font-weight:600;margin-bottom:10px">
Part Listing (3 parts)
</div>

<table>

<tr>
<th>Part Name</th>
<th>Material</th>
<th>Dimension Finish</th>
<th>Qty</th>
</tr>

<tr>
<td>Gear Shaft</td>
<td>SCM 440</td>
<td>Ø50 x 200</td>
<td>2</td>
</tr>

<tr>
<td>Bearing Housing</td>
<td>SS 304</td>
<td>100 x 100 x 50</td>
<td>4</td>
</tr>

<tr>
<td>Cover Plate</td>
<td>Mild Steel</td>
<td>200 x 150 x 10</td>
<td>2</td>
</tr>

</table>

</div>

@endsection
