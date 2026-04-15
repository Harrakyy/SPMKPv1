@extends('layouts.app')

@section('content')

<style>

/* TITLE */

.page-title{
font-size:26px;
font-weight:600;
}

.page-subtitle{
color:#6b7280;
margin-bottom:25px;
}

/* CARDS */

.card-row{
display:flex;
gap:20px;
margin-bottom:25px;
}

.stat-card{
flex:1;
background:white;
border-radius:14px;
padding:20px;
display:flex;
align-items:center;
justify-content:space-between;
box-shadow:0 2px 8px rgba(0,0,0,0.05);
}

.stat-left{
display:flex;
align-items:center;
gap:12px;
}

.stat-icon{
width:40px;
height:40px;
border-radius:10px;
display:flex;
align-items:center;
justify-content:center;
font-size:18px;
}

.icon-plan{
background:#e8f0ff;
color:#2563eb;
}

.icon-act{
background:#fff1e6;
color:#f97316;
}

.icon-done{
background:#e8f8ee;
color:#16a34a;
}

.stat-number{
font-size:22px;
font-weight:700;
}

.plan-number{
color:#2563eb;
}

.act-number{
color:#f97316;
}

.done-number{
color:#16a34a;
}

.stat-label{
font-size:14px;
color:#6b7280;
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
width:420px;
padding:10px 14px;
border:1px solid #e5e7eb;
border-radius:8px;
}

/* DROPDOWN */

.status-dropdown{
position:relative;
}

.status-btn{
padding:10px 16px;
border:1px solid #e5e7eb;
border-radius:8px;
background:white;
cursor:pointer;
min-width:150px;
display:flex;
justify-content:space-between;
align-items:center;
}

.status-menu{
position:absolute;
top:45px;
right:0;
background:white;
border-radius:10px;
border:1px solid #e5e7eb;
box-shadow:0 6px 20px rgba(0,0,0,0.1);
display:none;
width:200px;
z-index:99;
}

.status-menu div{
padding:12px 16px;
cursor:pointer;
}

.status-menu div:hover{
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
padding:14px 20px;
font-size:13px;
color:#6b7280;
}

td{
padding:16px 20px;
border-top:1px solid #f3f4f6;
font-size:14px;
}

/* STATUS */

.status-plan{
color:#2563eb;
font-weight:600;
}

.status-act{
color:#ef4444;
font-weight:600;
}

.status-done{
color:#16a34a;
font-weight:600;
}

/* PROCESS */

.process{
color:#2563eb;
font-weight:600;
}

/* ACTION ICON */

.action-play{
color:#f97316;
font-size:18px;
}

.action-done{
color:#16a34a;
font-size:18px;
cursor:pointer;
}

</style>

<div class="page-title">
Schedule MFG
</div>

<div class="page-subtitle">
Manufacturing Process Execution - Source: Excel schedule (6)
</div>

<!-- CARDS -->

<div class="card-row">

<div class="stat-card">

<div class="stat-left">

<div class="stat-icon icon-plan">
<i class="bi bi-clock"></i>
</div>

<div>

<div class="stat-number plan-number">3</div>
<div class="stat-label">Plan</div>

</div>

</div>

</div>

<div class="stat-card">

<div class="stat-left">

<div class="stat-icon icon-act">
<i class="bi bi-play"></i>
</div>

<div>

<div class="stat-number act-number">2</div>
<div class="stat-label">Act</div>

</div>

</div>

</div>

<div class="stat-card">

<div class="stat-left">

<div class="stat-icon icon-done">
<i class="bi bi-check-circle"></i>
</div>

<div>

<div class="stat-number done-number">1</div>
<div class="stat-label">Done</div>

</div>

</div>

</div>

</div>

<!-- FILTER -->

<div class="filter-bar">

<input class="search-input" placeholder="Search by machine or part...">

<div class="status-dropdown">

<div class="status-btn" onclick="toggleDropdown()">
<span id="statusText">All Status</span>
<i class="bi bi-chevron-down"></i>
</div>

<div class="status-menu" id="statusMenu">

<div onclick="setStatus('all')">All Status</div>

<div onclick="setStatus('plan')">
Plan
</div>

<div onclick="window.location.href='{{ route('operator.activity.detail', 'act') }}'">
    Act (In Progress)
