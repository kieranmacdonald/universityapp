<?php session_start();

    function is_logged_in(){
        return isset($_SESSION['staff_id']);
    }
    
    if(!is_logged_in()){
        //redirect to
        header("Location: " . '../login');
    }

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

    global $db;

    function redirect_to($location){
        header("Location: " . $location);
        exit;
    }

    function create_student($student){
        global $db;

        $sql = $db->prepare("INSERT INTO student (student_first_name,student_last_name,student_email,student_address_houseno,student_address_street,student_address_city,student_address_postcode,student_course_id) VALUES (?,?,?,?,?,?,?,?)");
        $sql->bind_param("ssssssss",
        $student['student_first_name'],
        $student['student_last_name'],
        $student['student_email'],
        $student['student_address_houseno'],
        $student['student_address_street'],
        $student['student_address_city'],
        $student['student_address_postcode'],
        $student['student_course_id']);
        
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

    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        //The edited form has been sent so update the database
        $student = [];
        $student['student_first_name'] = $_POST['studentFirstName'] ?? '';
        $student['student_last_name'] = $_POST['studentLastName'] ?? '';
        $student['student_email'] = $_POST['studentEmail'] ?? '';
        $student['student_address_houseno'] = $_POST['studentAddressHouseNo'] ?? '';
        $student['student_address_city'] = $_POST['studentAddressCity'] ?? '';
        $student['student_address_street'] = $_POST['studentAddressStreet'] ?? '';
        $student['student_address_postcode'] = $_POST['studentAddressPostcode'] ?? '';
        $student['student_course_id'] = $_POST['studentCourseDropdown'] ?? '';

        $result = create_student($student);
        if($result === true){
            $_SESSION['message'] = 'Student added successfully';
            redirect_to('./');
        } else {
            exit("Error when updating database");
        }

        $student = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
    }
?>
<?php
    //Disconnect from the database
    if (isset($db)){
        mysqli_close($db);
    }
?>
