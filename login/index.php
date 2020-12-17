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

    $_SESSION['loggedIn'] = false;
    if ($_SESSION['loggedIn']) {
        redirect_to('../courses');
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
        $_SESSION['staff_last_login'] = $staff['staff_last_login'];
        $_SESSION['staff_username'] = $staff['staff_username'];
        $_SESSION['staff_first_name'] = $staff['staff_first_name'];
        return true;
    }

    function update_last_login() {
        global $db;
    
        $sql = "UPDATE staff SET ";
        $sql .= "staff_last_login=NOW() ";
        $sql .= "WHERE staff_id='" . $_SESSION['staff_id'] . "' ";
        $sql .= "LIMIT 1";
    
        $result = mysqli_query($db, $sql);
        if (!$result){
            echo 'query failed';
        }
    }

    //Has any form variables been sent to the page?
    if($_SERVER['REQUEST_METHOD'] == 'POST'){

        //Get the form username and password
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';

        //try and log in by first finding the staff member in the database
        $staff = find_staff_by_username($username);

        //if a staff member exists check the password matches
        if($staff){
            //this php function checks the password entered is the same as the hashed one in the database
            if(password_verify($password, $staff['staff_password'])){
                //if password matches
                log_in_to_staff($staff);
                update_last_login();
                //go the the staff home page
                $_SESSION['loggedIn'] = true;
                redirect_to('../');
            } else {
                //username found but password incorrect
                $_SESSION['message'] = 'Incorrect login details';
            }
        }
    }

    function is_logged_in(){
        return isset($_SESSION['staff_id']);
    }

    if(is_logged_in()){
        //redirect to
        header("Location: " . '../');
    }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/c71c1cd399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/style.css">
    <title>Home | WIT</title>
</head>
<body>
    <script>
        function closeNotification() {
            document.getElementById('message').style.display = "none";
        }
    </script>
    <div id="miniNavBar">
        <p class="languagePara"><span>Current Time: </span><?php echo date("h:i:sa"); ?></p>
        <?php 
            if (isset($_SESSION['staff_id'])) { 
                echo '<p class="loginLink">Welcome, <strong>' . $_SESSION['staff_first_name'] . '</strong></p>';
                echo '<a class="loginLink" style="margin-left:30px;" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>';
            } else { 
                echo '<a class="loginLink" href="./login">Login</a>';
            } 
        ?>
    </div>
    <div id="topBar">
        <div id="navMargins">
            <a href="../"><img class="mainLogo" src="../images/logo.png" alt="Wales Institute of Technology Logo" height="60"></a>
            <div id="navBar">
                <ul class="mainNavBar">
                    <li><a href="../">Home</a></li>
                    <li><a href="../courses">Offered Courses</a></li>
                    <li><a href="../lecturers">Lecturers</a></li>
                    <li><a href="../news">Campus News</a></li>
                    <li><a href="../about">About us</a></li>
                    <?php 
                        if (isset($_SESSION['staff_id'])) { 
                            echo '<li><a href="../staff">Staff</a></li>';
                        }
                    ?>
                </ul>
            </div>
        </div>
    </div>
    <div id="content">
        <div id="pageContentBg">
            <p class="breadcrumb">
                <a class="basicLink" href="../">Home</a> 
                <span><i class="fas fa-chevron-right"></i></span>
                <a class="basicLink" href="./">Login</a> 
            </p>
            <div class="homeTitle">
                <h1>Staff Login</h1>
                <p class="pageDesc">Use your university staff credentials to login below</p>
            </div>
            <div id="loginSection">
                <?php
                    if(isset($_SESSION['message']) && ($_SESSION['message'] != '')){
                        echo '<div id="message">' . $_SESSION['message'] . '<i class="fas fa-times" onclick="closeNotification()"></i></div>';
                        unset($_SESSION['message']);
                    }
                ?>
                <div class="loginFormSection">
                    <img src="../images/login-page-icon.jpg" alt="Photograph of the Main WIT Campus" width="400">
                    <div>
                        <form action="index.php" method="post">
                            <label for="username">Username</label><br/>
                            <input type="text" name="username" value="" size="30" autocomplete="off" autofocus />
                            <br/>
                            <label for="password">Password</label><br/>
                            <input type="password" name="password" value="" size="30" />
                            <br/>
                            <input type="submit" name="submit" value="Login" />
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="footer">
        <div class="footerLinks">
            <h4>Navigation Links</h4>
            <ul>
                <li><a href="../">Home</a></li>
                <li><a href="../courses">Offered Courses</a></li>
                <li><a href="../lecturers">Lecturers</a></li>
                <li><a href="../news">Campus News</a></li>
                <li><a href="../about">About us</a></li>
            </ul>
            <footer>&copy; <?php echo date('Y'); ?> Wales Institute of Technology </footer>
        </div>
        <!-- <span class="divider"></span> -->
    </div>
</body>
</html>
<?php 
    if(isset($db)) {
        mysqli_close($db);
    }
    if(isset($_SESSION['message'])) {
        unset($_SESSION['message']);
    }
?>