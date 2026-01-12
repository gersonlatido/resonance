<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Show the admin login form
    public function showAdminLoginForm()
    {
        return view('admin.login');  // Admin login view
    }

    // Handle admin login
    public function adminLogin(Request $request)
    {
        // Validate the login form
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        // Find the user by username
        $user = User::where('username', $request->username)->first();

        // Check if user exists and password is correct, and if the user is an admin
        if ($user && Hash::check($request->password, $user->password) && $user->position === 'Admin') {
            Auth::login($user); // Log the user in
            return redirect()->route('admin.dashboard'); // Redirect to admin dashboard
        }

        // If login fails, return back with errors
        return back()->withErrors(['username' => 'Invalid username or password.']);
    }

    // Admin logout
    public function logout(Request $request)
{
    Auth::guard('admin')->logout();  // Log out the admin
    $request->session()->invalidate();  // Invalidate the session
    $request->session()->regenerateToken();  // Regenerate CSRF token

    // Redirect to the admin login page after logout
    return redirect()->route('admin.login');
}

}
