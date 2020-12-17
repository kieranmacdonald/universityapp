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

    function create_post($news_post){
        global $db;

        $sql = $db->prepare("INSERT INTO news_post (post_title,post_desc) VALUES (?,?)");
        $sql->bind_param("ss",$news_post['post_title'],$news_post['post_desc']);
        
        $result = $sql->execute();

        //$result = mysqli_query($db, $sql);
    
        if($result){
            return true;
        } else {
            //UPDATE failed
            echo mysqli_error($db);
            if(isset($db)){
                mysqli_close($db);
            }
            exit;
        }
    }

    function redirect_to($location){
        header("Location: " . $location);
        exit;
    }

    

    function get_posts() {
        global $db;

        $sql = "SELECT * FROM news_post ";
        $sql .= "ORDER BY post_date DESC";
        $result_set = mysqli_query($db, $sql);
        
        //confirm_result_set
        if (!$result_set){
            exit("database query failed.");
        }

        return $result_set;
    }

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        //The edited form has been sent so update the database
        $news_post = [];
        $news_post['post_title'] = $_POST['postTitle'];
        $news_post['post_desc'] = $_POST['postDesc'] ?? '';

        $result = create_post($news_post);
        if($result === true){
            $_SESSION['message'] = 'Posted successfully';
            redirect_to('./');
        } else {
            echo "Error when updating database";
        }

        $courseItem = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
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
    <title>News | WIT</title>
</head>
<body>
    <script>
        function showPopup() {
            document.getElementById('popupForm').style.display = "block";
        }

        function closePopup() {
            document.getElementById('popupForm').style.display = "none";
            document.getElementById('popupConfirm').style.display = "none";
        }

        function confirmDelete(postId) {
            document.getElementById('popupConfirm').style.display = "block";
            setDataDelete(postId);
        }

        function setDataDelete(postId) {
            document.getElementById('deleteForm').action = "./delete_news_post.php?post_id=" + postId;
        }

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
                    <li><a href="../lecturers">Lecturers</a></li>
                    <li class="active"><a href="../news">Campus News</a></li>
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
                <a class="basicLink" href="./">News</a>
            </p>
            <div class="homeTitle">
                <h1>Campus News
                    <?php 
                    if (isset($_SESSION['staff_id'])) {
                        echo '<span class="addBtn" onclick="showPopup()"><i class="fas fa-plus-square"></i></span></h1>';
                    } else {
                        echo '</h1>';
                    }
                    ?>
                <p class="pageDesc">Recent news from our main campus</p>
            </div>
            <div id="newsPosts">
                <?php
                    if(isset($_SESSION['message']) && ($_SESSION['message'] != '')){
                        echo '<div id="message">' . $_SESSION['message'] . '<i class="fas fa-times" onclick="closeNotification()"></i></div>';
                        unset($_SESSION['message']);
                    }
                ?>
                <?php 
                $result_set = get_posts();
                while($news_post = mysqli_fetch_assoc($result_set)) { 
                    echo '<div class="newsPost">';
                    if (isset($_SESSION['staff_id'])) {
                        echo '<h3>' . $news_post['post_title'] . '<span class="deleteBtn" onclick="confirmDelete(' . $news_post['post_id'] . ')"><i class="fas fa-trash"></i></span></h3>';
                    } else {
                        echo '<h3>' . $news_post['post_title'] . '</h3>';
                    }
                    echo '<p class="postDateTime">' . $news_post['post_date'] . '</p>';
                    echo '<div class="divider"></div>';
                    echo '<p class="postDesc">' . $news_post['post_desc'] . '</p>';
                    echo '</div>';
                } ?>
            </div>
        </div>
    </div>
    <?php 
        if (isset($_SESSION['staff_id'])) {
            echo '<div id="popupForm" class="modal">
            <div class="modal-content">
                <h3>Make a new post</h3>
                <form action="./index.php" method="post" autocomplete="off">
                    <label for="postTitle">Title</label><br>
                    <input type="text" name="postTitle" id="postTitle" value="" /><br>
                    <label for="postDesc">Content</label><br>
                    <textarea type="text" name="postDesc" id="postDesc" value=""></textarea><br>
                    <span>
                        <input class="editBtn" type="submit" value="Save" />
                        <button class="editBtn" onclick="closePopup()" type="button">Close</button>
                    </span>
                </form>
            </div>
        </div>
        <div id="popupConfirm" class="modalConfirm">
            <div class="modal-content">
                <h3>Are you sure?</h3>
                <form id="deleteForm" action="" method="post" autocomplete="off">
                    <input class="editBtn" type="submit" value="Yes" />
                    <button class="editBtn" onclick="closePopup()" type="button">No</button>
                </form>
            </div>
        </div>';
        }
    ?>
    
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
