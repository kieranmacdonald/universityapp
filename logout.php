<?php
    session_start();
    
    unset($_SESSION['staff_id']);
    unset($_SESSION['staff_username']);
    unset($_SESSION['staff_last_login']);
    unset($_SESSION['staff_first_name']);

    function redirect_to($location){
        header("Location: " . $location);
        exit;
    }

    redirect_to('./');

?>
