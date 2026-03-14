@php
    $users = $users ?? collect();
    $editUser = $editUser ?? null;

    $isEdit = !is_null($editUser);

    $userPosition = strtolower(auth()->user()->position ?? '');
    $isAdmin = $userPosition === 'admin';
    $isCashier = $userPosition === 'cashier';
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('images/Logogogo.png') }}">
    <title>User Management</title>

    <style>
        :root{
            --panel:#ffffff;
            --sidebar:#e4e3e3;
            --text:#222;
            --muted:#6b7280;
            --orange:#f59e0b;
            --orange-2:#ffb300;
            --card:#fff;
            --border:#e5e7eb;
            --shadow-soft:0 10px 30px rgba(0,0,0,.08);
            --shadow-strong:0 20px 50px rgba(0,0,0,.18);
            --radius:16px;
            --danger:#ef4444;
            --danger-dark:#dc2626;
            --success:#10b981;
            --success-dark:#059669;
            --blue:#2563eb;
            --blue-soft:#eff6ff;
            --bg-soft:#fafafa;
        }

        *{ box-sizing:border-box; }

        html{ scroll-behavior:smooth; }

        body{
            margin:0;
            font-family:'Figtree', sans-serif;
            background:#fff;
            color:var(--text);
        }

        .shell{
            width:100%;
            min-height:100vh;
            display:grid;
            grid-template-columns:240px 1fr;
        }

     .shell{
      width:100%;
      min-height:100vh;
      display:grid;
      grid-template-columns:240px 1fr;
    }

    .shell{
      width:100%;
      min-height:100vh;
      display:grid;
      grid-template-columns:240px 1fr;
    }

   .sidebar{
      background:var(--sidebar);
      padding:18px 14px;
      border-right:1px solid rgba(0,0,0,.06);
        position:sticky;
      top:0;
      height:100vh;
    }
    .sidebar .brand{
      display:flex;
      align-items:center;
      justify-content:center;
      padding:6px 6px 14px;
    }
    .logo-box{
      width:120px;
      height:58px;
      background:#fff;
      border-radius:10px;
      display:flex;
      align-items:center;
      justify-content:center;
      overflow:hidden;
      box-shadow:0 2px 8px rgba(0,0,0,.08);
    }
    .logo-box img{
      width:100%;
      height:100%;
      object-fit:contain;
      padding:6px;
    }

    .side-section-title{
      font-size:11px;
      font-weight:800;
      color:#1f2937;
      margin:14px 6px 8px;
      text-transform:uppercase;
      opacity:.85;
    }
    .nav{
      display:flex;
      flex-direction:column;
      gap:8px;
      padding:0 6px;
    }
    .nav a{
      text-decoration:none;
      font-size:13px;
      padding:10px 12px;
      border-radius:999px;
      color:#111;
      display:flex;
      align-items:center;
      gap:8px;
      transition:.15s ease;
      background:rgba(255,255,255,.55);
      border:1px solid rgba(0,0,0,.04);
    }
    .nav a:hover{ background:rgba(255,184,30,.25); }
    .nav a.active{
      background:var(--orange);
      color:#111;
      font-weight:800;
      border-color:rgba(0,0,0,.06);
      box-shadow:0 6px 14px rgba(0,0,0,.12);
    }


        .content{
            padding:22px 24px;
            background:#fff;
        }

        .header{
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:16px;
            margin-bottom:16px;
        }

        .header-left .title{
            font-size:26px;
            font-weight:900;
            color:var(--orange);
            letter-spacing:.2px;
            margin:0;
        }

        .header-left .subtitle{
            margin-top:6px;
            font-size:13px;
            color:var(--muted);
            font-weight:600;
        }

        .header-actions{
            display:flex;
            align-items:center;
            gap:10px;
            flex-wrap:wrap;
        }

        .add-btn,
        .logout-btn,
        .cancel-btn,
        .submit-btn,
        .edit-btn,
        .delete-btn,
        .secondary-btn,
        .modal-close{
            border:none;
            cursor:pointer;
            font-family:inherit;
            transition:.15s ease;
        }

        .add-btn{
            padding:10px 16px;
            background:var(--orange);
            border-radius:12px;
            font-weight:900;
            color:#111;
            box-shadow:0 8px 20px rgba(0,0,0,.12);
        }

        .logout-btn{
            padding:10px 16px;
            background:#fff;
            border:1px solid rgba(0,0,0,.08);
            border-radius:12px;
            font-weight:900;
            box-shadow:0 8px 20px rgba(0,0,0,.06);
        }

        .logout-btn:hover,
        .add-btn:hover,
        .submit-btn:hover,
        .edit-btn:hover,
        .delete-btn:hover,
        .secondary-btn:hover,
        .cancel-btn:hover,
        .modal-close:hover{
            filter:brightness(.98);
            transform:translateY(-1px);
        }

        .flash{
            margin-bottom:12px;
            padding:13px 15px;
            border-radius:12px;
            font-size:13px;
            font-weight:800;
            animation:fadeSlide .22s ease;
        }

        .flash.success{
            background:#ecfdf5;
            color:#065f46;
            border:1px solid #a7f3d0;
        }

        .flash.error{
            background:#fef2f2;
            color:#991b1b;
            border:1px solid #fecaca;
        }

        @keyframes fadeSlide{
            from{ opacity:0; transform:translateY(6px); }
            to{ opacity:1; transform:translateY(0); }
        }

        .card{
            background:#fff;
            border:1px solid rgba(0,0,0,.06);
            border-radius:var(--radius);
            box-shadow:var(--shadow-soft);
            overflow:hidden;
        }

        .card-header{
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:12px;
            padding:14px;
            border-bottom:1px solid rgba(0,0,0,.06);
            background:linear-gradient(0deg, rgba(245,158,11,.08), rgba(245,158,11,.08));
        }

        .card-title{
            font-size:20px;
            font-weight:900;
            margin:0;
            color:#111;
        }

        .card-sub{
            font-size:12px;
            color:var(--muted);
            font-weight:800;
        }

        .mini-pill{
            font-size:11px;
            color:var(--muted);
            font-weight:900;
            background:rgba(245,158,11,.12);
            border:1px solid rgba(245,158,11,.20);
            padding:4px 8px;
            border-radius:999px;
            white-space:nowrap;
        }

        .table-wrap{
            overflow:auto;
        }

        table{
            width:100%;
            border-collapse:collapse;
            min-width:900px;
        }

        th, td{
            padding:15px 16px;
            border-bottom:1px solid var(--border);
            text-align:left;
            vertical-align:middle;
        }

        th{
            font-size:12px;
            text-transform:uppercase;
            letter-spacing:.35px;
            color:#374151;
            background:#fff;
            font-weight:900;
            white-space:nowrap;
        }

        td{
            font-size:14px;
            color:#111827;
            background:#fff;
        }

        tbody tr{
            transition:.15s ease;
        }

        tr:hover td{
            background:#fffcf5;
        }

        .emp-id{
            font-weight:900;
            color:#111;
            white-space:nowrap;
        }

        .name-cell{
            font-weight:800;
        }

        .username-text{
            font-weight:700;
            color:#374151;
        }

        .email-text{
            color:#374151;
        }

        .position-badge{
            display:inline-flex;
            align-items:center;
            justify-content:center;
            padding:6px 12px;
            border-radius:999px;
            font-size:12px;
            font-weight:900;
            min-width:90px;
        }

        .position-admin{
            background:#fff2d9;
            color:#a16207;
            border:1px solid rgba(245,158,11,.18);
        }

        .position-cashier{
            background:#e8f0ff;
            color:#1d4ed8;
            border:1px solid rgba(37,99,235,.16);
        }

        .table-actions{
            display:flex;
            gap:8px;
            flex-wrap:wrap;
        }

        .edit-btn{
            background:var(--success);
            color:#fff;
            padding:8px 14px;
            border-radius:10px;
            font-size:13px;
            font-weight:800;
            text-decoration:none;
            display:inline-flex;
            align-items:center;
            justify-content:center;
        }

        .delete-btn{
            background:var(--danger);
            color:#fff;
            padding:8px 14px;
            border-radius:10px;
            font-size:13px;
            font-weight:800;
        }

        .empty{
            padding:26px 18px;
            text-align:center;
            color:#6b7280;
            font-size:13px;
            font-weight:800;
        }

        .modal-backdrop{
            position:fixed;
            inset:0;
            background:rgba(17,24,39,.52);
            display:none;
            align-items:center;
            justify-content:center;
            padding:16px;
            z-index:9999;
            backdrop-filter:blur(2px);
        }

        .modal-backdrop.show{
            display:flex;
            animation:modalFade .18s ease;
        }

        @keyframes modalFade{
            from{ opacity:0; }
            to{ opacity:1; }
        }

        .modal{
            width:100%;
            max-width:520px;
            background:#fff;
            border-radius:18px;
            box-shadow:var(--shadow-strong);
            overflow:hidden;
            animation:modalPop .2s ease;
        }

        .modal.large{
            max-width:640px;
        }

        @keyframes modalPop{
            from{ opacity:0; transform:translateY(10px) scale(.985); }
            to{ opacity:1; transform:translateY(0) scale(1); }
        }

        .modal-head{
            display:flex;
            align-items:flex-start;
            justify-content:space-between;
            gap:12px;
            padding:16px 18px 10px;
            border-bottom:1px solid rgba(0,0,0,.06);
            background:linear-gradient(0deg, rgba(245,158,11,.08), rgba(245,158,11,.08));
        }

        .modal-title{
            font-size:20px;
            font-weight:900;
            color:#111;
            margin:0;
        }

        .modal-subtitle{
            margin-top:4px;
            color:#6b7280;
            font-size:12px;
            font-weight:700;
        }

        .modal-close{
            background:#fff;
            width:34px;
            height:34px;
            border-radius:10px;
            font-size:18px;
            font-weight:900;
            line-height:1;
            box-shadow:0 2px 8px rgba(0,0,0,.08);
            flex-shrink:0;
        }

        .modal-body{
            padding:18px;
        }

        .form-grid{
            display:grid;
            grid-template-columns:1fr;
            gap:14px;
        }

        .field{
            display:flex;
            flex-direction:column;
            gap:7px;
        }

        .field.two{
            display:grid;
            grid-template-columns:1fr 1fr;
            gap:12px;
        }

        label{
            font-size:13px;
            font-weight:900;
            color:#111827;
        }

        input, select{
            width:100%;
            border:1px solid rgba(0,0,0,.12);
            background:#fff;
            border-radius:12px;
            padding:12px 13px;
            font-size:14px;
            font-family:inherit;
            outline:none;
            transition:.15s ease;
        }

        input:focus, select:focus{
            border-color:rgba(245,158,11,.85);
            box-shadow:0 0 0 4px rgba(245,158,11,.12);
        }

        .readonly{
            background:#f9fafb;
            color:#6b7280;
        }

        .helper{
            font-size:12px;
            color:var(--muted);
            font-weight:700;
            margin-top:-2px;
        }

        .error-text{
            font-size:12px;
            color:#dc2626;
            font-weight:800;
            margin-top:-2px;
        }

        .modal-actions{
            display:flex;
            justify-content:flex-end;
            gap:10px;
            flex-wrap:wrap;
            margin-top:8px;
        }

        .submit-btn{
            padding:11px 16px;
            background:var(--orange);
            border-radius:12px;
            font-size:14px;
            font-weight:900;
            color:#111;
            box-shadow:0 8px 20px rgba(0,0,0,.10);
        }

        .secondary-btn,
        .cancel-btn{
            padding:11px 16px;
            background:#eef2f7;
            color:#374151;
            border-radius:12px;
            font-size:14px;
            font-weight:900;
            text-decoration:none;
            display:inline-flex;
            align-items:center;
            justify-content:center;
        }

        .confirm-delete-btn{
            padding:11px 16px;
            border-radius:12px;
            font-size:14px;
            font-weight:900;
            background:var(--danger);
            color:#fff;
            border:none;
            cursor:pointer;
        }

        .form-note{
            margin-bottom:12px;
            padding:10px 12px;
            border-radius:10px;
            font-size:12px;
            font-weight:700;
            color:#92400e;
            background:#fff7e6;
            border:1px solid rgba(245,158,11,.18);
        }

        @media (max-width: 900px){
            .header{
                flex-direction:column;
                align-items:stretch;
            }
        }

        @media (max-width: 720px){
            .shell{ grid-template-columns:1fr; }
            .sidebar{ display:none; }
            .content{ padding:16px; }
            .header-actions{
                width:100%;
                justify-content:stretch;
            }
            .field.two{
                grid-template-columns:1fr;
            }
            .modal{
                max-width:100%;
            }
        }
    </style>
