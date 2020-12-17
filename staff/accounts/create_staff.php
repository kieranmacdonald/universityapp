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

    function create_staff($staff){
        global $db;

        $sql = $db->prepare("INSERT INTO staff (staff_username,staff_password,staff_email,staff_first_name,staff_last_name) VALUES (?,?,?,?,?)");
        $sql->bind_param("sssss",$staff['staff_username'],$staff['staff_password'],$staff['staff_email'],$staff['staff_first_name'],$staff['staff_last_name']);
        
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
        $staff = [];
        $staff['staff_username'] = $_POST['staffUsername'] ?? '';
        $staff['staff_password'] = password_hash($_POST['staffPassword'] ?? '', PASSWORD_BCRYPT);
        $staff['staff_email'] = $_POST['staffEmail'] ?? '';
        $staff['staff_first_name'] = $_POST['staffFirstName'] ?? '';
        $staff['staff_last_name'] = $_POST['staffLastName'] ?? '';

        $result = create_staff($staff);
        if($result === true){
            $_SESSION['message'] = 'Staff account added successfully';
            redirect_to('./');
        } else {
            exit("Error when updating database");
        }

        $staff = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
    }
?>
<?php
    //Disconnect from the database
    if (isset($db)){
        mysqli_close($db);
    }
?>
