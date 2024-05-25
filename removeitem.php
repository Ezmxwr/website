<?php
session_start();
unset($_SESSION['mycart'][$_GET['pid']]);
header('location:viewcart.php?status=2');
?>
