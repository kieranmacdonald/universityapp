<?php session_start(); 
    //Set up database login credentials
    define("DB_SERVER", "localhost");
    define("DB_USER", "root");
    define("DB_PASS", "");
    define("DB_NAME", "university");

    //connect to the database
    $db = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME);
    //Confirm databse connection
    if(mysqli_connect_errno()){
        $msg = "database connection failed: ";
        $msg .= mysqli_connect_error();
        $msg .= " (" . mysqli_connect_errno() . ")";
        exit($msg);
    }

    function get_lecturers() {
        global $db;

        $sql = "SELECT * FROM lecturer ";
        $sql .= "ORDER BY lecturer_first_name, lecturer_last_name ASC";
        $result_set = mysqli_query($db, $sql);

        //confirm_result_set
        if (!$result_set){
            exit("database query failed.");
        }

        return $result_set;
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
    <title>Lecturers | WIT</title>
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
                    <li class="active"><a href="../lecturers">Lecturers</a></li>
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
                <a class="basicLink" href="./">Lecturers</a>
            </p>
            <div class="homeTitle">
                <h1>Our Lecturers</h1>
                <p class="pageDesc">Our lecturers are always ready to support you in your studies</p>
            </div>
            <div id="lecturersList">
                <?php
                    if(isset($_SESSION['message']) && ($_SESSION['message'] != '')){
                        echo '<div id="message">' . $_SESSION['message'] . '<i class="fas fa-times" onclick="closeNotification()"></i></div>';
                        unset($_SESSION['message']);
                    }
                ?>
                <?php 
                $result_set = get_lecturers();
                while($lecturer = mysqli_fetch_assoc($result_set)) { 
                        echo '<div class="lecturer">';
                        echo '<img src="../images/staff_img/' . $lecturer['lecturer_img'] . '" alt="Image of ' . $lecturer['lecturer_first_name'] . ' ' . $lecturer['lecturer_last_name'] . '" width="200">';
                        echo '<div class="lecturerTextContent">';
                        echo '<h3>' . $lecturer['lecturer_first_name'] . ' ' . $lecturer['lecturer_last_name'] . '</h3>';
                        echo '<p class="lecturerEmail">' . $lecturer['lecturer_email'] . '</p>';
                        echo '<div class="lecturerDivider"></div>';
                        echo '<p class="lecturerDesc">' . $lecturer['lecturer_desc'] . '</p>';
                        echo '</div></div>';
                } ?>
                <!-- <div class="lecturer">
                    <img src="../images/staff_img/ginny_adams.jpg" alt="Image for Computer Science" width="300">
                    <div class="lecturerTextContent">
                        <h3>Ginny Adams</h3>
                        <p class="lecturerEmail">ginny.adams@wit.ac.uk</p>
                        <div class="lecturerDivider"></div>
                        <p class="lecturerDesc">Lorem ipsum dolor sit amet consectetur adipisicing elit. Totam vel fuga illo iste quasi dolores veritatis magnam quas nemo, molestias corrupti rem magni sit! Animi aspernatur velit dolorum est nesciunt.</p>
                    </div>
                </div>
                <div class="lecturer">
                    <img src="../images/staff_img/ben_kraft.jpg" alt="Image for Computer Science" width="300">
                    <div class="lecturerTextContent">
                        <h3>Ben Kraft</h3>
                        <p class="lecturerEmail">ben.kraft@wit.ac.uk</p>
                        <div class="lecturerDivider"></div>
                        <p class="lecturerDesc">Lorem ipsum dolor sit amet consectetur adipisicing elit. Totam vel fuga illo iste quasi dolores veritatis magnam quas nemo, molestias corrupti rem magni sit! Animi aspernatur velit dolorum est nesciunt.</p>
                    </div>
                </div>
                <div class="lecturer">
                    <img src="../images/staff_img/kelly_evans.jpg" alt="Image for Computer Science" width="300">
                    <div class="lecturerTextContent">
                        <h3>Kelly Evans</h3>
                        <p class="lecturerEmail">kelly.evans@wit.ac.uk</p>
                        <div class="lecturerDivider"></div>
                        <p class="lecturerDesc">Lorem ipsum dolor sit amet consectetur adipisicing elit. Totam vel fuga illo iste quasi dolores veritatis magnam quas nemo, molestias corrupti rem magni sit! Animi aspernatur velit dolorum est nesciunt.</p>
                    </div>
                </div> -->
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
