<?php
try {
    $db = new PDO('mysql:host=localhost;dbname=project;charset=utf8', 'root', '');
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
session_start();
$username = $_SESSION['activeUser'];
if(!isset($_SESSION['activeUser'])){

    echo "login first";
    header("Location: login.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Staff Page</title>
    <link rel="stylesheet" href="styll.css">
</head>
<body>
    <h2>Welcome <?php echo htmlspecialchars($username); ?></h2>

    <?php
    if (isset($_POST['update_status'])) {
        $orderId = $_POST['order_id'];
        $newStatus = $_POST['status'];
        
        $stmt = $db->prepare("UPDATE cusorder SET status = :status WHERE orderID = :orderID");
        $stmt->bindParam(':status', $newStatus);
        $stmt->bindParam(':orderID', $orderId);
        $stmt->execute();
        
        echo "<p>Order status updated successfully!</p>";
    }

    $stmt = $db->query("SELECT * FROM cusorder WHERE status != 'completed' ORDER BY datetime DESC");
    $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (isset($_POST['update_qty'])) {
        $productId = $_POST['product_id'];
        $newQty = $_POST['quantity'];
        
        try {
            $stmt = $db->prepare("UPDATE products SET qty = :qty WHERE pid = :pid");
            $stmt->bindParam(':qty', $newQty, PDO::PARAM_INT);
            $stmt->bindParam(':pid', $productId, PDO::PARAM_INT);
            $stmt->execute();
            
            echo "<p>Product quantity updated successfully!</p>";
        } catch (PDOException $e) {
            echo "<p>Error updating quantity: " . $e->getMessage() . "</p>";
        }
    }

    $stmt = $db->query("SELECT p.*, c.categoryName FROM products p JOIN category c ON p.categoryid = c.categoryid");
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <?php if (count($orders) > 0) { ?>
        <div class="up">
        <h2>Current Active Orders</h2>
        <table>
        <tr>
            <th>Order ID</th>
            <th>User ID</th>
            <th>Datetime</th>
            <th>Status</th>
            <th>Address</th>
            <th>Delivery Charge</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($orders as $order) { ?>
            <tr>
                <td><?php echo htmlspecialchars($order['orderID']); ?></td>
                <td><?php echo htmlspecialchars($order['userID']); ?></td>
                <td><?php echo htmlspecialchars($order['datetime']); ?></td>
                <td><?php echo htmlspecialchars($order['status']); ?></td>
                <td><?php echo htmlspecialchars($order['address']); ?></td>
                <td><?php echo htmlspecialchars($order['dcharge']); ?></td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="order_id" value="<?php echo $order['orderID']; ?>">
                        <select name="status">
                            <option value="acknowledged" <?php if ($order['status'] == 'acknowledged') echo 'selected'; ?>>Acknowledged</option>
                            <option value="in process" <?php if ($order['status'] == 'in process') echo 'selected'; ?>>In Process</option>
                            <option value="in transit" <?php if ($order['status'] == 'in transit') echo 'selected'; ?>>In Transit</option>
                            <option value="completed" <?php if ($order['status'] == 'completed') echo 'selected'; ?>>Completed</option>
                        </select>
                        <button type="submit" name="update_status" class="qty" >Update Status</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
        </table>
    <?php } else { ?>
        <p>No active orders found.</p>
    <?php } ?>


    <?php if (count($products) > 0) { ?>
        <div class="up">
        <h2>Product Inventory</h2>
        <table>
        <tr>
            <th>Product ID</th>
            <th>Category ID</th>
            <th>Name</th>
            <th>Price</th>
            <th>Quantity</th>
            <th>Picture</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($products as $product) { ?>
            <tr>
                <td><?php echo htmlspecialchars($product['pid']); ?></td>
                <td><?php echo htmlspecialchars($product['categoryid']); ?></td>
                <td><?php echo htmlspecialchars($product['name']); ?></td>
                <td><?php echo htmlspecialchars($product['price']); ?></td>
                <td><?php echo htmlspecialchars($product['qty']); ?></td>
                <td><img src='products/<?php echo htmlspecialchars($product["categoryName"]); ?>/<?php echo htmlspecialchars($product["picture"]); ?>' width='100' height='100'/></td>
                <td><?php echo htmlspecialchars($product['discerption']); ?></td>
                <td>
                    <form method="post" style="display:inline;">
                        <input type="hidden" name="product_id" value="<?php echo $product['pid']; ?>">
                        <input type="number" name="quantity" value="<?php echo $product['qty']; ?>" min="0">
                        <button type="submit" name="update_qty" class="qty">Update Quantity</button>
                    </form>
                </td>
            </tr>
        <?php } ?>
        </table>
        </div>
    <?php } else { ?>
        <p>No products found.</p>
    <?php } ?>

    <div class="add-product-form">
        <h2>Add New Product</h2>
        <form method="post" enctype="multipart/form-data">
            <div class="boxes">
            <label for="name">Name:</label>
            <input type="text" name="name" id="name" required>
            </div>
            <div class="boxes">
            <label for="price">Price:</label>
            <input type="number" name="price" id="price" step="0.01" required>
            </div>
            <div class="boxes">
            <label for="description">Description:</label>
            <textarea name="description" id="description" required></textarea>
            </div>
            <div class="boxes">
            <label for="qty">Quantity:</label>
            <input type="number" name="qty" id="qty" required>
            </div>
            <div class="boxes">
            <label for="picture">Picture:</label>
            <input type="file" name="picture" id="picture" accept="image/*"  required>
            </div>
            <div class="boxes">
            <label for="category_id">Category ID:</label>
            <input type="number" name="category_id" id="category_id" required>
            </div>
            <div class="boxes">
            <label for="catname">Category Name:</label>
            <input type="text" name="catname" id="catname" required>
            </div>
            <button type="submit" name="add_product">Add Product</button>
        </form>
    </div>

    <?php
    if (isset($_POST['add_product'])) {
        $name = $_POST['name'];
        $price = $_POST['price'];
        $description = $_POST['description'];
        $qty = $_POST['qty'];
        $category_id = $_POST['category_id'];
        $catname= $_POST['catname'];
    
        // Handle the file upload
        $picture = $_FILES['picture'];
        $pictureName = $picture['name'];
        $pictureTmpName = $picture['tmp_name'];
        $pictureExt = pathinfo($pictureName, PATHINFO_EXTENSION);
        $allowed = array('jpg', 'jpeg', 'png');
    
        if (in_array(strtolower($pictureExt), $allowed)) {
            $pictureNewName = uniqid('', true) . "." . $pictureExt;
            $pictureDestination = 'products/' . $catname . '/' . $pictureNewName;
    
            // Create category directory if it doesn't exist
            if (!is_dir('products/' . $catname)) {
                mkdir('products/' . $catname, 0777, true);
            }
    
            move_uploaded_file($pictureTmpName, $pictureDestination);
    
            // Insert product into the database
            try {
                $stmt = $db->prepare("INSERT INTO products (categoryid, name, price, qty, picture, discerption) VALUES (:categoryid, :name, :price, :qty, :picture, :discerption)");
                $stmt->bindParam(':categoryid', $category_id);
                $stmt->bindParam(':name', $name);
                $stmt->bindParam(':price', $price);
                $stmt->bindParam(':qty', $qty);
                $stmt->bindParam(':picture', $pictureNewName);
                $stmt->bindParam(':discerption', $description);
                $stmt->execute();
    
                echo "<p>Product added successfully!</p>";
            } catch (PDOException $e) {
                echo "<p>Error adding product: " . $e->getMessage() . "</p>";
            }
        } else {
            echo "<p>Invalid file type. Please upload a JPG, JPEG, or PNG file.</p>";
        }
    }
    ?>

</body>
</html>
