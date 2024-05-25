<!DOCTYPE html>
<html>
<head>
    <title>Count Form Elements</title>
</head>
<body>

<!-- Example forms for demonstration -->
<form>
    <input type="radio" name="radio1">
    <input type="radio" name="radio1" checked>
    <input type="radio" name="radio2" checked>
    <img src="image1.jpg" alt="image1">
</form>

<form>
    <input type="radio" name="radio3">
    <input type="radio" name="radio4" checked>
    <input type="radio" name="radio5" checked>
    <img src="image2.jpg" alt="image2">
    <img src="image3.jpg" alt="image3">
</form>

<!-- Result div -->
<div id="result"></div>

<script>
    // Function to count checked radio buttons and images
    function countFormElements() {
        // Get all forms in the document
        const forms = document.forms;
        let checkedRadioCount = 0;
        let imageCount = 0;

        // Iterate through each form
        for (let i = 0; i < forms.length; i++) {
            const form = forms[i];
            
            // Get all input elements in the form
            const elements = form.elements;
            for (let j = 0; j < elements.length; j++) {
                const element = elements[j];

                // Count checked radio buttons
                if (element.type === 'radio' && element.checked) {
                    checkedRadioCount++;
                }
            }

            // Get all image elements in the form
            const images = form.getElementsByTagName('img');
            imageCount += images.length;
        }

        // Display the result in the div with ID 'result'
        const resultDiv = document.getElementById('result');
        resultDiv.innerHTML = `Number of checked radio buttons: ${checkedRadioCount}<br>Number of images: ${imageCount}`;
    }

    // Call the function to count and display the results
    countFormElements();
</script>
<!-- --------------------------------------------///////////-----------Q2
 -->



 <!DOCTYPE html>
<html>
<head>
    <title>Traffic Light</title>
    <style>
        .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100px;
            background-color: black;
            padding: 10px;
        }
        .container div {
            width: 50px;
            height: 50px;
            margin: 10px 0;
            border-radius: 50%;
        }
    </style>
</head>
<body>

<div class="container">
    <div id="redLight" style="background-color: darkred;"></div>
    <div id="yellowLight" style="background-color: #FFFF99;"></div>
    <div id="greenLight" style="background-color: darkgreen;"></div>
</div>
<button onClick="startLights()">Start Traffic</button>
<button onClick="stopLights()">Stop Traffic</button>

<script>
    let TID;
    let lightsID = ['redLight', 'yellowLight', 'greenLight'];
    let active = 0;
    let lightONColors = ['red', 'yellow', 'lime'];
    let lightOFFColors = ['darkred', '#FFFF99', 'darkgreen'];

    function changeLight() {
        // Turn off all lights
        for (let i = 0; i < lightsID.length; i++) {
            document.getElementById(lightsID[i]).style.backgroundColor = lightOFFColors[i];
        }
        // Turn on the active light
        document.getElementById(lightsID[active]).style.backgroundColor = lightONColors[active];
        // Update active light index
        active = (active + 1) % lightsID.length;
    }

    function startLights() {
        changeLight(); // Start immediately with the first light
        TID = setInterval(changeLight, 3000); // Change light every 3 seconds
    }

    function stopLights() {
        clearInterval(TID); // Stop the timer
    }
</script>
<!-- ---------------------------//////////-------------/////////--------///////-----Q3 -A
 -->


 <!DOCTYPE html>
<html>
<head>
    <title>Image Game</title>
    <script>
        // Declare necessary variables
        let images = ['A.jpg', 'B.jpg', 'W.jpg', 'Z.jpg'];
        let currentIndex = 0;
        let intervalId;
        const imageElement = document.getElementById('myPic');

        // Function to change the image
        function changeImage() {
            currentIndex = (currentIndex + 1) % images.length;
            imageElement.src = images[currentIndex];
        }

        // Function to stop the image change
        function stop() {
            clearInterval(intervalId);
            if (imageElement.src === 'W.jpg') {
                alert("You win!");
            }
        }

        // Function to resume the image change
        function resume() {
            changeImage(); // Immediately change the image
            intervalId = setInterval(changeImage, 250); // Restart the interval
        }

        // Start the image change when the page is loaded
        window.onload = function() {
            intervalId = setInterval(changeImage, 250);
        }
    </script>
