<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login - PT Sumber Prima Mandiri</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet">

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Poppins',sans-serif;
}

body{
height:100vh;
overflow:hidden;
}

/* CONTAINER */

.container{
display:flex;
width:100%;
height:100vh;
}

/* LEFT SIDE */

.left{
width:45%;
display:flex;
justify-content:center;
align-items:center;
background:#ffffff;
}

.login-box{
width:360px;
}

/* LOGO */

.logo{
text-align:center;
margin-bottom:30px;
}

.logo-img{
width:70px;
height:auto;
display:block;
margin:0 auto 10px auto;
object-fit:contain;
transform: rotate(-95deg);
}

/* COMPANY */

.company{
font-size:22px;
font-weight:600;
}

.subtitle{
font-size:13px;
color:#777;
margin-top:5px;
}

/* FORM */

label{
font-size:14px;
margin-top:15px;
display:block;
}

input{
width:100%;
padding:12px;
border-radius:8px;
border:1px solid #ddd;
margin-top:5px;
font-size:14px;
}

.forgot{
text-align:right;
font-size:12px;
margin-top:6px;
color:red;
}

/* LOGIN BUTTON */

.login-btn{
width:100%;
padding:12px;
margin-top:20px;
border:none;
border-radius:8px;
background:#2F6FED;
color:white;
font-weight:500;
cursor:pointer;
font-size:15px;
}

.login-btn:hover{
background:#1d4fd2;
}

/* DIVIDER */

.divider{
text-align:center;
margin:20px 0;
color:#999;
}

/* SOCIAL BUTTON */

.social{
display:flex;
gap:15px;
justify-content:center;
}

.social-icon{
width:22px;
height:22px;
object-fit:contain;
}

.social-btn{
display:flex;
align-items:center;
justify-content:center;
gap:10px;
padding:10px 18px;
border-radius:8px;
border:1px solid #ddd;
background:white;
cursor:pointer;
font-size:14px;
}

.social-btn:hover{
background:#f3f4f6;
}

/* RIGHT SIDE */

.right{
width:55%;
background:#F5F5F5;
overflow:hidden;
display:flex;
justify-content:center;
align-items:center;
}

.right img{
width:100%;
height:100%;
object-fit:cover;
}

/* RESPONSIVE */

@media(max-width:900px){

.right{
display:none;
}

.left{
width:100%;
}

}

</style>

</head>

<body>

<div class="container">

<!-- LEFT LOGIN FORM -->

<div class="left">

<div class="login-box">

<div class="logo">

<img src="{{ asset('images/logo-pt-sumber-prima-mandiri.png') }}" class="logo-img">

<div class="company">
PT Sumber Prima Mandiri
</div>

<div class="subtitle">
Internal Production Management System
</div>

</div>

@if(session('error'))
<p style="color:red">{{ session('error') }}</p>
@endif

<form method="POST" action="{{ route('login.process') }}">
@csrf

<label>Email</label>
<input type="email" name="email" placeholder="nama@sumberprimamandiri.com" required>

<label>Password</label>
<input type="password" name="password" placeholder="••••••••" required>

<div class="forgot">
Lupa Kata Sandi?
</div>

<button class="login-btn">
Login
</button>

</form>

<div class="divider">
or
</div>

<div class="social">

<button class="social-btn">
<img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/google/google-original.svg" class="social-icon">
Google
</button>

<button class="social-btn">
<img src="https://cdn.jsdelivr.net/gh/devicons/devicon/icons/apple/apple-original.svg" class="social-icon">
Apple
</button>

</div>

</div>

</div>


<!-- RIGHT ILLUSTRATION -->

<div class="right">

<img src="{{ asset('images/ilustrasi-login.png') }}">

</div>

</div>

</body>
</html>