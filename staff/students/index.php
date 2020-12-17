<?php
    session_start(); 

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

    function get_course_list() {
        global $db;

        $sql = "SELECT * FROM course";
        $course_result_set = mysqli_query($db, $sql);
        

        //confirm_result_set
        if (!$course_result_set){
            exit("database query failed.");
        }

        return $course_result_set;
    }

    function redirect_to($location){
        header("Location: " . $location);
        exit;
    }

    function is_logged_in(){
        return isset($_SESSION['staff_id']);
    }

    if(!is_logged_in()){
        //redirect to
        header("Location: " . '../../login');
    } else {
        global $db;

        $sql = "SELECT * FROM student INNER JOIN course ON student.student_course_id = course.course_id ORDER BY student.student_last_name, student.student_first_name";
        $result_set = mysqli_query($db, $sql);

        //confirm_result_set
        if (!$result_set){
            exit("database query failed.");
        }
    }

    function update_student($student){
        global $db;
    
        $sql = "UPDATE student SET ";
        $sql .= "student_id='" . $student['student_id'] . "', ";
        $sql .= "student_first_name='" . $student['student_first_name'] . "', ";
        $sql .= "student_last_name='" . $student['student_last_name'] . "', ";
        $sql .= "student_email='" . $student['student_email'] . "', ";
        $sql .= "student_address_houseno='" . $student['student_address_houseno'] . "', ";
        $sql .= "student_address_street='" . $student['student_address_street'] . "', ";
        $sql .= "student_address_postcode='" . $student['student_address_postcode'] . "', ";
        $sql .= "student_course_id='" . $student['student_course_id'] . "' ";
        $sql .= "WHERE student_id='" . $student['student_id'] . "' ";
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
        $student = [];
        $student['student_id'] = $_POST['studentId'];
        $student['student_first_name'] = $_POST['studentFirstName'] ?? '';
        $student['student_last_name'] = $_POST['studentLastName'] ?? '';
        $student['student_email'] = $_POST['studentEmail'] ?? '';
        $student['student_address_hosueno'] = $_POST['studentAddressHouseNo'] ?? '';
        $student['student_address_street'] = $_POST['studentAddressStreet'] ?? '';
        $student['student_address_postcode'] = $_POST['studentAddressPostcode'] ?? '';
        $student['student_course_id'] = $_POST['studentCourseDropdown'] ?? '';

        $result = update_student($student);
        if($result === true){
            $_SESSION['message'] = 'Student updated successfully';
            redirect_to('./');
        } else {
            echo "Error when updating database";
        }

        $student = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/c71c1cd399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../../css/style.css">
    <title>Students | WIT</title>
</head>
<body>
    <script>
        function showPopup(studentId, studentFirstName, studentLastName, studentEmail, studentAddressHouseNo, studentAddressStreet, studentAddressPostcode, studentCourseDropdown) {
            document.getElementById('popupForm').style.display = "block";
            setDataEdit(studentId, studentFirstName, studentLastName, studentEmail, studentAddressHouseNo, studentAddressStreet, studentAddressPostcode, studentCourseDropdown);
        }

        function showAddPopup() {
            document.getElementById('popupAddForm').style.display = "block";
        }

        function confirmDelete(studentId) {
            document.getElementById('popupConfirm').style.display = "block";
            setDataDelete(studentId);
        }

        function closePopup() {
            document.getElementById('popupForm').style.display = "none";
            document.getElementById('popupConfirm').style.display = "none";
            document.getElementById('popupAddForm').style.display = "none";
        }

        function setDataDelete(studentId) {
            document.getElementById('deleteForm').action = "./delete_student.php?student_id=" + studentId;
        }

        function setDataEdit(studentId, studentFirstName, studentLastName, studentEmail, studentAddressHouseNo, studentAddressStreet, studentAddressPostcode, studentCourseDropdown) {
            document.getElementById('studentId').value = studentId;
            document.getElementById('studentFirstName').value = studentFirstName;
            document.getElementById('studentLastName').value = studentLastName;
            document.getElementById('studentEmail').value = studentEmail;
            document.getElementById('studentAddressHouseNo').value = studentAddressHouseNo;
            document.getElementById('studentAddressStreet').value = studentAddressStreet;
            document.getElementById('studentAddressPostcode').value = studentAddressPostcode;
            document.getElementById('studentCourseDropdown').value = studentCourseDropdown;
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
                echo '<a class="loginLink" style="margin-left:30px;" href="../../logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>';
            } else { 
                echo '<a class="loginLink" href="../../login">Login</a>';
            } 
        ?>
    </div>
    <div id="topBar">
        <div id="navMargins">
            <a href="../../"><img class="mainLogo" src="../../images/logo.png" alt="Wales Institute of Technology Logo" height="60"></a>
            <div id="navBar">
                <ul class="mainNavBar">
                    <li><a href="../../">Home</a></li>
                    <li><a href="../../courses">Offered Courses</a></li>
                    <li><a href="../../lecturers">Lecturers</a></li>
                    <li><a href="../../news">Campus News</a></li>
                    <li><a href="../../about">About us</a></li>
                    <?php 
                        if (isset($_SESSION['staff_id'])) { 
                            echo '<li class="active"><a href="../">Staff</a></li>';
                        }
                    ?>
                </ul>
            </div>
        </div>
    </div>
    <div id="content">
        <div id="pageContentBg">
            <p class="breadcrumb">
                <a class="basicLink" href="../../">Home</a> 
                <span><i class="fas fa-chevron-right"></i></span>
                <a class="basicLink" href="../">Staff</a> 
                <span><i class="fas fa-chevron-right"></i></span>
                <a class="basicLink" href="./">Students</a>
            </p>
            <div class="homeTitle">
                <p class="lastLoginTxt">Last login: <?php echo $_SESSION['staff_last_login']; ?></p>
                <h1>Manage Students<span class="addBtn" onclick="showAddPopup()"><i class="fas fa-plus-square"></i></span></h1></h1>
                <p class="pageDesc">Check, update or add student information to the university system</p>
            </div>
            <form action="index.php" method="post"></form>
            <div id="studentSection">
                <?php
                    if(isset($_SESSION['message']) && ($_SESSION['message'] != '')){
                        echo '<div id="message">' . $_SESSION['message'] . '<i class="fas fa-times" onclick="closeNotification()"></i></div>';
                        unset($_SESSION['message']);
                    }
                ?>
                <table id="studentTable">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>E-mail</th>
                        <th>Address</th>
                        <th>Course</th>
                        <th>Actions</th>
                    </tr>
                    <?php while($student = mysqli_fetch_assoc($result_set)) {
                        echo '<tr class="studentTableRecord">';
                        echo '<td>' . $student['student_id'] . '</td>';
                        echo '<td>' . $student['student_last_name'] . ', ' . $student['student_first_name'] . '</td>';
                        echo '<td>' . $student['student_email'] . '</td>';
                        echo '<td>' . $student['student_address_houseno'] . ' ' . $student['student_address_street'] . ',<br>' . $student['student_address_city'] . ',<br>' . $student['student_address_postcode'] . '</td>';
                        echo '<td>' . $student['course_title'] . ' ' . $student['course_name'] . '</td>';
                        echo '<td><center><span class="editBtn" onclick="showPopup(\''. $student['student_id'] .'\',\''. $student['student_first_name'] .'\',\''. $student['student_last_name'] .'\',\''. $student['student_email'] .'\',\''. $student['student_address_houseno'] .'\',\''. $student['student_address_street'] .'\',\''. $student['student_address_postcode'] .'\',\''. $student['student_course_id'] .'\')"><i class="fas fa-edit"></i></span><span class="deleteBtn" onclick="confirmDelete('. $student['student_id'] .')"><i class="fas fa-trash"></i></span></center></td>';
                        echo '</tr>';
                    } ?>
                </table>
            </div>
        </div>
    </div>
    <div id="popupForm" class="modal">
        <div class="modal-content">
            <h3>Edit student details</h3>
            <form action="index.php" method="post" autocomplete="off">
                <label for="studentId">Student ID</label><br>
                <input type="text" name="studentId" id="studentId" value="" readonly /><br>
                <label for="studentFirstName">First Name</label><br>
                <input type="text" name="studentFirstName" id="studentFirstName" value=""/><br>
                <label for="studentLastName">Last Name</label><br>
                <input type="text" name="studentLastName" id="studentLastName" value=""/><br>
                <label for="studentEmail">E-mail</label><br>
                <input type="text" name="studentEmail" id="studentEmail" value=""/><br>
                <label for="studentAddressHouseNo">House Number</label><br>
                <input type="text" name="studentAddressHouseNo" id="studentAddressHouseNo" value=""/><br>
                <label for="studentAddressStreet">Street</label><br>
                <input type="text" name="studentAddressStreet" id="studentAddressStreet" value=""/><br>
                <label for="studentAddressPostcode">Postcode</label><br>
                <input type="text" name="studentAddressPostcode" id="studentAddressPostcode" value=""/><br>
                <label for="studentCourseDropdown">Course</label><br>
                <select name="studentCourseDropdown" id="studentCourseDropdown">
                    <?php 
                    $course_result_set = get_course_list();
                    while($courseList = mysqli_fetch_assoc($course_result_set)) { 
                        echo '<option value="'. $courseList['course_id'] .'">'. $courseList['course_title'] . ' ' . $courseList['course_name'] .'</option>';
                    } ?>
                </select>
                <span>
                    <input class="editBtn" type="submit" value="Save" />
                    <button class="editBtn" onclick="closePopup()" type="button">Close</button>
                </span>
            </form>
        </div>
    </div>
    <div id="popupAddForm" class="modalAdd">
        <div class="modal-content">
            <h3>Add new student</h3>
            <form action="create_student.php" method="post" autocomplete="off">
                <label for="studentFirstName">First Name</label><br>
                <input type="text" name="studentFirstName" id="studentFirstName" value=""/><br>
                <label for="studentLastName">Last Name</label><br>
                <input type="text" name="studentLastName" id="studentLastName" value=""/><br>
                <label for="studentEmail">E-mail</label><br>
                <input type="text" name="studentEmail" id="studentEmail" value=""/><br>
                <label for="studentAddressHouseNo">House Number</label><br>
                <input type="text" name="studentAddressHouseNo" id="studentAddressHouseNo" value=""/><br>
                <label for="studentAddressStreet">Street</label><br>
                <input type="text" name="studentAddressStreet" id="studentAddressStreet" value=""/><br>
                <label for="studentAddressCity">City</label><br>
                <input type="text" name="studentAddressCity" id="studentAddressCity" value=""/><br>
                <label for="studentAddressPostcode">Postcode</label><br>
                <input type="text" name="studentAddressPostcode" id="studentAddressPostcode" value=""/><br>
                <label for="studentCourseDropdown">Course</label><br>
                <select name="studentCourseDropdown" id="studentCourseDropdown">
                    <?php 
                    $courses_result_set = get_course_list();
                    while($courseList = mysqli_fetch_assoc($courses_result_set)) { 
                        echo '<option value="'. $courseList['course_id'] .'">'. $courseList['course_title'] . ' ' . $courseList['course_name'] .'</option>';
                    } ?>
                </select>
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
    </div>
    <div id="footer">
        <div class="footerLinks">
            <h4>Navigation Links</h4>
            <ul>
                <li><a href="../../">Home</a></li>
                <li><a href="../../courses">Offered Courses</a></li>
                <li><a href="../../lecturers">Lecturers</a></li>
                <li><a href="../../news">Campus News</a></li>
                <li><a href="../../about">About us</a></li>
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
