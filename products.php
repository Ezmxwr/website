
<html>
  <head>
  <title>Home</title>
<link rel="stylesheet" href="styll.css">
  </head>
 
<body>
  <table align='center' width='500' >
    <caption><a href='viewcart.php'>View Cart</caption>
  <?php
 session_start();
 //Step 1 Connection Instance
  $db = new PDO('mysql:host=localhost;dbname=project;charset=utf8', 'root', '');
 //Step 2 for error handling
  $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
/*   $sql = "select * from products";
  $sql2 = "select * from category";
/*   $db=null; */
/*   $productsRecords = $db->query($sql);
  $categoryRecords = $db->query($sql2); */
  /* $cdetails=$categoryRecords->fetch(PDO::FETCH_ASSOC)
  extract($cdetails); */ 
  if (isset($_SESSION['activeUser'])){

     echo "<h1> welcome " . $_SESSION['activeUser'] . " we miss u <3 </h1> ";
  } else {

    echo "<h1> welcome no-name guy  nice to meet you!</h1> ";
  }
 

  echo"<div class=main>";
  echo "<ul>";
  echo"<li><a href='products.php'>Home Page</a></li>";
  echo"<li><a href='profile.php'>Profile</a></li>";
   if (isset($_SESSION['activeUser'])){
     echo"<li> <a href='logout.php'>Sign Out</a></li>";
   }else{ echo"<li> <a href='login.php'>Sign in</a></li>";}
     
  echo "<ul>";
  
  echo"<li><a href='products.php?Ct=01'>Fruits & Vegetables</a></li>";
  echo"<li><a href='products.php?Ct=02'>Meats</a></li>";
  echo"<li><a href='products.php?Ct=03'>Frozen Foods</a></li>";
  echo"<li><a href='products.php?Ct=04'>Drinks</a></li>";
  echo "<li><a href='products.php?Ct=05'>Dairy & Eggs</a></li>";
  echo"<li><a href='products.php?Ct=06'>Canned Foods</a></li>";
  echo "</ul>";
  echo "</ul>";
  echo"</div>";

  echo '<form  method="post">';
  echo ' <input type= "text" name="bar" value="" placeholder="looking for product?">';
  echo '<input type= "submit" name="search" value= "search"><br/>';
  echo '</form>';

$sql = "SELECT p.*, c.categoryName 
        FROM products p 
        JOIN category c ON p.categoryid = c.categoryid"; 
        $productsRecords = $db->query($sql);

if (isset($_REQUEST['search'])) {
 
  $keyWord = $_POST["bar"];
  
  $sql = "SELECT p.*, c.categoryName, p.picture
        FROM products p 
        JOIN category c ON p.categoryid = c.categoryid
        WHERE p.name REGEXP ? OR p.discerption REGEXP ? ";

  $stmt = $db->prepare($sql);
  $stmt->bindParam(1, $keyWord);
  $stmt->bindParam(2, $keyWord);
  $stmt->execute();
  $productsRecords = $stmt;
  $rowCount = $stmt->rowCount();
  
  if ($rowCount === 0) {
        echo "<center>";
      echo "<br><h2><b style='color:red'> No matching products found.</b></h2><br><br>";
  }       echo "</center>";
$productsRecords = $stmt;

 
} 





if (isset($_GET['Ct'])) {
  // Sanitize the input to prevent SQL injection (not shown here)
  $category = $_GET['Ct'];
  

  $sql = "SELECT p.*, c.categoryName 
          FROM products p 
          JOIN category c ON p.categoryid = c.categoryid 
          WHERE p.categoryid = :category";
  $stmt = $db->prepare($sql);
  $stmt->bindParam(':category', $category);
  $stmt->execute();
  $productsRecords = $stmt;

  if (isset($_REQUEST['search'])) {
    // Sanitize the input to prevent SQL injection (not shown here)
    $keyWord = $_POST["bar"];
    /* $sql = "SELECT * FROM products 
    WHERE name REGEXP ?"; */
    $sql = "SELECT p.*, c.categoryName, p.picture
          FROM products p 
          JOIN category c ON p.categoryid = c.categoryid
          WHERE p.name REGEXP ? OR p.discerption REGEXP ? ";
  
    $stmt = $db->prepare($sql);
    $stmt->bindParam(1, $keyWord);
    $stmt->bindParam(2, $keyWord);
    $stmt->execute();
    $productsRecords = $stmt;
  
    $rowCount = $stmt->rowCount();
  
    if ($rowCount === 0) {
          echo "<center>";
        echo "<br><h2><b style='color:red'> No matching products found.</b></h2><br><br>";
    }       echo "</center>";
  $productsRecords = $stmt;
  
   
  } 
 
} 
   

  while($details=$productsRecords->fetch(PDO::FETCH_ASSOC)){
   
   ?>
    <tr class="pro"> 
      <td>
        
         <img src='products/<?php echo $details["categoryName"];?>/<?php echo $details["picture"];?>' width='100' height='100'/>
      </td>
      <td>
        Product Name: <?php echo $details["name"];;?> <br />
        Product Price: BD <?php echo $details["price"];?><br/>
        <span class="dis">discerption:<?php echo $details["discerption"];?></br></span>
      </td>
 
      <td>
        <?php
        if ($details["qty"]==0)
          echo "<h3 style='color:red'>Out of Stock</h3>";
        else {
         ?>
          <form method='post' action='addtocart.php'>
            Quantity:
            <select name='qty'>
              <?php
                for($i=1;$i<=$details["qty"];++$i)
                  echo "<option>$i</option>\n";
               ?>
            </select><br />
            <input type='hidden' name='pid' value='<?php echo $details["pid"]; ?>' />
            <input type='submit' value='Add to Cart' />
          </form>
        <?php
      }
        ?>
      </td>
    </tr>
    <?php
  }
     ?>
  </table>
</body>
</html>
