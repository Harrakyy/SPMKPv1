@extends('layouts.app')

@section('content')

<style>

/* PAGE TITLE */

.page-title{
font-size:24px;
font-weight:600;
margin-bottom:4px;
}

.page-subtitle{
color:#6b7280;
margin-bottom:25px;
}

/* FILTER BAR */

.filter-bar{
background:white;
padding:15px;
border-radius:12px;
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:20px;
box-shadow:0 2px 8px rgba(0,0,0,0.05);
}

.search-input{
width:400px;
padding:10px 14px;
border:1px solid #e5e7eb;
border-radius:8px;
outline:none;
}

/* STATUS DROPDOWN */

.status-dropdown{
position:relative;
}

.status-btn{
display:flex;
align-items:center;
justify-content:space-between;
gap:10px;
padding:10px 14px;
border:1px solid #e5e7eb;
border-radius:8px;
background:white;
cursor:pointer;
min-width:170px;
}

.status-menu{
position:absolute;
right:0;
top:45px;
background:white;
border:1px solid #e5e7eb;
border-radius:10px;
box-shadow:0 6px 20px rgba(0,0,0,0.08);
width:220px;
display:none;
flex-direction:column;
z-index:100;
}

.status-menu a{
padding:12px 16px;
text-decoration:none;
color:#333;
font-size:14px;
}

.status-menu a:hover{
background:#f3f4f6;
}

/* TABLE */

.table-box{
background:white;
border-radius:14px;
overflow:hidden;
box-shadow:0 2px 10px rgba(0,0,0,0.07);
}

table{
width:100%;
border-collapse:collapse;
}

thead{
background:#f9fafb;
}

th{
text-align:left;
font-size:13px;
color:#6b7280;
padding:14px 20px;
border-bottom:1px solid #e5e7eb;
}

td{
padding:16px 20px;
border-bottom:1px solid #f3f4f6;
font-size:14px;
}

.machine-name{
font-weight:600;
}

.machine-desc{
font-size:12px;
color:#6b7280;
margin-top:4px;
}

.status-blue{
color:#2563eb;
font-weight:600;
}

.status-orange{
color:#f59e0b;
font-weight:600;
}

.action-btn{
background:none;
border:none;
cursor:pointer;
font-size:18px;
color:#2563eb;
}

.action-btn:hover{
color:#1d4ed8;
}

</style>

<div class="page-title">
Request Management
</div>

<div class="page-subtitle">
View machine production requests (Read Only)
</div>

<!-- FILTER BAR -->

<div class="filter-bar">

<input type="text"
class="search-input"
placeholder="Search by ID or machine name...">

<div class="status-dropdown">

<div class="status-btn" onclick="toggleStatusMenu()">
<span id="statusText">All Status</span>
<i class="bi bi-chevron-down"></i>
</div>

<div class="status-menu" id="statusMenu">

<a href="#" onclick="selectStatus('All Status')">
All Status
</a>

<a href="#" onclick="selectStatus('Waiting for Parts')">
Waiting for Parts
</a>

<a href="#" onclick="selectStatus('Parts Completed')">
Parts Completed
</a>

<a href="#" onclick="selectStatus('Ready Production')">
Ready Production
</a>

<a href="#" onclick="selectStatus('In Production')">
In Production
</a>

<a href="#" onclick="selectStatus('Completed')">
Completed
</a>

</div>

</div>

</div>

<!-- TABLE -->

<div class="table-box">

<table>

<thead>

<tr>
<th>REQUEST ID</th>
<th>MACHINE NAME</th>
<th>QUANTITY</th>
<th>DUE DATE</th>
<th>PARTS</th>
<th>STATUS</th>
<th>ACTIONS</th>
</tr>

</thead>

<tbody>

<tr data-status="completed">

<td>REQ-2025-001</td>

<td>

<div class="machine-name">
Custom Gear Box Machine
</div>

<div class="machine-desc">
Mesin gear box untuk packaging line dengan kapasitas 100
</div>

</td>

<td>2 unit</td>

<td>15/2/2025</td>

<td>5 parts</td>

<td class="status-blue">
Part Listing Completed
</td>

<td>

<a href="{{ route('machining.request.detail','REQ-2025-001') }}">
<button class="action-btn">
<i class="bi bi-eye"></i>
</button>
</a>

</td>

</tr>

<tr data-status="waiting">

<td>REQ-2025-002</td>

<td>

<div class="machine-name">
Conveyor Belt System
</div>

<div class="machine-desc">
System conveyor dengan panjang 10 meter
</div>

</td>

<td>1 unit</td>

<td>28/2/2025</td>

<td>0 parts</td>

<td class="status-orange">
Waiting for Part Listing
</td>

<td>

<a href="{{ route('machining.request.detail','REQ-2025-002') }}">
<button class="action-btn">
<i class="bi bi-eye"></i>
</button>
</a>

</td>

</tr>

</tbody>

</table>

</div>

<script>

function toggleStatusMenu(){

let menu = document.getElementById("statusMenu");

if(menu.style.display === "flex"){
menu.style.display = "none";
}else{
menu.style.display = "flex";
}

}

function selectStatus(status){

document.getElementById("statusText").innerText = status;
document.getElementById("statusMenu").style.display = "none";

let rows = document.querySelectorAll("tbody tr");

rows.forEach(row => {

let rowStatus = row.getAttribute("data-status");

if(status === "All Status"){
row.style.display = "";
}

else if(status === "Waiting for Parts"){
row.style.display = rowStatus === "waiting" ? "" : "none";
}

else if(status === "Parts Completed"){
row.style.display = rowStatus === "completed" ? "" : "none";
}

else{
row.style.display = "";
}

});

}

window.onclick = function(e){

if(!e.target.closest(".status-dropdown")){
document.getElementById("statusMenu").style.display = "none";
}

}

</script>

@endsection
