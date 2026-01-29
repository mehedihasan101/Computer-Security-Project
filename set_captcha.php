<?php
session_start();
if (isset($_GET['code'])) {
    $_SESSION['captcha_code'] = $_GET['code'];
}
?>
