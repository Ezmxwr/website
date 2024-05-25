<html>
<head>
    <link rel="stylesheet" href="styll.css">
    <title>view</title>
    <script type="text/javascript">
        function updateCart(pid, qty) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "updatecart.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
               
            };
            xhr.send("pid=" + pid + "&qty=" + qty);
        }
    </script>
</head>
<?php
session_start();
echo "<div class=main>";
echo "<ul>";
echo "<li><a href='products.php'>Home Page</a></li>";
echo "</ul>";
echo"</div>";
if (!isset($_SESSION['mycart']) || empty($_SESSION['mycart'])) {
    die("<h3>Your Cart is Empty</h3>");
}

try {
    require('connection.php');
} catch (PDOException $e) {
    die($e->getMessage());
}
?>
<body>
    <?php
    if (isset($_GET['status'])) {
        switch ($_GET['status']) {
            case 1:
                echo "<h3 style='color:red;text-align:center'>Cart Updated</h3>";
                break;
            case 2:
                echo "<h3 style='color:red;text-align:center'>Cart Item Removed</h3>";
                break;
        }
    }
    ?>
    <table class="view">
        <tr>
            <th>Picture</th>
            <th>Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Remove</th>
        </tr>
        <form id='cartForm' method='post' action='processcart.php'>
            <?php
            foreach ($_SESSION['mycart'] as $pid => $qty) {
                try {
                    $sql = "SELECT p.*, c.categoryName 
                            FROM products p 
                            JOIN category c ON p.categoryid = c.categoryid 
                            WHERE pid = $pid";

                    $productsRecord = $db->query($sql);
                } catch (PDOException $e) {
                    die($e->getMessage());
                }
                if ($details = $productsRecord->fetch()) {
                    echo "<tr>";
                    ?>
                    <td>
                        <img src='products/<?php echo $details["categoryName"];?>/<?php echo $details["picture"];?>' width='100' height='100'/>
                    </td>
                    <?php
                    echo "<td>" . $details['name'] . "</td>";
                    echo "<td>" . $details['price'] . "</td>";
                    echo "<td>";
                    echo "<select name='qty[]' onchange='updateCart(" . $pid . ", this.value)'>";
                    for ($i = 1; $i <= $details['qty']; ++$i) {
                        echo "<option value='$i' ";
                        if ($i == $qty) echo "selected ";
                        echo ">$i</option>";
                    }
                    echo "</select>";
                    echo "<input type='hidden' name='pid[]' value='$pid' />";
                    echo "</td>";
                    echo "<td><a href='removeitem.php?pid=$pid'>Remove?</a></td>";
                    echo "</tr>";
                }
            }
            $db = null;
            ?>
            <tr>
                <th colspan='5'> <!-- /*new*/ -->
                    <?php  if (!isset($_SESSION['activeUser'])){
                        echo "<a href='login.php'>Please log in to place an order </a></td>";} else
                    echo "<input type='submit' name='sbtn' value='Place Order' />";
                                                                ?>
                </th>
            </tr>
        </form>
    </table>
</body>
</html>
