<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- <meta http-equiv="refresh" content="3"> -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/c71c1cd399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../css/style.css">
    <title>About | WIT</title>
</head>
<body>
    <div id="miniNavBar">
        <p class="languagePara"><span>Current Time: </span><?php echo date("h:i:sa"); ?></p>
        <?php 
            if (isset($_SESSION['staff_id'])) { 
                echo '<p class="loginLink">Welcome, <strong>' . $_SESSION['staff_first_name'] . '</strong></p>';
                echo '<a class="loginLink" style="margin-left:30px;" href="../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>';
            } else { 
                echo '<a class="loginLink" href="../login">Login</a>';
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
                    <li class="active"><a href="../about">About us</a></li>
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
                <a class="basicLink" href="./">About</a>
            </p>
            <div class="homeTitle">
                <h1>About Us</h1>
                <p class="pageDesc">Learn more about the university and how to apply</p>
            </div>
            <div id="aboutSection">
                <p>Founded in 1994, the Wales Institute of Technology has been a global leader in researching the most important, groundbreaking technologies of our time.</p>
                <p>Apply to the Wales Institute of Technology through our <a href="../courses">courses page</a></p>
                <img class="mainLogo" src="../images/logo.png" alt="Wales Institute of Technology Logo" height="40">
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
            <footer>&copy; <?php echo date('Y'); ?> Wales Institute of Technology </footer>
        </div>
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
