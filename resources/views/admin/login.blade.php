<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-container {
            background-color: #fff;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 40px;
            border-radius: 10px;
            width: 350px;
            text-align: center;
        }

        .login-container img {
            width: 200px; /* Logo size */
            margin-bottom: 20px;
        }

        .login-container h2 {
            font-size: 24px;
            margin-bottom: 30px;
            color: #333;
        }

        .login-container input {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 2px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }

        .login-container input:focus {
            outline: none;
            border-color: #ff7f00;
        }

        .login-container button {
            width: 100%;
            padding: 14px;
            background-color: #ff7f00;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .login-container button:hover {
            background-color: #f98e2f;
        }

        .login-container .error-message {
            color: red;
            margin-bottom: 20px;
            font-size: 14px;
        }
    </style>
</head>
<body>

    <div class="login-container">
        <img src="{{ asset('/images/logo-image.png')}}" alt="Logo">
        <h2>Login</h2>

        <!-- Login Form -->
        <form action="{{ route('admin.login') }}" method="POST">
            @csrf
            <input type="text" id="username" name="username" placeholder="Enter your username" value="{{ old('username') }}" required>
            <input type="password" id="password" name="password" placeholder="Enter your password" required>
            <button type="submit">Login</button>
        </form>
    </div>

</body>
</html>
