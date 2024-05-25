<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <link rel="stylesheet" href="styll.css">
</head>
<body>
<?php
session_start();

if (!isset($_SESSION['activeUser'])) {
        header("Location: login.php");
    exit();
} 

if (isset($_POST['sbtn']) && $_POST['sbtn'] == 'Update All') {
    if (isset($_POST['qty']) && isset($_POST['pid'])) {
        foreach ($_POST['pid'] as $index => $pid) {
            $quantity = $_POST['qty'][$index];
            if ($quantity > 0) {
                $_SESSION['mycart'][$pid] = $quantity;
            } else {
                unset($_SESSION['mycart'][$pid]);
            }
        }
    }
    header('Location: viewcart.php?status=1');
    exit();
} else {
    try {
        require('connection.php');

        echo "<div class='receipt-container'>";
        echo "<div class='receipt-header'>";
        echo "<h1>Receipt</h1>";
        echo "</div>";
        echo "<table class='receipt-table'>";
        echo "<tr><th>Description</th><th>Price</th><th>Quantity</th><th>Total</th></tr>";

        $totalPrice = 0;

        foreach ($_SESSION['mycart'] as $pid => $qty) {
            $sql = "SELECT * FROM products WHERE pid = $pid";
            $stmt = $db->query($sql);

            if ($stmt->rowCount() > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $quantity = $_SESSION['mycart'][$pid];
                    $price = $row["price"];
                    $total = $quantity * $price;
                    $totalPrice += $total;

                    echo "<tr>";
                    echo "<td>" . $row["discerption"] . "</td>";
                    echo "<td>BD" . number_format($price, 2) . "</td>";
                    echo "<td>" . $quantity . "</td>";
                    echo "<td>BD" . number_format($total, 2) . "</td>";
                    echo "</tr>";
                }
            }
        }

        $totalPriceFormatted = number_format($totalPrice+1.5, 2);
        echo "<tr><td colspan='3' class='receipt-total'>Total Price (delivery charge 1.5 BD):</td><td class='receipt-total'>BD" . $totalPriceFormatted . "</td></tr>";
        echo "</table>";

        $db->beginTransaction();
        
        $charges = $totalPriceFormatted;
        $datetime = date('Y-m-d H:i:s');
        $sql = "INSERT INTO cusorder (userID, `datetime`, status, address, dcharge) VALUES ((SELECT userid FROM users WHERE username = ? LIMIT 1), ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([$_SESSION['activeUser'], $datetime, 'order placed', '', $charges]);

        $orderId = $db->lastInsertId();

        foreach ($_SESSION['mycart'] as $pid => $qty) {
            $price = $db->query("SELECT price FROM products WHERE pid = $pid")->fetchColumn();
            $stmt = $db->prepare("INSERT INTO orderitem (orderid, Quantity, productid, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$orderId, $qty, $pid, $price]);
        }
      
        $stmt2 = $db->prepare("UPDATE products SET qty = qty - ?
        WHERE pid = ?"); 
        
        $pid=$_POST['pid'];
        $qty=$_POST['qty'];

        for($i=0;$i<count($pid);++$i){
            
              //update the record
              $stmt2->execute(array($qty[$i], $pid[$i]));
            
          }
        $db->commit();
        unset($_SESSION['mycart']);

        echo "<div class='receipt-footer'>";
        echo "<h3 style='color:green;'>Order Placed</h3>";
        echo "<h3><a href='products.php'>View Products</a></h3>";
        echo "</div>";

    } catch (PDOException $e) {
        $db->rollBack();
        die($e->getMessage());
    }
} 
?>
</body>
</html>
