<?php

/*
 * Following code will create a new user row
 * All user details are read from HTTP Post Request
 */

// array for JSON response
$response = array();

// check for required fields
if (!(empty($_POST['name']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['phoneNumber'])  || empty($_POST['address1']) || empty($_POST['address2']) || empty($_POST['state']) || empty($_POST['city']))) {

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
    echo json_encode($salt);
    echo json_encode($encrypted_password);

    $address1 = $_POST['address1'];
	$address2 = $_POST['address2'];
    $email = $_POST['email'];
	$state = $_POST['state'];
	$city = $_POST['city'];
    $phoneNumber = $_POST['phoneNumber'];
    $website = $_POST['website'];
    $mission = $_POST['mission'];



    // include db connect class
    require_once __DIR__ . '/db_connect.php';

    // connecting to db
    $db = new DB_CONNECT();

    // mysql inserting a new row
    $result = mysql_query("INSERT INTO orphanages( name, email, encrypted_password, salt, mission, website, phoneno, address1, address2, state, city ) VALUES( '$name', '$email', '$encrypted_password' , '$salt', '$mission', '$website', '$phoneNumber', '$address1','$address2','$state','$city')");

    // check if row inserted or not
    if ($result) {
        // successfully inserted into database
        $response["success"] = 1;
        $response["message"] = "Orphanage successfully Registered.";

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
