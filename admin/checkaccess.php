<?php

// Check only when inside the admin area
$logged = false;
if (basename($_SERVER['PHP_SELF']) != "login.php") {
    $login = new imPrivateArea();
    if ($login->checkAccess("admin/" . basename($_SERVER['PHP_SELF'])) !== 0) {
        $login->savePage();
        if (isset($_SERVER['HTTP_REFERER']) && basename($_SERVER['HTTP_REFERER']) == "login.php") {
            header("Location: login.php?error");
            exit;
        } else {
            header("Location: login.php");
            exit;
        }
    } else {
        $logged = true;
    }
}

// End of file checkaccess.php
