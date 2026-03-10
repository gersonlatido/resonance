<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Staff</title>

<style>

body{
    font-family:Arial, Helvetica, sans-serif;
    background:#f5f5f5;
    margin:0;
}

.container{
    max-width:600px;
    margin:60px auto;
    background:white;
    padding:30px;
    border-radius:10px;
    box-shadow:0 8px 20px rgba(0,0,0,.08);
}

h2{
    margin-top:0;
}

.form-group{
    margin-bottom:15px;
}

label{
    display:block;
    font-size:14px;
    margin-bottom:5px;
}

input,select{
    width:100%;
    padding:10px;
    border:1px solid #ddd;
    border-radius:6px;
}

button{
    background:#f59e0b;
    color:white;
    border:none;
    padding:10px 18px;
    border-radius:6px;
    cursor:pointer;
}

button:hover{
    opacity:.9;
}

.back{
    margin-bottom:20px;
    display:inline-block;
}

.error{
    background:#fee2e2;
    color:#991b1b;
    padding:10px;
    border-radius:6px;
    margin-bottom:15px;
}

</style>
</head>

<body>

<div class="container">

<a href="{{ route('admin.users.index') }}" class="back">← Back to Users</a>

<h2>Add Staff</h2>

@if ($errors->any())
<div class="error">
<ul>
@foreach ($errors->all() as $error)
<li>{{ $error }}</li>
@endforeach
</ul>
</div>
@endif

<form method="POST" action="{{ route('admin.users.store') }}">

@csrf

<div class="form-group">
<label>Name</label>
<input type="text" name="name" required>
</div>

<div class="form-group">
<label>Username</label>
<input type="text" name="username" required>
</div>

<div class="form-group">
<label>Email</label>
<input type="email" name="email">
</div>

<div class="form-group">
<label>Password</label>
<input type="password" name="password" required>
</div>

<div class="form-group">
<label>Position</label>

<select name="position" required>
<option value="">Select Position</option>
<option value="admin">Admin</option>
<option value="cashier">Cashier</option>
</select>

</div>

<button type="submit">Create User</button>

</form>

</div>

</body>
</html>