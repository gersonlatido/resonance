<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>User Management</title>

<style>
:root{
    --panel:#ffffff;
    --sidebar:#a9a9a9;
    --text:#222;
    --muted:#6b7280;
    --orange:#f59e0b;
    --border:#e5e7eb;
    --shadow:0 8px 20px rgba(0,0,0,.08);
}

*{
    box-sizing:border-box;
}

body{
    margin:0;
    font-family:Arial, Helvetica, sans-serif;
    background:#f6f6f6;
}

.container{
    max-width:1100px;
    margin:auto;
    padding:40px 20px;
}

.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

h1{
    margin:0;
    font-size:26px;
}

.add-btn{
    background:var(--orange);
    color:white;
    border:none;
    padding:10px 18px;
    border-radius:6px;
    cursor:pointer;
    font-size:14px;
}

.add-btn:hover{
    opacity:.9;
}

.table-box{
    background:white;
    border-radius:10px;
    box-shadow:var(--shadow);
    overflow:hidden;
}

table{
    width:100%;
    border-collapse:collapse;
}

th,td{
    padding:14px;
    text-align:left;
}

th{
    background:#fafafa;
    border-bottom:1px solid var(--border);
}

td{
    border-bottom:1px solid var(--border);
}

.role{
    padding:4px 10px;
    border-radius:20px;
    font-size:12px;
}

.role.admin{
    background:#ffe7c2;
    color:#b45309;
}

.role.cashier{
    background:#dbeafe;
    color:#1d4ed8;
}

.actions{
    display:flex;
    gap:10px;
}

.edit-btn{
    background:#10b981;
    color:white;
    border:none;
    padding:6px 12px;
    border-radius:5px;
    cursor:pointer;
}

.delete-btn{
    background:#ef4444;
    color:white;
    border:none;
    padding:6px 12px;
    border-radius:5px;
    cursor:pointer;
}

.success{
    background:#d1fae5;
    color:#065f46;
    padding:10px;
    border-radius:6px;
    margin-bottom:15px;
}
</style>
</head>

<body>

<div class="container">

<div class="header">
<h1>User Management</h1>

<a href="{{ route('admin.users.create') }}">
<button class="add-btn">+ Add Staff</button>
</a>
</div>

@if(session('success'))
<div class="success">
{{ session('success') }}
</div>
@endif

<div class="table-box">

<table>
<thead>
<tr>
<th>Employee ID</th>
<th>Name</th>
<th>Username</th>
<th>Email</th>
<th>Position</th>
<th>Actions</th>
</tr>
</thead>

<tbody>

@foreach($users as $user)

<tr>

<td>{{ $user->employee_id }}</td>
<td>{{ $user->name }}</td>
<td>{{ $user->username }}</td>
<td>{{ $user->email }}</td>

<td>
<span class="role {{ strtolower($user->position) }}">
{{ ucfirst($user->position) }}
</span>
</td>

<td>

<div class="actions">

<a href="{{ route('admin.users.edit',$user->employee_id) }}">
<button class="edit-btn">Edit</button>
</a>

<form action="{{ route('admin.users.destroy',$user->employee_id) }}" method="POST">
@csrf
@method('DELETE')

<button class="delete-btn"
onclick="return confirm('Delete this user?')">
Delete
</button>

</form>

</div>

</td>

</tr>

@endforeach

</tbody>

</table>

</div>

</div>

</body>
</html>