</head>

<body>
<div class="shell">

    <!-- Sidebar -->
   <aside class="sidebar">
  <div class="brand">
    <div class="logo-box">
      <img src="{{ asset('images/logo-image.png') }}" alt="Silog Cafe Logo" />
    </div>
  </div>

  @php
    $userPosition = strtolower(auth()->user()->position ?? '');
    $isAdmin = $userPosition === 'admin';
    $isCashier = $userPosition === 'cashier';
  @endphp

  {{-- Cashier + Admin --}}
  @if($isAdmin || $isCashier)
    <div class="side-section-title">Cashier Transaction</div>
    <nav class="nav">

    <a href="{{ route('admin.dashboard.analytics') }}"   class="{{ request()->routeIs('admin.dashboard.analytics') ? 'active' : '' }}">Dashboard</a>

      <a href="{{ route('admin.dashboard') }}"
         class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">Order Management</a>

      <a href="{{ route('admin.table-management') }}"
         class="{{ request()->routeIs('admin.table-management') ? 'active' : '' }}">Table Management</a>

      <a href="{{ route('admin.daily-sales-report') }}"
         class="{{ request()->routeIs('admin.daily-sales-report') ? 'active' : '' }}">Sales Report</a>


    </nav>
  @endif

  {{-- Admin only --}}
  @if($isAdmin)
    <div class="side-section-title" style="margin-top:18px;">Admin Management</div>
    <nav class="nav">
      <a href="{{ route('admin.menu-management') }}"
         class="{{ request()->routeIs('admin.menu-management') ? 'active' : '' }}">Menu Management</a>

      <a href="{{ route('admin.feedbacks') }}"
         class="{{ request()->routeIs('admin.feedbacks') ? 'active' : '' }}">Feedback Management</a>

      <a href="{{ route('admin.inventory') }}"
         class="{{ request()->routeIs('admin.inventory') ? 'active' : '' }}">Inventory Management</a>

      <a href="{{ route('admin.sales-stock-reports') }}"
         class="{{ request()->routeIs('admin.sales-stock-reports') ? 'active' : '' }}">Stock Reports</a>

      <a href="{{ route('admin.users.index') }}"
         class="{{ request()->routeIs('admin.users.*') ? 'active' : '' }}">User Management</a>
    </nav>
  @endif
