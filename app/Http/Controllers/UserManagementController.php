<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | SHOW USER MANAGEMENT PAGE
    |--------------------------------------------------------------------------
    | One page only:
    | - left side = user list
    | - right side = add form
    */
    public function index()
    {
       $users = User::orderByRaw("CAST(SUBSTRING(employee_id, 4) AS UNSIGNED) ASC")->get();
        $editUser = null;

        return view('admin.user-management', compact('users', 'editUser'));
    }

    /*
    |--------------------------------------------------------------------------
    | OPTIONAL CREATE REDIRECT
    |--------------------------------------------------------------------------
    | Since we are using one page only, redirect create to index.
    */
    public function create()
    {
        return redirect()->route('admin.users.index');
    }

    /*
    |--------------------------------------------------------------------------
    | STORE NEW USER
    |--------------------------------------------------------------------------
    */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'nullable|email|max:255',
            'password' => 'required|string|min:6',
            'position' => 'required|in:Admin,Cashier',
        ]);

        User::create([
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'] ?? null,
            'password' => Hash::make($validated['password']),
            'position' => $validated['position'],
        ]);

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User created successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | SHOW EDIT MODE ON SAME PAGE
    |--------------------------------------------------------------------------
    | Same blade, but with editUser filled.
    */
    public function edit($employee_id)
    {
        $users = User::orderBy('employee_id', 'asc')->get();
        $editUser = User::where('employee_id', $employee_id)->firstOrFail();

        return view('admin.user-management', compact('users', 'editUser'));
    }

    /*
    |--------------------------------------------------------------------------
    | UPDATE USER
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, $employee_id)
    {
        $user = User::where('employee_id', $employee_id)->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $user->employee_id . ',employee_id',
            'email' => 'nullable|email|max:255',
            'position' => 'required|in:Admin,Cashier',
            'password' => 'nullable|string|min:6',
        ]);

        $user->name = $validated['name'];
        $user->username = $validated['username'];
        $user->email = $validated['email'] ?? null;
        $user->position = $validated['position'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return redirect()
            ->route('admin.users.index')
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

        return redirect()
            ->route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}