<?php 
    session_start();
    define("DB_SERVER","localhost");
    define("DB_USER","root");
    define("DB_PASS","");
    define("DB_NAME","university");

    $db = mysqli_connect(DB_SERVER,DB_USER,DB_PASS,DB_NAME);
    if(mysqli_connect_errno()) {
        $msg = "Database connection failed: ";
        $msg .= mysqli_connect_error();
        $msg .= " (" . mysqli_connect_errno() . ")";
        exit($msg);
    }

    //Declare variables and set to nothing
    $username = '';
    $password = '';

    //Function to redirect to another web page
    function redirect_to($location){
    header("Location: " . $location);
    exit;
    }

    //Finds the member of staff based on the username entered on the form
    function find_staff_by_username($username){
        global $db;

        $sql = "SELECT * FROM staff ";
        $sql .= "WHERE staff_username='" . $username . "' ";
        $sql .= "LIMIT 1";
        $result = mysqli_query($db, $sql);

        //Confirm the result Set
        if (!$result){
            exit("database query failed.");
        }

        //Get the result set back ie one records
        $staff = mysqli_fetch_assoc($result);
        //Now I have the record stored in $staff delete and free from memoery
        mysqli_free_result($result);
        //send the staff member's record back
        return $staff;
    }

    //In order to log in we need to set SESSION variables to be tested by member pages
    function log_in_to_staff($staff) {
        $_SESSION['staff_id'] = $staff['staff_id'];
        $_SESSION['staff_last_login'] = time();
        $_SESSION['staff_username'] = $staff['staff_username'];
        return true;
    }

    function update_last_login() {
        global $db;
    
        $sql = "UPDATE staff SET ";
        $sql .= "staff_last_login=NOW() ";
        $sql .= "WHERE staff_id='" . $_SESSION['staff_id'] . "' ";
        $sql .= "LIMIT 1";
    
        $result = mysqli_query($db, $sql);
    }

    //Has any form variables been sent to the page?
    if($_SERVER['REQUEST_METHOD'] == 'POST'){
        //Get the form username and password
        $username = $_POST['staff_username'] ?? '';
        $password = $_POST['staff_password'] ?? '';

        //try and log in by first finding the staff member in the database
        $staff = find_staff_by_username($username);

        //if a staff member exists check the password matches
        if($staff){
            //this php function checks the password entered is the same as the hashed one in the database
            if(password_verify($password, $staff['staff_password'])){
                //if password matches
                update_last_login();
                log_in_to_staff($staff);
                //go the the staff home page
                redirect_to('../courses');
            } else {
                //username found but password incorrect
                $_SESSION['message'] = "Login was unsuccessful";
            }
        }
    }

    if(isset($db)) {
        mysqli_close($db);
    }
?>