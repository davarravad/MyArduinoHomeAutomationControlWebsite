<?php
/**
* Arduino Smart Home Control Console - Garage Control File
*
*
* @author David (DaVaR) Sargent <davar@userapplepie.com>
* @version 1.1
*/

/* Let Browser Know This is a Text Output */
header("Content-Type: text/plain");

/* Require the database file */
require_once('database.php');

/* Get Data From URL Input */
if(isset($_REQUEST['door_id'])){ $door_id = $_REQUEST['door_id']; }
if(isset($_REQUEST['action'])){ $action = $_REQUEST['action']; }
if(isset($_REQUEST['action_data'])){ $action_data = $_REQUEST['action_data']; }
if(isset($_REQUEST['tkn'])){ $tkn = $_REQUEST['tkn']; }

/* Check to make sure token is valid before updating database */
if($tkn == $db_house_token){
    if($action == "update_sensor"){
        /* Update the garage door sensor data (Open/Closed) */
        if(isset($action_data)){
            /* Update Current Door Status in Database */
            $sql = "UPDATE uap4_hc_garage SET door_status = ? WHERE house_id = ? AND door_id = ?";
            $pdo->prepare($sql)->execute([$action_data, $house_id, $door_id]);
            /* If Door is closed then reset the email setting for door */
            /* Update Current Door Status in Database */
            if($action_data == "CLOSED"){
              $sql = "UPDATE uap4_hc_garage SET email_sent = ? WHERE house_id = ? AND door_id = ? AND door_status = ?";
              $pdo->prepare($sql)->execute(["no", $house_id, $door_id, "CLOSED"]);
            }
            /* Let Arduino know database was updated */
            echo "<UPDATED>";
        }else{
            /* Let Arduino know there was an error */
            echo "<ERROR485>";
        }
        /* Check to see how long the door has been open */
        /* If door_status is open and enabled.  Check to see if timestamp is greater than 15 min.  Send email */
        $min_time = $db_email_doors_minutes;
        $stmt = $pdo->prepare('SELECT * FROM uap4_hc_garage WHERE house_id = :house_id AND door_id = :door_id AND door_status = :door_status AND enable = "1" AND email_sent = "no" AND timestamp < DATE_SUB(NOW(),INTERVAL :min_time MINUTE)');
        $stmt->execute(['house_id' => $house_id, 'door_id' => $door_id, 'door_status' => "OPEN", 'min_time' => $min_time]);
        $data_garage = $stmt->fetch();
        $count = $stmt->rowCount();
        if($count > 0){
          /* Get House Owner's Email if they have garage notifaction enabled */
          if($db_email_enable_doors == "1"){
            /* Get User ID Based on House ID */
            $stmt = $pdo->prepare('SELECT user_id FROM uap4_hc_user_perm WHERE house_id = :house_id');
            $stmt->execute(['house_id' => $house_id]);
            $email_data = $stmt->fetchAll();
            /* Send email to all users attached to house id */
            foreach ($email_data as $ed) {
              /* Get User Email Based on User ID */
              $ed_user_id = $ed['user_id'];
              $stmt = $pdo->prepare('SELECT email FROM uap4_users WHERE userID = :userID LIMIT 1');
              $stmt->execute(['userID' => $ed_user_id]);
              $data_users = $stmt->fetch();
              $email = $data_users["email"];
              if(isset($email)){
                /* If user enabled Send email if there was a result */
                /* EMAIL MESSAGE USING PHPMAILER */
                $mail = new \Libs\PhpMailer\Mail();
                $mail->addAddress($email);
                $mail->setFrom(SITEEMAIL, EMAIL_FROM_NAME);
                $mail->subject(SITE_TITLE. " - ".$data_garage['door_title']." is Open!");
                $body = " Your ".$data_garage['door_title']." has been open for more than $min_time minutes.<br/><br/>";
                $body .= "<b>Ahhhh!</b>";
                $body .= "<br><br>You might want to close your garage door! <br/><br/>";
                $body .= "<a href='".SITE_URL."'>".SITE_TITLE."</a>";
                $mail->body($body);
                $mail->send();
              }
            }
          }
          /* Update Current Door Status in Database */
          $sql = "UPDATE uap4_hc_garage SET email_sent = ? WHERE house_id = ? AND door_id = ? AND door_status = ?";
          $pdo->prepare($sql)->execute(["yes", $house_id, $door_id, "OPEN"]);
        }
    }else if($action == "door_button"){
        /* Check database to see if door button was pushed */
        $stmt = $pdo->prepare('SELECT door_button FROM uap4_hc_garage WHERE house_id = :house_id AND door_id = :door_id');
        $stmt->execute(['house_id' => $house_id, 'door_id' => $door_id]);
        $data = $stmt->fetch();
        $output = $data["door_button"];
        if(!empty($output)){
            echo "<".$output.">";
            /* Update Database to change button to do nothing */
            $sql = "UPDATE uap4_hc_garage SET door_button = ? WHERE house_id = ? AND door_id = ?";
            $pdo->prepare($sql)->execute(["DO_NOTHING", $house_id, $door_id]);
        }else{
            /* Button not pushed - Let Arduino Know */
            echo "<DO_NOTHING1>";
        }
    }else if($action == "garage_data"){
        /* Get garage door status */
        $stmt = $pdo->prepare('SELECT * FROM uap4_hc_garage WHERE house_id = :house_id');
        $stmt->execute(['house_id' => $house_id]);
        $data = $stmt->fetch();
        $count = $stmt->rowCount();
        /* check to see if there was a result */
        if($count > 0){
            $data['success'] = "true";
            header('Content-type: application/json');
            echo json_encode( $data );
        }else{
            $error_data['success'] = "false";
            $error_data['error']['code'] = "5868";
            $error_data['error']['message'] = "Garage Data Not Found";
            header('Content-type: application/json');
            echo json_encode( $error_data );
        }
    }else{
        /* Button not pushed - Let Arduino Know */
        echo "<DO_NOTHING2>";
    }
}else{
    /* There was an error with Token - Let Arduino Know */
    echo "<ERROR-TOKEN-NO-MATCH>";
}





?>
