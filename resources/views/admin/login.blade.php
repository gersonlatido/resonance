<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login</title>

<style>

body{
    font-family: Arial, sans-serif;
    background-color:#f4f4f4;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
    margin:0;
}

.login-container{
    background:#fff;
    box-shadow:0 4px 12px rgba(0,0,0,0.1);
    padding:40px;
    border-radius:10px;
    width:350px;
    text-align:center;
}

.login-container img{
    width:200px;
    margin-bottom:20px;
}

.login-container h2{
    font-size:24px;
    margin-bottom:30px;
    color:#333;
}

.login-container input{
    width:100%;
    padding:12px;
    border:2px solid #ccc;
    border-radius:5px;
    font-size:16px;
    box-sizing:border-box;
    margin-bottom:20px;
}

.login-container input:focus{
    outline:none;
    border-color:#ff7f00;
}

.login-container button.login-btn{
    width:100%;
    padding:14px;
    background:#ff7f00;
    color:white;
    border:none;
    border-radius:5px;
    font-size:18px;
    cursor:pointer;
    transition:0.3s;
}

.login-container button.login-btn:hover{
    background:#f98e2f;
}

/* password wrapper */

.password-wrapper{
    position:relative;
    margin-bottom:20px;
}

.password-wrapper input{
    padding-right:45px;
    margin-bottom:0;
}

/* eye button */

.toggle-password{
    position:absolute;
    right:12px;
    top:50%;
    transform:translateY(-50%);
    border:none;
    background:none;
    cursor:pointer;
    padding:0;
    display:flex;
    align-items:center;
    justify-content:center;
    color:#555;
}

.toggle-password svg{
    width:20px;
    height:20px;
}

</style>
</head>

<body>

<div class="login-container">

<img src="{{ asset('/images/logo-image.png') }}" alt="Logo">

<h2>Login</h2>

<form id="login-form" action="{{ route('admin.login') }}" method="POST">

@csrf

<input
type="text"
id="username"
name="username"
placeholder="Enter your username"
value="{{ old('username') }}"
required
>

<div class="password-wrapper">

<input
type="password"
id="password"
name="password"
placeholder="Enter your password"
required
>

<button type="button" class="toggle-password" id="togglePassword">

<!-- eye open -->
<svg id="eyeOpen" xmlns="http://www.w3.org/2000/svg"
fill="none" viewBox="0 0 24 24" stroke="currentColor">

<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>

<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
d="M2.458 12C3.732 7.943 7.523 5 12 5
c4.478 0 8.268 2.943 9.542 7
-1.274 4.057-5.064 7-9.542 7
-4.477 0-8.268-2.943-9.542-7z"/>

</svg>

<!-- eye closed -->
<svg id="eyeClosed" style="display:none"
xmlns="http://www.w3.org/2000/svg"
fill="none"
viewBox="0 0 24 24"
stroke="currentColor">

<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
d="M13.875 18.825A10.05 10.05 0 0112 19
c-4.478 0-8.268-2.943-9.542-7
a10.051 10.051 0 012.082-3.368"/>

<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
d="M6.223 6.223A9.953 9.953 0 0112 5
c4.478 0 8.268 2.943 9.542 7
a9.953 9.953 0 01-4.132 5.411"/>

<line x1="3" y1="3" x2="21" y2="21"
stroke-width="2" stroke-linecap="round"/>

</svg>

</button>

</div>

<button type="submit" class="login-btn">Login</button>

</form>

</div>

<script>

document.addEventListener("DOMContentLoaded", function(){

const togglePassword = document.getElementById("togglePassword");
const password = document.getElementById("password");

const eyeOpen = document.getElementById("eyeOpen");
const eyeClosed = document.getElementById("eyeClosed");

togglePassword.addEventListener("click", function(){

const hidden = password.type === "password";

password.type = hidden ? "text" : "password";

eyeOpen.style.display = hidden ? "none" : "block";
eyeClosed.style.display = hidden ? "block" : "none";

});

});

</script>

</body>
</html>