<?php
    session_start();

    function is_logged_in(){
        return isset($_SESSION['staff_id']);
    }

    if(!is_logged_in()){
        //redirect to
        header("Location: " . '../login');
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- <meta http-equiv="refresh" content="3"> -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/c71c1cd399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/style.css">
    <title>Staff | WIT</title>
</head>
<body>
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
                            echo '<li class="active"><a href="../staff">Staff</a></li>';
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
                <a class="basicLink" href="./">Staff</a>
            </p>
            <div class="homeTitle">
                <p class="lastLoginTxt">Last login: <?php echo $_SESSION['staff_last_login']; ?></p>
                <h1>Staff Area</h1>
                <p class="pageDesc">Find our range of management tools below</p>
            </div>
            <div id="staffSection">
                <div>
                    <a href="./accounts">
                        <h3><i class="fas fa-users-cog"></i> Staff accounts</h3>
                        <p>Add, edit or delete staff accounts</p>
                    </a>
                </div>
                <div>
                    <a href="./students">
                        <h3><i class="fas fa-user-graduate"></i> Students</h3>
                        <p>Check, update or add student information to the university system</p>
                    </a>
                </div>
                <div>
                    <a href="./search">
                        <h3><i class="fas fa-search"></i> Search</h3>
                        <p>Search for staff and student accounts</p>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div id="footer">
        <div class="footerLinks">
            <h4>Navigation Links</h4>
            <ul>
                <li><a href="./">Home</a></li>
                <li><a href="../courses">Offered Courses</a></li>
                <li><a href="../lecturers">Lecturers</a></li>
                <li><a href="../news">Campus News</a></li>
                <li><a href="../about">About us</a></li>
            </ul>
        </div>
        <footer>&copy; <?php echo date('Y'); ?> Wales Institute of Technology </footer>
        <!-- <span class="divider"></span> -->
    </div>
</body>
</html>
<?php
    //Disconnect from the database
    if (isset($db)){
        mysqli_close($db);
    }
?>
