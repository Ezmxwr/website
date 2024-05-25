<?php

try {
  $db = new PDO('mysql:host=localhost;dbname=project;charset=utf8', 'root', '');
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); 
} catch (PDOException $e) {
  die("Connection failed: " . $e->getMessage());
}
  session_start();
  if (isset($_SESSION['activeUser']))
      session_unset(); //this will remove all session keys
?>
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <title>Sign-In</title>
    <link rel="stylesheet" href="login.css">
  </head>
  <body>

  <?php
  if (isset($_POST['sbtn']) ){
    $uname = $_POST['un']; 
    $pass = $_POST['ps'];
  
    try {
        require('connection.php');
        $sql = "select * from users
                  where username = '$uname'";

        $result = $db->query($sql);
        if ($row = $result->fetch()){
          $pass = trim($pass);
          if($uname=='admin' && $pass == 'abc123'){
            $_SESSION['activeUser'] = $uname;
            echo "SDSDSDSD";
            header("location: admin.php");
           exit() ;}
           
        
          if (password_verify($pass, $row['userpassword'])) {
              $_SESSION['activeUser'] = $uname;
              if ($row['usertype_id'] == 2) {
                header('location: staff.php');
            } else {
                header('location: products.php');
            }
            die();
        } else {
            echo "<h3>Invalid password</h3>";
        }
    } else {
        echo "<h3>Invalid Username</h3>";
    }
    $db = null;
} catch (PDOException $e) {
    die($e->getMessage());
}
}

  ?>
      <div class="container">
            <form method="post">
          
            <div class="boxes">
            <i class='bx bx-user'></i>
            <input type="text" name="un" id="un" onkeyup= "checkUN(this.value)" pattern=".{3,20}" placeholder="username length must be from 3 to 20" required> <br />
            </div>
           <div class="boxes">
            <i class='bx bxs-lock'></i>
            <input type="password" name="ps" id="ps" placeholder="Enter Password" required> <br />
           </div>
           <div class="Remember">
           <label><input type="checkbox"> Remember me</label></div>
           <button type="submit" name="sbtn" class="button">Sign in</button>
                <div class="sign-in-page">
                    <p>new to our website?
                    <a href="create_user.php">sign up</a></p><br>
                    <p>Go to home page
                    <a href="products.php">Home page</a></p>
                </div>
            </form>
        </div>


  </body>
</html>
