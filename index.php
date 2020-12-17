<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/c71c1cd399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="./css/style.css">
    <title>Home | WIT</title>
</head>
<body>
    <div id="miniNavBar">
        <p class="languagePara"><span>Current Time: </span><?php echo date("h:i:sa"); ?></p>
        <?php 
            if (isset($_SESSION['staff_id'])) { 
                echo '<p class="loginLink">Welcome, <strong>' . $_SESSION['staff_first_name'] . '</strong></p>';
                echo '<a class="loginLink" style="margin-left:30px;" href="./logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>';
            } else { 
                echo '<a class="loginLink" href="./login">Login</a>';
            } 
        ?>
    </div>
    <div id="topBar">
        <div id="navMargins">
            <a href="./"><img class="mainLogo" src="./images/logo.png" alt="Wales Institute of Technology Logo" height="60"></a>
            <div id="navBar">
                <ul class="mainNavBar">
                    <li class="active"><a href="./">Home</a></li>
                    <li><a href="./courses">Offered Courses</a></li>
                    <li><a href="./lecturers">Lecturers</a></li>
                    <li><a href="./news">Campus News</a></li>
                    <li><a href="./about">About us</a></li>
                    <?php 
                        if (isset($_SESSION['staff_id'])) { 
                            echo '<li><a href="./staff">Staff</a></li>';
                        }
                    ?>
                </ul>
            </div>
        </div>
    </div>
    <div id="content">
        <div id="pageContentBg">
            <p class="breadcrumb">
                <a class="basicLink" href="./">Home</a> 
                <span><i class="fas fa-chevron-right"></i></span>
            </p>
            <div class="homeTitle">
                <h1>Learn to succeed</h1>
                <p class="pageDesc">Take a look at our vast range of courses and invest in your future</p>
            </div>
            <div id="studySection">
                <img src="./images/front-page-icon.jpg" alt="Photograph of 2019 WIT Graduates" width="400">
                <div>
                    <h3>Join The Class Of 2021</h3>
                    <p>With over 30 years of experience, we ensure that our students are prepared for the successful life they hope to lead after they graduate. Want to be part of the class of 2021? Take a look through our choice of undergraduate courses.</p>
                    <a href="./courses">Take a look<span><i class="fas fa-chevron-right"></i></span></a>
                </div>
            </div>
        </div>
    </div>
    <div id="footer">
        <div class="footerLinks">
            <h4>Navigation Links</h4>
            <ul>
                <li><a href="./">Home</a></li>
                <li><a href="./courses">Offered Courses</a></li>
                <li><a href="./lecturers">Lecturers</a></li>
                <li><a href="./news">Campus News</a></li>
                <li><a href="./about">About us</a></li>
            </ul>
        </div>
        <footer>&copy; <?php echo date('Y'); ?> Wales Institute of Technology </footer>
    </div>
</body>
</html>
<?php
    //Disconnect from the database
    if (isset($db)){
        mysqli_close($db);
    }
?>
