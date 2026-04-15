@extends('layouts.app')

@section('content')

<style>

/* TITLE */

.page-title{
font-size:24px;
font-weight:600;
margin-bottom:5px;
}

.page-subtitle{
color:#6b7280;
margin-bottom:25px;
}


/* FILTER BUTTON */

.filter-container{
display:flex;
gap:10px;
margin-bottom:25px;
}

.filter-btn{
padding:8px 18px;
border-radius:8px;
border:none;
background:#e5e7eb;
cursor:pointer;
font-size:14px;
}

.filter-active{
background:#2563eb;
color:white;
}


/* TABLE BOX */

.table-box{
background:white;
border-radius:12px;
padding:25px;
box-shadow:0 2px 10px rgba(0,0,0,0.07);
}

.table-title{
font-size:18px;
font-weight:600;
margin-bottom:5px;
}

.table-subtitle{
font-size:13px;
color:#6b7280;
margin-bottom:15px;
}

table{
width:100%;
border-collapse:collapse;
}

th{
text-align:left;
font-size:13px;
color:#6b7280;
border-bottom:1px solid #e5e7eb;
padding:12px 0;
}

td{
padding:14px 0;
border-bottom:1px solid #f3f4f6;
}

.status-progress{
color:#ef4444;
font-weight:600;
}

.done-btn{
background:#16a34a;
color:white;
border:none;
padding:7px 16px;
border-radius:8px;
cursor:pointer;
}

</style>


<div class="page-title">
Manufacturing Process
</div>

<div class="page-subtitle">
Activity execution & status management
</div>


<!-- FILTER -->

<div class="filter-container">

<button class="filter-btn filter-active">
All Activities
</button>

<button class="filter-btn">
Plan
</button>

<button class="filter-btn">
In Progress
</button>

<button class="filter-btn">
Completed
</button>

</div>



<!-- TABLE -->

<div class="table-box">

<div class="table-title">
Activity Execution Table
</div>

<div class="table-subtitle">
Manage your assigned manufacturing activities
</div>

<table>

<tr>
<th>Part Name</th>
<th>Assigned Activity</th>
<th>Machine</th>
<th>PIC</th>
<th>Plan Date</th>
<th>Actual Date</th>
<th>Status</th>
<th>Action</th>
</tr>

<tr>

<td>Gear Shaft</td>

<td style="color:#2563eb">
Milling Operation
</td>

<td>CNC Milling 01</td>

<td>Operator A</td>

<td>2025-01-25</td>

<td>2025-01-25</td>

<td class="status-progress">
In Progress
</td>

<td>

<button class="done-btn" onclick="markDone(this)">
✔ Mark Done
</button>

</td>

</tr>

</table>

</div>

<script>

function markDone(button){

    // cari baris tabel
    let row = button.closest("tr");

    // ubah status
    let statusCell = row.querySelector(".status-progress");

    if(statusCell){
        statusCell.innerHTML = "Done";
        statusCell.style.color = "#16a34a";
        statusCell.style.fontWeight = "600";
    }

    // ubah tombol
    button.outerHTML = `
        <span style="color:#16a34a;font-weight:600">
            ✔ Completed
        </span>
    `;
}

</script>

@endsection