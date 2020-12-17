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

    function get_staff_list() {
        global $db;

        $sql = "SELECT * FROM staff";
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

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        //The edited form has been sent so update the database
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
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://kit.fontawesome.com/c71c1cd399.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="../../css/style.css">
    <title>Staff Accounts | WIT</title>
</head>
<body>
    <script>
        function showPopup(staffId, staffUsername, staffEmail, staffFirstName, staffLastName) {
            document.getElementById('popupForm').style.display = "block";
            setDataEdit(staffId, staffUsername, staffEmail, staffFirstName, staffLastName);
        }

        function showAddPopup() {
            document.getElementById('popupAddForm').style.display = "block";
        }

        function confirmDelete(staffId) {
            document.getElementById('popupConfirm').style.display = "block";
            setDataDelete(staffId);
        }

        function closePopup() {
            document.getElementById('popupForm').style.display = "none";
            document.getElementById('popupConfirm').style.display = "none";
            document.getElementById('popupAddForm').style.display = "none";
        }

        function setDataDelete(staffId) {
            document.getElementById('deleteForm').action = "./delete_staff.php?staff_id=" + staffId;
        }

        function setDataEdit(staffId, staffUsername, staffEmail, staffFirstName, staffLastName) {
            document.getElementById('staffId').value = staffId;
            document.getElementById('staffUsername').value = staffUsername;
            document.getElementById('staffEmail').value = staffEmail;
            document.getElementById('staffFirstName').value = staffFirstName;
            document.getElementById('staffLastName').value = staffLastName;
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
                <a class="basicLink" href="./">Accounts</a>
            </p>
            <div class="homeTitle">
                <p class="lastLoginTxt">Last login: <?php echo $_SESSION['staff_last_login']; ?></p>
                <h1>Manage Staff Accounts<span class="addBtn" onclick="showAddPopup()"><i class="fas fa-plus-square"></i></span></h1></h1>
                <p class="pageDesc">Add, edit or delete staff accounts</p>
            </div>
            <div id="studentSection">
                <?php
                    if(isset($_SESSION['message']) && ($_SESSION['message'] != '')){
                        echo '<div id="message">' . $_SESSION['message'] . '<i class="fas fa-times" onclick="closeNotification()"></i></div>';
                        unset($_SESSION['message']);
                    }
                ?>
                <table id="studentTable">
                    <tr>
                        <th>Username</th>
                        <th>Name</th>
                        <th>E-mail</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                    </tr>
                    <?php 
                    $result_set = get_staff_list();
                    while($staff = mysqli_fetch_assoc($result_set)) {
                        echo '<tr class="studentTableRecord">';
                        echo '<td>' . $staff['staff_username'] . '</td>';
                        echo '<td>' . $staff['staff_first_name'] . ' ' . $staff['staff_last_name'] . '</td>';
                        echo '<td>' . $staff['staff_email'] . '</td>';
                        echo '<td>' . $staff['staff_last_login'] . '</td>';
                        echo '<td><center><span class="editBtn" onclick="showPopup(\''. $staff['staff_id'] .'\',\''. $staff['staff_username'] .'\',\''. $staff['staff_email'] .'\',\''. $staff['staff_first_name'] .'\',\''. $staff['staff_last_name'] .'\')"><i class="fas fa-edit"></i></span><span class="deleteBtn" onclick="confirmDelete('. $staff['staff_id'] .')"><i class="fas fa-trash"></i></span></center></td>';
                        echo '</tr>';
                    } ?>
                </table>
            </div>
        </div>
    </div>
    <div id="popupForm" class="modal">
        <div class="modal-content">
            <h3>Edit staff account details</h3>
            <form action="index.php" method="post" autocomplete="off">
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
    <div id="popupAddForm" class="modalAdd">
        <div class="modal-content">
            <h3>Add new staff account</h3>
            <form action="create_staff.php" method="post" autocomplete="off">
                <label for="staffUsername">Username</label><br>
                <input type="text" name="staffUsername" id="staffUsername" value=""/><br>
                <label for="staffPassword">Password</label><br>
                <input type="password" name="staffPassword" id="staffPassword" value=""/><br>
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
