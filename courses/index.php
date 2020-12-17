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

    function get_courses() {
        global $db;

        $sql = "SELECT * FROM course ";
        $sql .= "ORDER BY course_name ASC";
        $result_set = mysqli_query($db, $sql);

        //confirm_result_set
        if (!$result_set){
            exit("database query failed.");
        }

        return $result_set;
    }

    function redirect_to($location){
        header("Location: " . $location);
        exit;
    }

    function update_course($courseItem){
        global $db;
    
        $sql = "UPDATE course SET ";
        $sql .= "course_id='" . $courseItem['course_id'] . "', ";
        $sql .= "course_title='" . $courseItem['course_title'] . "', ";
        $sql .= "course_name='" . $courseItem['course_name'] . "', ";
        $sql .= "course_code='" . $courseItem['course_code'] . "', ";
        $sql .= "course_desc='" . $courseItem['course_desc'] . "', ";
        $sql .= "course_img='" . $courseItem['course_img'] . "' ";
        $sql .= "WHERE course_id='" . $courseItem['course_id'] . "' ";
        $sql .= "LIMIT 1";
    
        $result = mysqli_query($db, $sql);
    
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

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        //The edited form has been sent so update the database
        $courseItem = [];
        $courseItem['course_id'] = $_POST['courseId'];
        $courseItem['course_title'] = $_POST['courseTitle'] ?? '';
        $courseItem['course_name'] = $_POST['courseName'] ?? '';
        $courseItem['course_code'] = $_POST['courseCode'] ?? '';
        $courseItem['course_desc'] = $_POST['courseDesc'] ?? '';
        $courseItem['course_img'] = $_POST['courseImg'] ?? '';

        $result = update_course($courseItem);
        if($result === true){
            $_SESSION['message'] = 'Course updated successfully';
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
    <title>Courses | WIT</title>
</head>
<body>
    <script>
        function showPopup(courseId, courseTitle, courseName, courseCode, courseDesc, courseImg) {
            document.getElementById('popupForm').style.display = "block";
            setDataEdit(courseId, courseTitle, courseName, courseCode, courseDesc, courseImg);
        }

        function confirmDelete(courseId) {
            document.getElementById('popupConfirm').style.display = "block";
            setDataDelete(courseId);
        }

        function closePopup() {
            document.getElementById('popupForm').style.display = "none";
            document.getElementById('popupConfirm').style.display = "none";
        }

        function setDataDelete(courseId) {
            document.getElementById('deleteForm').action = "./delete_course.php?course_id=" + courseId;
        }

        function setDataEdit(courseId, courseTitle, courseName, courseCode, courseDesc, courseImg) {
            document.getElementById('courseId').value = courseId;
            document.getElementById('courseTitle').value = courseTitle;
            document.getElementById('courseName').value = courseName;
            document.getElementById('courseCode').value = courseCode;
            document.getElementById('courseDesc').value = courseDesc;
            document.getElementById('courseImg').value = courseImg;
            document.getElementById('courseImgPreview').src = '../images/course_img/' + courseImg;
            
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
                    <li class="active"><a href="../courses">Offered Courses</a></li>
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
                <a class="basicLink" href="./">Courses</a>
            </p>
            <div class="homeTitle">
                <h1>Undergraduate Courses</h1>
                <p class="pageDesc">Take a look at our selection of undergraduate courses for 2021 applicants</p>
            </div>
            
            <div id="courseItemList">
                <?php
                    if(isset($_SESSION['message']) && ($_SESSION['message'] != '')){
                        echo '<div id="message">' . $_SESSION['message'] . '<i class="fas fa-times" onclick="closeNotification()"></i></div>';
                        unset($_SESSION['message']);
                    }
                ?>
                <?php 
                $result_set = get_courses();
                while($course = mysqli_fetch_assoc($result_set)) { 
                    echo '<div class="courseItem">';
                    echo '<img src="../images/course_img/' . $course['course_img'] . '" alt="Image for Computer Science" width="400">';
                    echo '<div class="courseItemTextContent">';
                    if (isset($_SESSION['staff_id'])) {
                        echo '<h3>' . $course['course_title'] . ' ' . $course['course_name'] . '<span class="editBtn" onclick="showPopup(\''. $course['course_id'] .'\', \''. $course['course_title'] .'\', \''. $course['course_name'] .'\', \''. $course['course_code'] .'\', \''. $course['course_desc'] .'\', \''. $course['course_img'] .'\')"><i class="fas fa-edit"></i></span><span class="deleteBtn" onclick="confirmDelete(' . $course['course_id'] . ')"><i class="fas fa-trash"></i></span></h3>';
                    } else {
                        echo '<h3>' . $course['course_title'] . ' ' . $course['course_name'] . '</h3>';
                    }
                    echo '<p class="courseItemCode">' . $course['course_code'] . '</p>';
                    echo '<div class="divider"></div>';
                    echo '<p class="courseItemDesc">' . $course['course_desc'] . '</p>';
                    echo '<p style="text-align:center;margin-top:30px;"><a class="applyBtn" href="./">Apply Now</a></p>';
                    echo '</div></div>';
                } ?>
            </div>
        </div>
    </div>
    <?php 
        if (isset($_SESSION['staff_id'])) {
            echo '<div id="popupForm" class="modal">
            <div class="modal-content">
                <h3>Update course information</h3>
                <form action="./index.php" method="post" autocomplete="off">
                    <label for="courseId">ID</label><br>
                    <input type="text" name="courseId" id="courseId" value="" readonly /><br>
                    <label for="courseTitle">Title</label><br>
                    <input type="text" name="courseTitle" id="courseTitle" value=""/><br>
                    <label for="courseName">Name</label><br>
                    <input type="text" name="courseName" id="courseName" value=""/><br>
                    <label for="courseCode">Code</label><br>
                    <input type="text" name="courseCode" id="courseCode" value=""/><br>
                    <label for="courseDesc">Description</label><br>
                    <textarea type="text" name="courseDesc" id="courseDesc" value=""></textarea><br>
                    <label for="courseImg">Image</label><br>
                    <input type="text" name="courseImg" id="courseImg" value=""/><br>
                    <img src="" id="courseImgPreview" alt="" width="200"><br>
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
