
<?php
//require_once('private/initialise.php');

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

$hashed_password = password_hash('norton123', PASSWORD_BCRYPT);

$sql = "INSERT INTO staff ";
$sql .= "(staff_username, staff_password, staff_email, staff_first_name, staff_last_name) ";
$sql .= "values (";
$sql .= "'ben.kraft', '" . $hashed_password . "', 'ben.kraft@wit.ac.uk', 'Ben', 'Kraft'";
$sql .= ")";
echo $sql;

$result = mysqli_query($db, $sql); //true or false result

if($result){
  return true;
}else{
  //UPDATE failed
  echo mysqli_error($db);
  if(isset($db)){
    mysqli_close($db);
  }
  exit;
}

//Ben Kraft = norton123
// admin = root
?>