</head>

    <img src='A.jpg' id='myPic' width='100' height='100' onMouseOver='stop()' onMouseOut='resume()' />

<!-- -----------------------------------############# Q3 part B -->

<?php

// Regular expressions
$regex_student_id = '/^(20[0-5][0-9]([0-9]{4}))$/';
$regex_number_range = '/^(25[0-9]|2[0-4][0-9]|[01]?[0-9]?[0-9])$/';
$regex_serial_code = '/^[A-Z][a-z]{2}[a-zA-Z]{3}[0-9]{6}$/';

// Example usage with HTTP POST input
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $serial = $_POST['serial'];

    // Validate student ID
    if (preg_match($regex_student_id, $serial)) {
        echo "Student ID is valid: $serial<br>";
    } else {
        echo "Invalid Student ID: $serial<br>";
    }

    // Validate number in range
    if (preg_match($regex_number_range, $serial)) {
        echo "Number in range is valid: $serial<br>";
    } else {
        echo "Invalid Number in range: $serial<br>";
    }

    // Validate serial code
    if (preg_match($regex_serial_code, $serial)) {
        echo "Serial code is valid: $serial<br>";
    } else {
        echo "Invalid Serial code: $serial<br>";
    }
}
?>
<!-- -----------------------------------############# Q4 -->


<?php
session_start();
require_once('Connection.php');

// Check if the user is logged in
if (!isset($_SESSION['activeUserID'])) {
    die("User is not logged in.");
}

// Retrieve the active user ID
$activeUserID = $_SESSION['activeUserID'];

// Retrieve the product details from the session
if (!isset($_SESSION[$activeUserID])) {
    die("No products in the shopping cart.");
}

$products = $_SESSION[$activeUserID];

// Begin transaction
$db->beginTransaction();

try {
    // Insert into Orders table
    $orderTotal = 0;
    $orderDate = date('Y-m-d H:i:s');
    
    foreach ($products as $productName => $productDetails) {
        $quantity = $productDetails['Quantity'];
        $unitPrice = $productDetails['UnitPrice'];
        $subtotal = $quantity * $unitPrice;
        $orderTotal += $subtotal;
    }

    $insertOrderStmt = $db->prepare("INSERT INTO Orders (userID, total, orderDate) VALUES (?, ?, ?)");
    $insertOrderStmt->execute([$activeUserID, $orderTotal, $orderDate]);
    $orderID = $db->lastInsertId(); // Retrieve the last inserted orderID
    
    // Insert into OrderItems table
    $insertItemStmt = $db->prepare("INSERT INTO OrderItems (orderID, productName, quantity, unitPrice) VALUES (?, ?, ?, ?)");

    foreach ($products as $productName => $productDetails) {
        $quantity = $productDetails['Quantity'];
        $unitPrice = $productDetails['UnitPrice'];
        
        $insertItemStmt->execute([$orderID, $productName, $quantity, $unitPrice]);
    }

    // Commit the transaction
    $db->commit();
    
    // Clear the shopping cart session
    unset($_SESSION[$activeUserID]);
    
    echo "Order placed successfully!";
} catch (PDOException $ex) {
    // Rollback the transaction on error
    $db->rollback();
    echo "An error occurred: " . $ex->getMessage();
}
?>
<!-- ...................................Q5 a -->



<?php
// Connect to the database
include("connection.php");

// Query to retrieve all categories
$query = "SELECT * FROM categories";
$stmt = $db->prepare($query);
$stmt->execute();
$categories = $stmt->fetchAll();

