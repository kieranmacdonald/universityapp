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

    function redirect_to($location){
        header("Location: " . $location);
        exit;
    }

    global $db;

    $id = $_GET['course_id'];

    function delete_course_by_id($id){
        global $db;
        
        $sql = "DELETE FROM course ";
        $sql .= "WHERE course_id='" . $id . "' ";
        $sql .= "LIMIT 1";
        
        $result = mysqli_query($db, $sql);
        
        if($result){
            return true;
        }else{
            //DELETE failed
            echo mysqli_error($db);
            //disconnect from database
        if(isset($db)){
            mysqli_close($db);
        }
            exit;
        }
    }
    if($_SERVER['REQUEST_METHOD'] == 'POST') {
        $result = delete_course_by_id($id);
        if($result === true){
            $_SESSION['message'] = 'Course deleted successfully';
            redirect_to('./');
        }else{
            echo "Errors";
        }
    }
    if (isset($db)){
        mysqli_close($db);
    }
?>