</aside>

    <main class="content">
        <div class="header">
            <div class="header-left">
                <h1 class="title">User Management</h1>
                <div class="subtitle">Manage staff accounts with create, edit, and delete actions.</div>
            </div>

            <div class="header-actions">
                <button type="button" class="add-btn" id="openCreateModal">+ Add Staff</button>

                <form method="POST" action="{{ route('admin.logout') }}">
                    @csrf
                    <button class="logout-btn" type="submit">Log Out</button>
                </form>
            </div>
        </div>

        @if(session('success'))
            <div class="flash success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="flash error">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="flash error">{{ $errors->first() }}</div>
        @endif

        <section class="card">
            <div class="card-header">
                <div>
                    <h2 class="card-title">Staff Accounts</h2>
                    <div class="card-sub">All registered admin and cashier users</div>
                </div>
                <div class="mini-pill">{{ $users->count() }} total</div>
            </div>

            <div class="table-wrap">
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
                    @forelse($users as $user)
                        @php
                            $position = strtolower($user->position ?? '');
                        @endphp
                        <tr>
                            <td class="emp-id">{{ $user->employee_id }}</td>
                            <td class="name-cell">{{ $user->name }}</td>
                            <td class="username-text">{{ $user->username }}</td>
                            <td class="email-text">{{ $user->email }}</td>
                            <td>
                                <span class="position-badge {{ $position === 'admin' ? 'position-admin' : 'position-cashier' }}">
                                    {{ ucfirst($user->position) }}
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="{{ route('admin.users.edit', $user->employee_id) }}" class="edit-btn">Edit</a>

                                    <form method="POST"
                                          action="{{ route('admin.users.destroy', $user->employee_id) }}"
                                          class="delete-user-form"
                                          data-name="{{ $user->name }}"
                                          style="margin:0;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="delete-btn">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <div class="empty">No staff accounts found.</div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>

