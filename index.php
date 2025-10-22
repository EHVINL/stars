<?php
session_start();

if (isset($_SESSION['user_id'])) {
    header("Location: includes/home.php");
} else {
    header("Location: includes/home_public.html");
}
exit();
?>