// Display categories in a grid view
echo "<table>";
foreach ($categories as $category) {
    echo "<tr>";
    echo "<td><img src='" . $category['picture'] . "' alt='" . $category['name'] . "'></td>";
    echo "<td>" . $category['name'] . "</td>";
    echo "<td>" . $category['views'] . "</td>";
    echo "</tr>";
}
echo "</table>";
?>


<?php
// Connect to the database
include("connection.php");

// Get the category ID from the URL
$categoryId = $_GET['id'];

// Query to retrieve all blogs in the selected category
$query = "SELECT * FROM blogs WHERE category_id = :categoryId";
$stmt = $db->prepare($query);
$stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
$stmt->execute();
$blogs = $stmt->fetchAll();

// Increment the category views by 1
$query = "UPDATE categories SET views = views + 1 WHERE id = :categoryId";
$stmt = $db->prepare($query);
$stmt->bindValue(':categoryId', $categoryId, PDO::PARAM_INT);
$stmt->execute();

// Display the blogs in the selected category
echo "<h2>Blogs in Category " . $categoryId . "</h2>";
foreach ($blogs as $blog) {
    echo "<div class='blog'>";
    echo "<h3>" . $blog['title'] . "</h3>";
    echo "<p>" . substr($blog['content'], 0, 100) . "...</p>";
    echo "</div>";
}
?>

<!-- ........................Q5 B -->



<?php
// Connect to the database
$conn = mysqli_connect("hostname", "username", "password", "database_name");

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Get the input values
$title = $_POST["title"];
$content = $_POST["content"];
$category = $_POST["category"];
$pic = $_FILES["pic"]["name"];
$pic_type = $_FILES["pic"]["type"];
$pic_size = $_FILES["pic"]["size"];

// Validate the inputs
if (strlen($title) < 4 || strlen($title) > 100) {
    echo "Invalid title length.";
    exit;
}

if (strlen($content) < 50 || strlen($content) > 1000) {
    echo "Invalid content length.";
    exit;
}

if (!preg_match("/^[a-zA-Z0-9 ]+$/", $title)) {
    echo "Invalid title format.";
    exit;
}

if (!preg_match("/^[a-zA-Z0-9 ]+$/", $content)) {
    echo "Invalid content format.";
    exit;
}

if ($pic_type != "image/gif" && $pic_type != "image/jpeg") {
    echo "Invalid picture type.";
    exit;
}

if ($pic_size > 1000000) {
    echo "Picture too large.";
    exit;
}

// Save the picture file
move_uploaded_file($_FILES["pic"]["tmp_name"], "images/" . $pic);

// Add the new blog record to the database
$sql = "INSERT INTO blogs (title, content, category, pic) VALUES ('$title', '$content', '$category', '$pic')";
mysqli_query($conn, $sql);

// Close the database connection
mysqli_close($conn);

// Redirect to the home page
header("Location: index.php");
exit;
?>

<!-- .......................Q5 C -->

<?php
session_start();

// Check if the SESSION variable exists
if (isset($_SESSION["Categories"])) {
    // Prepare the insert query
    $insertQuery = "INSERT INTO categories (cat_name, pic_url) VALUES (:cat_name, :pic_url)";
    $stmt = $conn->prepare($insertQuery);

    // Bind the parameters
    $stmt->bindParam(":cat_name", $catName);
    $stmt->bindParam(":pic_url", $picUrl);

    // Iterate over the SESSION array and extract the categories
    foreach ($_SESSION["Categories"] as $key => $value) {
        $catName = $value["CatName"];
        $picUrl = $value["PicUrl"];

        // Execute the insert query
        $stmt->execute();
    }

    // Commit the transaction
    $conn->commit();
} else {
    // Display error message if the SESSION variable doesn't exist
    echo "Error: SESSION variable does not exist!";
}
?>









































</body>
</html>