<!-- CREATE MODAL -->
<div class="modal-backdrop {{ (!$isEdit && $errors->any()) ? 'show' : '' }}" id="createModal">
    <div class="modal large">
        <div class="modal-head">
            <div>
                <h3 class="modal-title">Add Staff</h3>
                <div class="modal-subtitle">Create a new admin or cashier account.</div>
            </div>
            <button type="button" class="modal-close" data-close="createModal">&times;</button>
        </div>

        <div class="modal-body">
            <div class="form-note">Fill in the required account details below.</div>

            <form method="POST" action="{{ route('admin.users.store') }}" id="createUserForm">
                @csrf

                <div class="form-grid">
                    <div class="field">
                        <label for="create_name">Name</label>
                        <input type="text" id="create_name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            @if(!$isEdit)
                                <div class="error-text">{{ $message }}</div>
                            @endif
                        @enderror
                    </div>

                    <div class="field two">
                        <div style="display:flex; flex-direction:column; gap:7px;">
                            <label for="create_username">Username</label>
                            <input type="text" id="create_username" name="username" value="{{ old('username') }}" required>
                            @error('username')
                                @if(!$isEdit)
                                    <div class="error-text">{{ $message }}</div>
                                @endif
                            @enderror
                        </div>

                        <div style="display:flex; flex-direction:column; gap:7px;">
                            <label for="create_position">Position</label>
                            <select id="create_position" name="position" required>
                                <option value="">Select Position</option>
                                <option value="Admin" {{ old('position') === 'Admin' ? 'selected' : '' }}>Admin</option>
                                <option value="Cashier" {{ old('position') === 'Cashier' ? 'selected' : '' }}>Cashier</option>
                            </select>
                            @error('position')
                                @if(!$isEdit)
                                    <div class="error-text">{{ $message }}</div>
                                @endif
                            @enderror
                        </div>
                    </div>

                    <div class="field">
                        <label for="create_email">Email</label>
                        <input type="email" id="create_email" name="email" value="{{ old('email') }}">
                        @error('email')
                            @if(!$isEdit)
                                <div class="error-text">{{ $message }}</div>
                            @endif
                        @enderror
                    </div>

                    <div class="field">
                        <label for="create_password">Password</label>
                        <input type="password" id="create_password" name="password" required>
                        <div class="helper">Set a secure password for the new account.</div>
                        @error('password')
                            @if(!$isEdit)
                                <div class="error-text">{{ $message }}</div>
                            @endif
                        @enderror
                    </div>

                    <div class="modal-actions">
                        <button type="button" class="cancel-btn" data-close="createModal">Cancel</button>
                        <button type="submit" class="submit-btn">Create User</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- EDIT MODAL -->
