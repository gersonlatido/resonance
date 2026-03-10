<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SHOW ALL USERS
    |--------------------------------------------------------------------------
    */
    public function index()
    {
        $users = User::orderBy('employee_id', 'asc')->get();

        return view('admin.user-management', compact('users'));
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW CREATE USER FORM
    |--------------------------------------------------------------------------
    */
    public function create()
    {
        return view('admin.user-create');
    }

    /*
    |--------------------------------------------------------------------------
    | STORE NEW USER
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'nullable|email',
            'password' => 'required|min:6',
            'position' => 'required|in:admin,cashier',
        ]);

        User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'position' => $request->position,
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW EDIT USER
    |--------------------------------------------------------------------------
    */
    public function edit($employee_id)
    {
        $user = User::where('employee_id', $employee_id)->firstOrFail();

        return view('admin.user-edit', compact('user'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE USER
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $employee_id)
    {
        $user = User::where('employee_id', $employee_id)->firstOrFail();

        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->employee_id . ',employee_id',
            'email' => 'nullable|email',
            'position' => 'required|in:admin,cashier',
        ]);

        $user->update([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'position' => $request->position,
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => Hash::make($request->password)
            ]);
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | DELETE USER
    |--------------------------------------------------------------------------
    */
    public function destroy($employee_id)
    {
        $user = User::where('employee_id', $employee_id)->firstOrFail();

        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}