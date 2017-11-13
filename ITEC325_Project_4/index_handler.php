<?php
//header('Location: main.html');
error_reporting(E_ALL);

require_once('proj4connectpath.php');

//NEEDS TO be sanitized!!!
$email=$_POST['email'];
$password=$_POST['password'];
//$lastAuthentication = 0;
$message="";

if (isset($_POST['login'])) {
    //NEED TO make prepared statement!!!
    $userQuery = "SELECT * FROM users WHERE email='" . $email . "' and password = '" . $password . "'";
    
    //make database connection, if valid user/email combination, pull row, close connection
    $connect = $path;
    echo "Connection ", ($connect ? "" : "NOT "), "established.<br />\n";
    $uQuery = mysqli_query($connect, $userQuery);
    $user = mysqli_fetch_assoc($uQuery);
    mysqli_close($connect);
    
    if(is_array($user)) {
        session_start();
        //var_dump($email);
        $_SESSION['email'] = $email;
        var_dump($_SESSION);
        header('Location: main.php');
        exit();
    }
    else {
        $message = "Please try again.";
        echo $message;
    }
}
elseif (isset($_POST['create'])) {
    //NEED TO make prepared statement!!!
    $createQuery = "INSERT INTO users(email,password) VALUES('$email', '$password')";
    
    //make database connection
    $connect = $path;
    $results = mysqli_query($connect, $createQuery);
    if(mysqli_affected_rows($connect) > 0){
        echo "<p>New account created.</p>";
        echo "<a href='main.html'>Add Some Tasks</a>";
    }
    else {
        echo "Sorry, you'll have to try again.<br />";
        echo mysqli_error ($connect);
    }
    //close database connection
    mysqli_close($connect);
}


?>