<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
  <link rel="stylesheet" href="login.css">
  <meta charset="utf-8">
  <title>Sign-Up</title>
  <script>
    function checkUN(str) {
        if (str.length < 2) {
            updateMessage("Type more than 2 characters!", "red");
            return;
        }
        if (str.length > 20) {
            updateMessage("Must be less than 20 characters!", "red");
            return;
        }
        const xhttp = new XMLHttpRequest();

        xhttp.onload = function() {
            if (this.responseText.trim() === "taken") {
                updateMessage("Not Available", "red");
                document.getElementById("sign").disabled = true; // Disable Sign Up button
            } else {
                updateMessage("Available", "green");
                document.getElementById("sign").disabled = false; // Enable Sign Up button
            }
        };

        xhttp.open("GET", "checkun.php?q=" + str, true);
        xhttp.send();
    }
 
    function updateMessage(msg, color) {
        const unmsg = document.getElementById("unmsg");
        unmsg.textContent = msg;
        unmsg.className = color;
    }

    // Disable form submission if Sign Up button is disabled
    document.getElementById("signupForm").addEventListener("submit", function(event) {
        if (document.getElementById("sign").disabled) {
            event.preventDefault(); // Prevent form submission
        }
    });
  </script>
</head>
<body>
  <div class="container">
    <form id="signupForm" method="post">
      <h1>Sign Up</h1>
      <div class="boxes">
        <input type="text" name="n" id="n" placeholder="Enter Name" required> 
      </div>
      <div class="boxes">
        <input type="text" name="un" id="un" placeholder="Enter username" required onkeyup="checkUN(this.value)"> 
        <div id="unmsg"></div> <!-- Div to show availability message -->
      </div>
      <div class="boxes">
        <input type="password" name="ps" id="ps" placeholder="Enter Password" required> 
      </div>
      <div class="boxes">
        <input type="password" name="cps" id="cps" placeholder="Enter Password Again" required> 
        <button type="submit" class="buttonv" id="sign" name="sbtn">Sign Up</button>
      </div>
      <div class="sign-in-page">
                    <br><br><br><p>Already have an account?
                    <a href="create_user.php">sign up</a></p>
                    <p>Go to home page
                    <a href="products.php">Home page</a></p>
                </div>
    </form>
  </div>
</body>
</html>
