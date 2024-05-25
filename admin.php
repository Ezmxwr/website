<?php
session_start();
?>
<html>
<head>
<link rel="stylesheet" href="login.css">
</head>

<div class="container">
<h1>Create staff</h1>
<form method="post">
    <div class="boxes">
    <input type="text" id="username" name="username" placeholder="Enter username" required>
    </div>
    <div class="boxes">
    <input type="password" id="userpassword" name="userpassword" placeholder="Enter password" required>
    </div>
    <input type="submit" value="Create Staff" class="button">
</form>

<?php

if(!isset($_SESSION['activeUser'])){

    echo "login first";
    header("Location: login.php");
}
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require('connection.php');
    $username = $_POST['username'];
    $password = password_hash($_POST['userpassword'], PASSWORD_DEFAULT);
    $usertype_id = 2;

    try {
        $sql = "INSERT INTO users (username, userpassword, usertype_id) VALUES (:username, :password, :usertype_id)";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);
        $stmt->bindParam(':usertype_id', $usertype_id);

        $stmt->execute();

        echo "<h4 style='color:white;'>Staff created successfully!</h3>";
    } catch (PDOException $e) {
        echo "<h4 style='color:red;'>Error: " . $e->getMessage() . "</h3>";
    }

    // Close the database connection
    $db = null;
}
echo "</div>";
?>
</html>