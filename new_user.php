<?php

error_reporting(E_ALL ^ E_DEPRECATED);

/*
 * Following code will create a new user row
 * All user details are read from HTTP Post Request
 */

// array for JSON response
$response = array();

// check for required fields
if (!(empty($_POST['name']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['phoneNumber']))) {

    $name = $_POST['name'];
    $password = $_POST['password'];

    /**
     * Encrypting password
     * returns salt and encrypted password
     */
    $salt = sha1(rand());
    $salt = substr($salt, 0, 10);
    $encrypted = base64_encode(sha1($password . $salt, true) . $salt);
    $hash = array("salt" => $salt, "encrypted" => $encrypted);
    $encrypted_password = $hash["encrypted"]; // encrypted password
    $salt = $hash["salt"]; // salt
    $address1 = $_POST['address1'];
	$address2 = $_POST['address2'];
    $email = $_POST['email'];
	$state = $_POST['state'];
	$city = $_POST['city'];
	$phoneNumber =$_POST['phoneNumber'];

    $name = str_replace("'","''",$name);
    $address1 = str_replace("'","''",$address1);
    $address2 = str_replace("'","''",$address2);

    // include db connect class
    require_once './db_Connect.php';

    // connecting to db
    $db = new DB_CONNECT();

    if($state == "[State]")
        $state = null;
    if($city =="[City]")
        $city = null;

    //Check if User already exists in users table
    $emailCheck = mysql_query("SELECT email from users WHERE email = '$email'");
    $no_of_rows = mysql_num_rows($emailCheck);
    if ($no_of_rows > 0) {
        // failed to insert row
        $response["success"] = 0;
        $response["message"] = "There is already an user registered with that email";

        // echoing JSON response
        echo json_encode($response);
        exit;
    }

    //Check if User already exists in orphanage table
    $emailCheck = mysql_query("SELECT email from orphanages WHERE email = '$email'");
    $no_of_rows = mysql_num_rows($emailCheck);
    if ($no_of_rows > 0) {
        // failed to insert row
        $response["success"] = 0;
        $response["message"] = "There is already an user registered with that email";

        // echoing JSON response
        echo json_encode($response);
        exit;
    }


    // mysql inserting a new row
    $result = mysql_query("INSERT INTO users( name, email, encrypted_password, salt, phoneno, address1, address2, state, city ) VALUES( '$name', '$email', '$encrypted_password' , '$salt', '$phoneNumber', '$address1','$address2','$state','$city')");

    // check if row inserted or not
    if ($result) {
        // successfully inserted into database
        $response["success"] = 1;
        $response["message"] = "User successfully Registered.";

        // echoing JSON response
        echo json_encode($response);
    } else {
        // failed to insert row
        $response["success"] = 0;
        $response["message"] = "Oops! An error occurred.";

        // echoing JSON response
        echo json_encode($response);
    }
} else {
    // required field is missing
    $response["success"] = 0;
    $response["message"] = "Required field(s) is missing";

    // echoing JSON response
    echo json_encode($response);
}

?>