<div class="modal-backdrop {{ $isEdit ? 'show' : '' }}" id="editModal">
    <div class="modal large">
        <div class="modal-head">
            <div>
                <h3 class="modal-title">Edit Staff</h3>
                <div class="modal-subtitle">Update staff account information.</div>
            </div>
            <button type="button" class="modal-close" data-edit-close="1">&times;</button>
        </div>

        <div class="modal-body">
            @if($isEdit)
                <div class="form-note">Update only the fields you need to change.</div>

                <form method="POST" action="{{ route('admin.users.update', $editUser->employee_id) }}" id="editUserForm">
                    @csrf
                    @method('PUT')

                    <div class="form-grid">
                        <div class="field">
                            <label>Employee ID</label>
                            <input type="text" class="readonly" value="{{ $editUser->employee_id }}" readonly>
                        </div>

                        <div class="field">
                            <label for="edit_name">Name</label>
                            <input type="text" id="edit_name" name="name" value="{{ old('name', $editUser->name) }}" required>
                            @error('name')
                                @if($isEdit)
                                    <div class="error-text">{{ $message }}</div>
                                @endif
                            @enderror
                        </div>

                        <div class="field two">
                            <div style="display:flex; flex-direction:column; gap:7px;">
                                <label for="edit_username">Username</label>
                                <input type="text" id="edit_username" name="username" value="{{ old('username', $editUser->username) }}" required>
                                @error('username')
                                    @if($isEdit)
                                        <div class="error-text">{{ $message }}</div>
                                    @endif
                                @enderror
                            </div>

                            <div style="display:flex; flex-direction:column; gap:7px;">
                                <label for="edit_position">Position</label>
                                <select id="edit_position" name="position" required>
                                    <option value="">Select Position</option>
                                    <option value="Admin" {{ old('position', $editUser->position) === 'Admin' ? 'selected' : '' }}>Admin</option>
                                    <option value="Cashier" {{ old('position', $editUser->position) === 'Cashier' ? 'selected' : '' }}>Cashier</option>
                                </select>
                                @error('position')
                                    @if($isEdit)
                                        <div class="error-text">{{ $message }}</div>
                                    @endif
                                @enderror
                            </div>
                        </div>

                        <div class="field">
                            <label for="edit_email">Email</label>
                            <input type="email" id="edit_email" name="email" value="{{ old('email', $editUser->email) }}">
                            @error('email')
                                @if($isEdit)
                                    <div class="error-text">{{ $message }}</div>
                                @endif
                            @enderror
                        </div>

                        <div class="field">
                            <label for="edit_password">New Password</label>
                            <input type="password" id="edit_password" name="password">
                            <div class="helper">Leave blank if you do not want to change the password.</div>
                            @error('password')
                                @if($isEdit)
                                    <div class="error-text">{{ $message }}</div>
                                @endif
                            @enderror
                        </div>

                        <div class="modal-actions">
                            <a href="{{ route('admin.users.index') }}" class="cancel-btn">Cancel</a>
                            <button type="submit" class="submit-btn">Update User</button>
                        </div>
                    </div>
                </form>
            @endif
        </div>
    </div>
