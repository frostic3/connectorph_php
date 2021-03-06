<?php

error_reporting(E_ALL ^ E_DEPRECATED);

/*
 * Following code will create a new user row
 * All user details are read from HTTP Post Request
 */

// array for JSON response
$response = array();

// check for required fields
if (!(empty($_POST['email']) )) {

    $email = $_POST['email'];
    $donationid = $_POST['donationid'];

    // include db connect class
    require_once './db_Connect.php';
    require './mailer.php';

    // connecting to db
    $db = new DB_CONNECT();
    $mailIt = new Mailer();

    //Get oid from the email
    $result_getOid = mysql_query("SELECT oid, name,phoneno from orphanages WHERE email = '$email'");
    $result_getOid = mysql_fetch_array($result_getOid);
    $oid = $result_getOid["oid"];
    $orphanageName =$result_getOid["name"];
    $phoneNumber = $result_getOid["phoneno"];
    $claimed = 1;
    $claim_code = $mailIt->random_code();
    $currentTime = (new \DateTime())->format('Y-m-d H:i:s');

    // mysql updating a row
    $result_update_donation = mysql_query("UPDATE `donations` SET `claimed` = '$claimed', `claimed_at` = '$currentTime', `claim_code` = '$claim_code',
                        `oid` = '$oid' WHERE `donationid` = '$donationid'");

    // check if row inserted or not
    if ($result_update_donation) {
        // successfully updated database
        $response["success"] = 1;
        $response["message"] = "Donation successfully claimed.";
    } else {
        // failed to insert row
        $response["success"] = 0;
        $response["message"] = "Oops! An error occurred while claiming the donation.";

        // echoing JSON response
        echo json_encode($response);
        exit;
    }

    //Get phonenumber from the donationid
    $result_getPNO = mysql_query("SELECT phoneNumber, created_at, uid FROM donations WHERE donationid = $donationid") or die(mysql_error());
    $result_getPNO = mysql_fetch_array($result_getPNO);
    $phoneNumberDonor = $result_getPNO["phoneNumber"];
    $created_at = $result_getPNO["created_at"];
    $uid = $result_getPNO["uid"];

    // check if row inserted or not
    if ($result_getPNO) {
        // successfully inserted into database
        $response["phoneNumber"] = $phoneNumberDonor;
        $result = mysql_query("SELECT * FROM users WHERE uid = '$uid'") or die(mysql_error());
        // check for result
        $no_of_rows = mysql_num_rows($result);
        if ($no_of_rows > 0) {
            // user found
            $user=mysql_fetch_array($result);
            $user_name=$user["name"];
            $emailDonor=$user["email"];
            $body="Claim Code: $claim_code.\nYour donation which was created at $created_at was claimed by the following Orphanage.\nOrphanage Name: $orphanageName\nPhone Number: $phoneNumber";
            $bodyForMail=nl2br("Claim Code: $claim_code.\r\n\r\nYour donation which was created at $created_at was claimed by the following Orphanage.\r\n\r\nOrphanage Name: $orphanageName\r\n\r\nPhone Number: $phoneNumber");
            $response["body"] = $body;
            $subject="Claim Code";
            $response["mailIt"]=$mailIt->mailMe($emailDonor,$user_name,$subject,$bodyForMail);
            // echoing JSON response
            echo json_encode($response);
        } else {
            // user not found
            $response["success"] = 0;
            $response["error"] = 1;
            $response["message"] = "There is no user registered with that email address";
            // echoing JSON response
            echo json_encode($response);
            exit;
        }
    } else {
        // failed to insert row
        $response["success"] = 0;
        $response["message"] = "Oops! An error occurred while claiming the donation.";

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
