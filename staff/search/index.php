<?php
    session_start(); 

    function is_logged_in(){
        return isset($_SESSION['staff_id']);
    }

    if(!is_logged_in()){
        //redirect to
        header("Location: " . '../../login');
    }

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

    function redirect_to($location){
        header("Location: " . $location);
        exit;
    }

    function get_course_list() {
        global $db;

        $sql = "SELECT * FROM course";
        $course_result_set = mysqli_query($db, $sql);
        
        if (!$course_result_set){
            exit("database query failed.");
        }

        return $course_result_set;
    }

    function update_staff($staff){
        global $db;
    
        $sql = "UPDATE staff SET ";
        $sql .= "staff_username='" . $staff['staff_username'] . "', ";
        $sql .= "staff_email='" . $staff['staff_email'] . "', ";
        $sql .= "staff_first_name='" . $staff['staff_first_name'] . "', "; //having a problem here
        $sql .= "staff_last_name='" . $staff['staff_last_name'] . "' ";
        $sql .= "WHERE staff_id=" . $staff['staff_id'];
    
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

    $sql="";             //Used to store the SQL query
    $rowCount=0;         //How many rows have been found
    $posted = false;     //We want to know if the data has been posted later to display the results under the form
    $resultOutput = "";  //Build up a result string and output later

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        //get the search term and remove white space from beginning and end
        if (isset($_POST['keyword']) && $_POST['keyword'] !=""){
            $posted=true;
            //remove white space from around the search term
            $search_term = trim($_POST['keyword']);

            if ($_POST['filter'] == "staff"){
                $sql="SELECT * FROM staff WHERE ";  //This is the SQL
                $sql.="staff_first_name LIKE '%$search_term%' OR staff_last_name LIKE '%$search_term%'"; //based on the $search_term
                //Should use functions here really
                $result = mysqli_query($db, $sql);  //Run the query and get the results
                //confirm_result_set - do we have a result?
                if ($result){
                    $rowCount = mysqli_num_rows($result); //I want to display the number of records found so get the number of rows found
                    //Loop for every record found and build an output table in a variable to display later
                    $resultOutput.= "<div id=\"studentSection\"><table id=\"studentTable\"><tr><th>Username</th><th>Name</th><th>E-mail</th><th>Last Login</th><th>Actions</th></tr>";
                    while($staff = mysqli_fetch_assoc($result)){
                        $resultOutput.= '<tr class="studentTableRecord">';
                        $resultOutput.= '<td>' . $staff['staff_username'] . '</td>';
                        $resultOutput.= '<td>' . $staff['staff_first_name'] . ' ' . $staff['staff_last_name'] . '</td>';
                        $resultOutput.= '<td>' . $staff['staff_email'] . '</td>';
                        $resultOutput.= '<td>' . $staff['staff_last_login'] . '</td>';
                        $resultOutput.= '<td><center><span class="editBtn" onclick="showPopup(\''. $staff['staff_id'] .'\',\''. $staff['staff_username'] .'\',\''. $staff['staff_email'] .'\',\''. $staff['staff_first_name'] .'\',\''. $staff['staff_last_name'] .'\')"><i class="fas fa-edit"></i></span><span class="deleteBtn" onclick="confirmDelete('. $staff['staff_id'] .')"><i class="fas fa-trash"></i></span></center></td>';
                        $resultOutput.= '</tr>';
                    }
                    $resultOutput.="</div></table>";
                }
            } else if ($_POST['filter'] == "student"){
                $sql = "SELECT * FROM student INNER JOIN course ON student.student_course_id = course.course_id WHERE ";
                $sql.="student.student_first_name LIKE '%$search_term%' OR student.student_last_name LIKE '%$search_term%'";
                $result = mysqli_query($db, $sql);
                //confirm_result_set
                if ($result){
                    $rowCount = mysqli_num_rows($result);
                    $resultOutput.= "<div id=\"studentSection\"><table id=\"studentTable\"><tr><th>ID</th><th>Name</th><th>E-mail</th><th>Address</th><th>Course</th><th>Actions</th></tr>";
                    while($student = mysqli_fetch_assoc($result)){
                        $resultOutput.= '<tr class="studentTableRecord">';
                        $resultOutput.= '<td>' . $student['student_id'] . '</td>';
                        $resultOutput.= '<td>' . $student['student_last_name'] . ', ' . $student['student_first_name'] . '</td>';
                        $resultOutput.= '<td>' . $student['student_email'] . '</td>';
                        $resultOutput.= '<td>' . $student['student_address_houseno'] . ' ' . $student['student_address_street'] . ',<br>' . $student['student_address_city'] . ',<br>' . $student['student_address_postcode'] . '</td>';
                        $resultOutput.= '<td>' . $student['course_title'] . ' ' . $student['course_name'] . '</td>';
                        $resultOutput.= '<td><center><span class="editBtn" onclick="showPopup(\''. $student['student_id'] .'\',\''. $student['student_first_name'] .'\',\''. $student['student_last_name'] .'\',\''. $student['student_email'] .'\',\''. $student['student_address_houseno'] .'\',\''. $student['student_address_street'] .'\',\''. $student['student_address_postcode'] .'\',\''. $student['student_course_id'] .'\')"><i class="fas fa-edit"></i></span><span class="deleteBtn" onclick="confirmDelete('. $student['student_id'] .')"><i class="fas fa-trash"></i></span></center></td>';
                        $resultOutput.= '</tr>';
                    }
                    $resultOutput.="</div></table>";
                }
            }
        } else if (isset($_POST['staffId']) && $_POST['staffId'] !="") {
            $staff = [];
            $staff['staff_id'] = $_POST['staffId'];
            $staff['staff_username'] = $_POST['staffUsername'] ?? '';
            $staff['staff_email'] = $_POST['staffEmail'] ?? '';
            $staff['staff_first_name'] = $_POST['staffFirstName'] ?? '';
            $staff['staff_last_name'] = $_POST['staffLastName'] ?? '';

            $result = update_staff($staff);
            if($result === true){
                $_SESSION['message'] = 'Staff user updated successfully';
                redirect_to('./');
            } else {
                echo "Error when updating database";
            }

            $staff = mysqli_fetch_assoc($result);
            mysqli_free_result($result);
        } else if (isset($_POST['studentId']) && $_POST['studentId'] !="") {
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
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/c71c1cd399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../../css/style.css">
    <title>Search | WIT</title>
</head>
<body>
    <script>
        <?php 
            if ($_POST['filter'] == "staff") {
                echo '
                function showPopup(staffId, staffUsername, staffEmail, staffFirstName, staffLastName) {
                    document.getElementById(\'popupForm\').style.display = "block";
                    setDataEdit(staffId, staffUsername, staffEmail, staffFirstName, staffLastName);
                }
        
                function showAddPopup() {
                    document.getElementById(\'popupAddForm\').style.display = "block";
                }
        
                function confirmDelete(staffId) {
                    document.getElementById(\'popupConfirm\').style.display = "block";
                    setDataDelete(staffId);
                }
        
                function closePopup() {
                    document.getElementById(\'popupForm\').style.display = "none";
                    document.getElementById(\'popupConfirm\').style.display = "none";
                    document.getElementById(\'popupAddForm\').style.display = "none";
                }
        
                function setDataDelete(staffId) {
                    document.getElementById(\'deleteForm\').action = "../accounts/delete_staff.php?staff_id=" + staffId;
                }
        
                function setDataEdit(staffId, staffUsername, staffEmail, staffFirstName, staffLastName) {
                    document.getElementById(\'staffId\').value = staffId;
                    document.getElementById(\'staffUsername\').value = staffUsername;
                    document.getElementById(\'staffEmail\').value = staffEmail;
                    document.getElementById(\'staffFirstName\').value = staffFirstName;
                    document.getElementById(\'staffLastName\').value = staffLastName;
                }';
            } else if ($_POST['filter'] == "student") {
                echo '
                function showPopup(studentId, studentFirstName, studentLastName, studentEmail, studentAddressHouseNo, studentAddressStreet, studentAddressPostcode, studentCourseDropdown) {
                    document.getElementById(\'popupForm\').style.display = "block";
                    setDataEdit(studentId, studentFirstName, studentLastName, studentEmail, studentAddressHouseNo, studentAddressStreet, studentAddressPostcode, studentCourseDropdown);
                }
        
                function showAddPopup() {
                    document.getElementById(\'popupAddForm\').style.display = "block";
                }
        
                function confirmDelete(studentId) {
                    document.getElementById(\'popupConfirm\').style.display = "block";
                    setDataDelete(studentId);
                }
        
                function closePopup() {
                    document.getElementById(\'popupForm\').style.display = "none";
                    document.getElementById(\'popupConfirm\').style.display = "none";
                    document.getElementById(\'popupAddForm\').style.display = "none";
                }
        
                function setDataDelete(studentId) {
                    document.getElementById(\'deleteForm\').action = "../students/delete_student.php?student_id=" + studentId;
                }
        
                function setDataEdit(studentId, studentFirstName, studentLastName, studentEmail, studentAddressHouseNo, studentAddressStreet, studentAddressPostcode, studentCourseDropdown) {
                    document.getElementById(\'studentId\').value = studentId;
                    document.getElementById(\'studentFirstName\').value = studentFirstName;
                    document.getElementById(\'studentLastName\').value = studentLastName;
                    document.getElementById(\'studentEmail\').value = studentEmail;
                    document.getElementById(\'studentAddressHouseNo\').value = studentAddressHouseNo;
                    document.getElementById(\'studentAddressStreet\').value = studentAddressStreet;
                    document.getElementById(\'studentAddressPostcode\').value = studentAddressPostcode;
                    document.getElementById(\'studentCourseDropdown\').value = studentCourseDropdown;
                }';
            }
        ?>
    </script>
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
                <a class="basicLink" href="./">Search</a>
            </p>
            <div class="homeTitle">
                <p class="lastLoginTxt">Last login: <?php echo $_SESSION['staff_last_login']; ?></p>
                <h1>Search Tool</h1>
                <p class="pageDesc">Search for staff and student accounts</p>
            </div>
            <div id="searchBar">
                <?php
                    if(isset($_SESSION['message']) && ($_SESSION['message'] != '')){
                        echo '<div id="message">' . $_SESSION['message'] . '<i class="fas fa-times" onclick="closeNotification()"></i></div>';
                        unset($_SESSION['message']);
                    }
                ?>
                <form action="./" method="post">
                    <input type="text" name="keyword" id="keyword" placeholder="Search" autofocus="on" autocomplete="off">
                    <select name="filter" id="filter">
                        <option value="staff">Staff Account</option>
                        <option value="student">Students</option>
                    </select>
                    <input type="submit" value="Search">
                </form>
                <?php
                    if($posted){ //has the form been posted
                        if($rowCount > 0){ //Have any records been found
                            echo "<p>".$rowCount." results found</p>";
                            echo $resultOutput;
                        }else{
                            echo "No results found.";
                        }
                    }
                ?>
            </div>
        </div>
    </div>
    <?php
        if(!isset($_POST['filter'])) {
            $_POST['filter'] = "";
        }
        if ($_POST['filter'] == "staff") {
            echo '<div id="popupForm" class="modal">
                    <div class="modal-content">
                        <h3>Edit staff account details</h3>
                        <form action="./" method="post" autocomplete="off">
                            <label for="staffId">Staff ID</label><br>
                            <input type="text" name="staffId" id="staffId" value="" readonly /><br>
                            <label for="staffUsername">Username</label><br>
                            <input type="text" name="staffUsername" id="staffUsername" value=""/><br>
                            <label for="staffEmail">E-mail</label><br>
                            <input type="text" name="staffEmail" id="staffEmail" value=""/><br>
                            <label for="staffFirstName">First Name</label><br>
                            <input type="text" name="staffFirstName" id="staffFirstName" value=""/><br>
                            <label for="staffLastName">Last Name</label><br>
                            <input type="text" name="staffLastName" id="staffLastName" value=""/>
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
        } else if ($_POST['filter'] == "student") {
            echo '<div id="popupForm" class="modal">
                    <div class="modal-content">
                        <h3>Edit student details</h3>
                        <form action="./" method="post" autocomplete="off">
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
                            <select name="studentCourseDropdown" id="studentCourseDropdown">';
                            $course_result_set = get_course_list();
                            while($courseList = mysqli_fetch_assoc($course_result_set)) { 
                                echo '<option value="'. $courseList['course_id'] .'">'. $courseList['course_title'] . ' ' . $courseList['course_name'] .'</option>';
                            }
                            echo '</select>
                            <span>
                                <input class="editBtn" type="submit" value="Save" />
                                <button class="editBtn" onclick="closePopup()" type="button">Close</button>
                            </span>
                        </form>
                    </div>
                </div>';
        }
    ?>
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
