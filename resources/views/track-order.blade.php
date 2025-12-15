<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Order</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <style>
        body {
            font-family: 'Figtree', sans-serif;
            background-color: #EFEFEF;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .track-card {
            background-color: #fff;
            max-width: 400px;
            width: 90%;
            padding: 30px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }

        .track-card h2 {
            color:  #f7b413;
            margin-bottom: 15px;
        }

        .track-card p {
            color: #555;
            font-size: 16px;
            margin-bottom: 20px;
        }

        .track-card img {
            width: 350px;
            margin-bottom: 20px;
        }

        .status {
            font-weight: bold;
            color: #f7b413;
            font-size: 18px;
        }
    </style>
</head>
<body>

<div class="track-card">
    <h2>Tracking Order</h2>
    <p>Your order will be at your table in <strong style=" color:#f7b413">30:00</strong></p>

    <img src="{{ asset('images/logo-image.png') }}" alt="Logo">

    <p>Status: <span class="status">Preparing</span></p>
</div>

</body>
</html>
