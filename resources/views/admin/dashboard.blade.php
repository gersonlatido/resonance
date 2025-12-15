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
        <form id="logout-form" method="POST" action="{{ route('admin.logout') }}">
            @csrf
            <button type="submit">Log Out</button>
        </form>
    </div>

    <div class="order-card">
        <h2>Active Orders</h2>
        <table id="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Customer</th>
                    <th>Items</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <!-- Data will be inserted here by JavaScript -->
            </tbody>
        </table>
    </div>

    <script>
        // Function to calculate the total price of each item and the grand total
        function calculateTotalPrice(cart) {
            let totalPrice = 0;
            cart.forEach(item => {
                const price = parseFloat(item.price) || 0;  // Ensure price is a number
                const quantity = parseInt(item.qty) || 1;  // Default to 1 if qty is missing
                totalPrice += price * quantity;
            });
            return totalPrice.toFixed(2); // Returns the total price with 2 decimal places
        }

        // Fetch cart data from localStorage
        const cart = JSON.parse(localStorage.getItem('cart')) || []; // Default to empty array if no cart in localStorage

        // Default values for order ID and customer
        const orderId = "ORD001";
        const customer = "TABLE 1";

        // Populate the orders table
        const ordersTableBody = document.querySelector("#orders-table tbody");

        // Loop through the cart items and create rows for the table
        cart.forEach(item => {
            const row = document.createElement('tr');

            // Order ID cell
            const orderIdCell = document.createElement('td');
            orderIdCell.textContent = orderId;  // Default Order ID
            row.appendChild(orderIdCell);

            // Customer cell
            const customerCell = document.createElement('td');
            customerCell.textContent = customer;  // Default customer
            row.appendChild(customerCell);

            // Items cell
            const itemsCell = document.createElement('td');
            itemsCell.textContent = item.name;  // Assuming the cart items have a 'name' property
            row.appendChild(itemsCell);

            // Total Price cell (calculated for each item)
            const totalCell = document.createElement('td');
            const itemTotal = (parseFloat(item.price) * parseInt(item.qty)).toFixed(2);
            totalCell.textContent = itemTotal;  // Calculated price for each item
            row.appendChild(totalCell);

            // Append the row to the table body
            ordersTableBody.appendChild(row);
        });

        // Calculate and display the grand total
        const grandTotal = calculateTotalPrice(cart);
        const grandTotalRow = document.createElement('tr');
        const grandTotalLabelCell = document.createElement('td');
        grandTotalLabelCell.textContent = "Grand Total";
        grandTotalLabelCell.colSpan = 3;
        grandTotalRow.appendChild(grandTotalLabelCell);
        
        const grandTotalCell = document.createElement('td');
        grandTotalCell.textContent = grandTotal;
        grandTotalRow.appendChild(grandTotalCell);

        // Append the grand total row to the table
        ordersTableBody.appendChild(grandTotalRow);
    </script>

</body>
</html>