</div>

<div onclick="window.location.href='{{ route('operator.activity.detail', 'done') }}'">
    Done (Completed)
</div>

</div>

</div>

</div>

<!-- TABLE -->

<div class="table-box">

<table>

<thead>

<tr>
<th>MACHINE</th>
<th>PART NAME</th>
<th>PIC</th>
<th>QTY</th>
<th>PLAN DATE</th>
<th>ACT DATE</th>
<th>PROCESS</th>
<th>STATUS</th>
<th>ACTIONS</th>
</tr>

</thead>

<tbody>

<tr id="row1" data-status="act">

<td>CNC Milling 01</td>
<td>Gear Shaft</td>
<td>Operator A</td>
<td>-</td>
<td>25/1/2025</td>
<td>25/1/2025</td>

<td class="process">MC</td>

<td class="status-act">
Act
</td>

<td>

<a href="/machining/activity-detail?machine=CNC Milling 01&part=Gear Shaft&pic=Operator A&plan=25/1/2025&act=25/1/2025&process=MC&row=row1">

<i class="bi bi-check-circle-fill action-done"></i>

</a>

</td>

</tr>


<tr id="row2" data-status="act">

<td>Heat Treatment Furnace</td>
<td>Gear Shaft</td>
<td>Operator B</td>
<td>-</td>
<td>26/1/2025</td>
<td>26/1/2025</td>

<td class="process">HT</td>

<td class="status-act">
Act
</td>

<td>

<a href="/machining/activity-detail?machine=Heat Treatment Furnace&part=Gear Shaft&pic=Operator B&plan=26/1/2025&act=26/1/2025&process=HT&row=row2">

<i class="bi bi-check-circle-fill action-done"></i>

</a>

</td>

</tr>


<tr id="row3" data-status="plan">

<td>Grinding Machine 02</td>
<td>Gear Shaft</td>
<td>Operator C</td>
<td>-</td>
<td>27/1/2025</td>
<td>-</td>

<td class="process">CD</td>

<td class="status-plan">
Plan
</td>

<td>
<i class="bi bi-play-fill action-play"></i>
</td>

</tr>


<tr id="row4" data-status="plan">

<td>Lathe 01</td>
<td>Bearing Housing</td>
<td>Operator D</td>
<td>-</td>
<td>28/1/2025</td>
<td>-</td>

<td class="process">SG</td>

<td class="status-plan">
Plan
</td>

<td>
<i class="bi bi-play-fill action-play"></i>
</td>

</tr>


<tr id="row5" data-status="plan">

<td>Welding Station 01</td>
<td>Frame Base</td>
<td>Operator E</td>
<td>-</td>
<td>1/2/2025</td>
<td>-</td>

<td class="process">WELD</td>

<td class="status-plan">
Plan
</td>

<td>
<i class="bi bi-play-fill action-play"></i>
</td>

</tr>

</tbody>

</table>

</div>

<script>

/* ===============================
   DROPDOWN
================================ */

function toggleDropdown(){

let menu = document.getElementById("statusMenu");

menu.style.display =
menu.style.display === "block"
? "none"
: "block";

}


/* ===============================
   FILTER STATUS
================================ */

function setStatus(status){

document.getElementById("statusMenu").style.display = "none";

let text = "All Status";

if(status === "plan") text = "Plan";
if(status === "act") text = "Act (In Progress)";
if(status === "done") text = "Done (Completed)";

document.getElementById("statusText").innerText = text;

let rows = document.querySelectorAll("tbody tr");

rows.forEach(row => {

let rowStatus = row.getAttribute("data-status");

if(status === "all"){
row.style.display = "";
}

else if(rowStatus === status){
row.style.display = "";
}

else{
row.style.display = "none";
}

});

}


/* ===============================
   LOAD STATUS DONE
================================ */

document.addEventListener("DOMContentLoaded", function(){

let doneRow = localStorage.getItem("done_row");

if(doneRow){

let row = document.getElementById(doneRow);

if(row){

row.setAttribute("data-status","done");

row.children[7].innerHTML =
'<span class="status-done">Done</span>';

row.children[8].innerHTML =
'<i class="bi bi-check-circle-fill action-done"></i>';

}

}

});

</script>

@endsection