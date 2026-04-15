@extends('layouts.app')

@section('content')

<style>

.page-title{
font-size:26px;
font-weight:600;
margin-bottom:5px;
}

.page-sub{
color:#6b7280;
margin-bottom:25px;
}

.back-link{
display:flex;
align-items:center;
gap:8px;
color:#374151;
text-decoration:none;
margin-bottom:15px;
font-size:14px;
}

.card-box{
background:white;
border-radius:14px;
padding:25px;
box-shadow:0 2px 10px rgba(0,0,0,0.07);
}

.info-grid{
display:grid;
grid-template-columns:1fr 1fr 1fr;
gap:30px;
margin-bottom:25px;
}

.label{
font-size:12px;
color:#6b7280;
margin-bottom:4px;
}

.value{
font-size:15px;
font-weight:500;
}

.edit-box{
margin-top:30px;
border-top:1px solid #eee;
padding-top:25px;
}

.input-date{
width:350px;
padding:10px;
border:1px solid #e5e7eb;
border-radius:8px;
}

.status-text{
color:#f97316;
font-weight:600;
}

.btn{
padding:10px 18px;
border-radius:8px;
border:none;
font-weight:500;
cursor:pointer;
}

.btn-update{
background:#f97316;
color:white;
}

.btn-done{
background:#16a34a;
color:white;
display:flex;
align-items:center;
gap:6px;
}

.btn-delete{
background:#dc2626;
color:white;
display:flex;
align-items:center;
gap:6px;
}

.btn-back{
border:1px solid #ddd;
background:white;
padding:10px 20px;
border-radius:8px;
cursor:pointer;
}

.completed-text{
color:#16a34a;
font-weight:600;
display:flex;
align-items:center;
gap:6px;
}

/* MODAL */

.modal-overlay{
position:fixed;
top:0;
left:0;
width:100%;
height:100%;
background:rgba(0,0,0,0.5);
display:none;
align-items:center;
justify-content:center;
z-index:999;
}

.modal-box{
background:white;
border-radius:12px;
width:420px;
padding:25px;
box-shadow:0 10px 25px rgba(0,0,0,0.2);
}

.modal-title{
font-size:18px;
font-weight:600;
margin-bottom:15px;
display:flex;
align-items:center;
gap:10px;
}

.warning-box{
background:#fff8e6;
border:1px solid #facc15;
padding:12px;
border-radius:8px;
font-size:13px;
margin-top:15px;
}

.modal-actions{
margin-top:20px;
display:flex;
justify-content:flex-end;
gap:10px;
}

.btn-cancel{
background:#f3f4f6;
padding:8px 16px;
border-radius:6px;
border:none;
cursor:pointer;
}

.btn-remove{
background:#dc2626;
color:white;
padding:8px 16px;
border-radius:6px;
border:none;
cursor:pointer;
}

</style>

@php

$machine = request()->get('machine','-');
$part = request()->get('part','-');
$pic = request()->get('pic','-');
$plan = request()->get('plan','-');
$act = request()->get('act','-');
$process = request()->get('process','-');

@endphp


<a href="/machining/schedule" class="back-link">
<i class="bi bi-arrow-left"></i>
Back to Schedule MFG
</a>

<div class="page-title">
Activity Detail
</div>

<div class="page-sub">
Activity ID: ACT-001 - Request: REQ-2025-001
</div>


<div class="card-box">

<h5 style="margin-bottom:20px;">Activity Information</h5>

<div class="info-grid">

<div>
<div class="label">MACHINE</div>
<div class="value">{{ $machine }}</div>
</div>

<div>
<div class="label">PART NAME</div>
<div class="value">{{ $part }}</div>
</div>

<div>
<div class="label">PROCESS CODE</div>
<div class="value text-primary fw-bold">{{ $process }}</div>
</div>

<div>
<div class="label">PLAN DATE</div>
<div class="value">{{ $plan }}</div>
</div>

<div>
<div class="label">PIC</div>
<div class="value">{{ $pic }}</div>
</div>

<div>
<div class="label">QUANTITY</div>
<div class="value">-</div>
</div>

</div>


<div class="edit-box">

<h6 style="margin-bottom:20px;">Editable Fields</h6>

<div style="display:flex; gap:40px; margin-bottom:15px;">

<div>

<div class="label">
ACT DATE (EDITABLE)
</div>

<input
type="text"
class="input-date"
placeholder="MM/DD/YY">

</div>

<div>

<div class="label">
STATUS (EDITABLE)
</div>

<div class="status-text">
Current: Act
</div>

</div>

</div>


<div style="display:flex; gap:12px; align-items:center; margin-bottom:25px;">

<button class="btn btn-update">
Update
</button>

<button class="btn btn-done" id="doneBtn" onclick="markDone()">
<i class="bi bi-check-circle"></i>
Mark as Done
</button>

<div id="completedText" class="completed-text" style="display:none;">
<i class="bi bi-check-circle"></i>
Activity Completed
</div>

</div>


<div style="display:flex; justify-content:space-between; align-items:center;">

<button class="btn btn-delete" onclick="openModal()">
<i class="bi bi-trash"></i>
Remove Activity
</button>

<a href="/machining/schedule">
<button class="btn-back">
Back to Schedule
</button>
</a>

</div>

</div>

</div>


<!-- MODAL -->

<div class="modal-overlay" id="removeModal">

<div class="modal-box">

<div class="modal-title">
<i class="bi bi-exclamation-circle text-danger"></i>
Remove Activity
</div>

<p>
Are you sure you want to remove this activity from the manufacturing schedule?
This action will update Excel Schedule (6).
</p>

<div class="warning-box">
<strong>Warning:</strong><br>
Removing this activity will affect the production schedule.
Make sure this is intentional.
</div>

<div class="modal-actions">

<button class="btn-cancel" onclick="closeModal()">
Cancel
</button>

<button class="btn-remove" onclick="removeActivity()">
Remove Activity
</button>

</div>

</div>

</div>


<script>

function markDone(){

document.getElementById("doneBtn").style.display="none";
document.getElementById("completedText").style.display="flex";

}

/* MODAL */

function openModal(){

document.getElementById("removeModal").style.display="flex";

}

function closeModal(){

document.getElementById("removeModal").style.display="none";

}

function removeActivity(){

window.location.href="/machining/schedule";

}

</script>

@endsection