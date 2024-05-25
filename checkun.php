<?php
require('connection.php');

if (isset($_GET['q'])) {
    $username = $_GET['q'];
    
    $sql = "SELECT * FROM users WHERE username = ?";
    $stmt = $db->prepare($sql);
    $stmt->execute([$username]);

    if ($stmt->rowCount() > 0) {
        echo "taken";
    } else {
        echo "available";
    }
}
?>
