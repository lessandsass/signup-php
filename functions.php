<?php

function checkLogin($connection) {
    if (isset($_SESSION['user_id'])) {
        $id = $_SESSION['user_id'];
        $query = "SELECT * FROM users WHERE user_id = '$id' LIMIT 1";
        
        $result = mysqli_query($connection, $query);
        if ($result && mysqli_num_rows($result) > 0) {
            $userData = mysqli_fetch_assoc($result);
            return $userData;
        }
    }
    // Redirect to login if not logged in
    header("Location: login.php");
    die;
}