</div>

<!-- DELETE MODAL -->
<div class="modal-backdrop" id="deleteModal">
    <div class="modal">
        <div class="modal-head">
            <div>
                <h3 class="modal-title">Delete User</h3>
                <div class="modal-subtitle">This action cannot be undone.</div>
            </div>
            <button type="button" class="modal-close" data-close="deleteModal">&times;</button>
        </div>

        <div class="modal-body">
            <div id="deleteMessage" style="color:#4b5563; font-size:14px; font-weight:600; line-height:1.55;">
                Are you sure you want to delete this user?
            </div>

            <div class="modal-actions">
                <button type="button" class="cancel-btn" id="cancelDelete">Cancel</button>
                <button type="button" class="confirm-delete-btn" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
    let selectedDeleteForm = null;

    const createModal = document.getElementById('createModal');
    const editModal = document.getElementById('editModal');
    const deleteModal = document.getElementById('deleteModal');

    const openCreateModalBtn = document.getElementById('openCreateModal');
    const deleteForms = document.querySelectorAll('.delete-user-form');
    const deleteMessage = document.getElementById('deleteMessage');
    const cancelDelete = document.getElementById('cancelDelete');
    const confirmDelete = document.getElementById('confirmDelete');
    const createUserForm = document.getElementById('createUserForm');

    function openModal(modal){
        if(modal) {
            modal.classList.add('show');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeModal(modal){
        if(modal) {
            modal.classList.remove('show');

            const stillOpen = document.querySelector('.modal-backdrop.show');
            if (!stillOpen) {
                document.body.style.overflow = '';
            }
        }
    }

    function resetCreateForm(){
        if (createUserForm) {
            createUserForm.reset();
        }
    }

    if (openCreateModalBtn) {
        openCreateModalBtn.addEventListener('click', function(){
            resetCreateForm();
            openModal(createModal);
        });
    }

    document.querySelectorAll('[data-close]').forEach(btn => {
        btn.addEventListener('click', function(){
            const modalId = this.getAttribute('data-close');
            const modal = document.getElementById(modalId);
            closeModal(modal);

            if (modalId === 'createModal') {
                resetCreateForm();
            }

            if (modalId === 'deleteModal') {
                selectedDeleteForm = null;
            }
        });
    });

    document.querySelectorAll('[data-edit-close]').forEach(btn => {
        btn.addEventListener('click', function(){
            window.location.href = "{{ route('admin.users.index') }}";
        });
    });

    [createModal, editModal, deleteModal].forEach(modal => {
        if (!modal) return;

        modal.addEventListener('click', function(e){
            if (e.target === modal) {
                if (modal === editModal) {
                    window.location.href = "{{ route('admin.users.index') }}";
                } else {
                    closeModal(modal);

                    if (modal === createModal) {
                        resetCreateForm();
                    }

                    if (modal === deleteModal) {
                        selectedDeleteForm = null;
                    }
                }
            }
        });
    });

    document.addEventListener('keydown', function(e){
        if (e.key === 'Escape') {
            if (deleteModal.classList.contains('show')) {
                closeModal(deleteModal);
                selectedDeleteForm = null;
                return;
            }

            if (editModal.classList.contains('show')) {
                window.location.href = "{{ route('admin.users.index') }}";
                return;
            }

            if (createModal.classList.contains('show')) {
                closeModal(createModal);
                resetCreateForm();
            }
        }
    });

    deleteForms.forEach(form => {
        form.addEventListener('submit', function(e){
            e.preventDefault();
            selectedDeleteForm = this;
            const name = this.dataset.name || 'this user';
            deleteMessage.textContent = `Are you sure you want to delete ${name}?`;
            openModal(deleteModal);
        });
    });

    if (cancelDelete) {
        cancelDelete.addEventListener('click', function(){
            closeModal(deleteModal);
            selectedDeleteForm = null;
        });
    }

    if (confirmDelete) {
        confirmDelete.addEventListener('click', function(){
            if(selectedDeleteForm){
                confirmDelete.disabled = true;
                confirmDelete.textContent = 'Deleting...';
                selectedDeleteForm.submit();
            }
        });
    }

    @if(!$isEdit && $errors->any())
        document.body.style.overflow = 'hidden';
    @endif

    @if($isEdit)
        document.body.style.overflow = 'hidden';
    @endif
</script>
</body>
</html>