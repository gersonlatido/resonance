<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .header {
            background-color: #fff;
            padding: 20px;
            text-align: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            margin: 0;
            font-size: 32px;
            color: #333;
        }

        .header button {
            position: absolute;
            top: 20px;
            right: 20px;
            padding: 10px 20px;
            background-color: #ff7f00;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .order-card {
            background-color: #fff;
            padding: 20px;
            margin: 20px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .order-card h2 {
            margin: 0;
            font-size: 24px;
            color: #333;
        }

        .order-card table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .order-card th, .order-card td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        .order-card th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Admin Dashboard</h1>
        <form action="{{ route('admin.logout') }}" method="POST">
            @csrf
            <button type="submit">Log Out</button>
        </form>
    </div>

    <div class="order-card">
        <h2>Active Orders</h2>
        <table>
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($activeOrders as $order)
                    <tr>
                        <td>{{ $order->id }}</td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ implode(', ', $order->items->pluck('name')->toArray()) }}</td>
                        <td>{{ $order->total }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</body>
</html>
