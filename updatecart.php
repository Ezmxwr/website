<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pid']) && isset($_POST['qty'])) {
    $pid = intval($_POST['pid']);
    $qty = intval($_POST['qty']);

    if ($qty > 0) {
        $_SESSION['mycart'][$pid] = $qty;
    } else {
        unset($_SESSION['mycart'][$pid]);
    }

    echo "Cart updated";
}
?>
