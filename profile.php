<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Profiles</title>
    <link rel="stylesheet" href="profile.css">
</head>
<body>
    <header>
        <h1>Customer Profiles</h1>
        <a href="logout.php">Logout</a>
        <a href="products.php">Back to home page</a>
    </header>
    <main>
        <?php

 
        require 'connection.php';

        // Start the session
        session_start();

        try {
            // Check if the activeUser session variable is set
            if (!isset($_SESSION['activeUser'])) {
                throw new Exception("No active user session found.");
            }

            // Get the user name from the session
            $userName = $_SESSION['activeUser'];

            // Fetch and display order history for the user
            $orderSql = "SELECT co.orderID, co.userID, co.datetime, co.status, co.address, co.dcharge,
                                oi.oitemID, oi.Quantity, oi.productid, p.name, p.price AS product_price
                         FROM cusorder co
                         LEFT JOIN orderitem oi ON co.orderID = oi.orderid
                         LEFT JOIN products p ON oi.productid = p.pid
                         WHERE co.userID = (SELECT userid FROM users WHERE username = :userName)
                         ORDER BY co.datetime DESC";
            $orderStmt = $db->prepare($orderSql);
            $orderStmt->bindParam(':userName', $userName);
            $orderStmt->execute();

            if ($orderStmt->rowCount() > 0) {
                echo "<h3>Order History:</h3>";
                echo "<table border='1'>";
                echo "<tr><th>Order ID</th><th>Date/Time</th><th>Status</th><th>Delivery Charge</th><th>Order Details</th></tr>";
                
                $currentOrderID = null;
                $orderTotal = 0;
                $fees = 1.5;

                while ($orderRow = $orderStmt->fetch(PDO::FETCH_ASSOC)) {
                    if ($orderRow['orderID'] !== $currentOrderID) {
                        if ($currentOrderID !== null) {
                            // Close the previous order's item table and show total price
                            $totalPrice = $orderTotal + $fees;
                            echo "<tr></td><td>Total:</td><td colspan='3'>" . $totalPrice . " BD</td></tr>";
                            echo "</table></td></tr>";
                        }

                        // Start a new order row
                        $orderTotal = 0;
                        echo "<tr>";
                        echo "<td>" . $orderRow["orderID"] . "</td>";
                        echo "<td>" . $orderRow["datetime"] . "</td>";
                        echo "<td>" . $orderRow["status"] . "</td>";
                        echo "<td>" . $fees . "</td>";
                        echo "<td>";
                        // Start a new nested table for order items
                        echo "<table border='1'>";
                        echo "<tr><th>Product Name</th><th>Quantity</th><th>Product ID</th><th>Price</th></tr>";
                        $currentOrderID = $orderRow['orderID'];
                    }

                    // Print the current order item
                    echo "<tr>";
                    echo "<td>" . $orderRow["name"] . "</td>";
                    echo "<td>" . $orderRow["Quantity"] . "</td>";
                    echo "<td>" . $orderRow["productid"] . "</td>";
                    echo "<td>" . $orderRow["product_price"] . " BD</td>";
                    echo "</tr>";

                    // Calculate the total for the current order
                    $orderTotal += $orderRow["product_price"] * $orderRow["Quantity"];
                }

                // Close the last order's item table and show total price
                $totalPrice = $orderTotal + $fees;
                echo "<tr><td>Total:</td><td colspan='3'>" . $totalPrice . " BD</td></tr>";
                echo "</table></td></tr>";
                echo "</table>";
            } else {
                echo "<p>No order history found.</p>";
            }
        }  catch (Exception $e) {
            error_log("Exception - Message: " . $e->getMessage());
           
            header("Location: login.php");
        }
        ?>
    </main>
</body>
</html>
