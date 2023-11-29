<?php
 /**
 * This is a sample USSD code with session management. * It has only two screens.The purpose is to help developers get started with their USSD application 
 *and session management. */
$request = file_get_contents("php://input"); $data = json_decode($request, true); 
/** * set your custom session id and start session for the incoming request. 
 * * Note!! * The only unique parameter is the msisdn.
 *  * Set session id with the msisdn in order to track the session */
session_id(md5($data['MSISDN']));
 session_start();
  $ussd_id = $data['USERID']; 
  $msisdn = $data['MSISDN']; 
  $user_data = $data['USERDATA']; 
  $msgtype = $data['MSGTYPE']; 
  $id = session_id(); 
  $totalScreens = 3; 

  function sendResponse($responsePayload) {
    if ($responsePayload['MSGTYPE'] == true) { echo json_encode($responsePayload);
    } else {
        echo json_encode($responsePayload); session_destroy();
    }
}

if (isset($_SESSION[$id]) and $msgtype == false) {
     $_SESSION[$id] = "{$_SESSION[$id]}{$user_data},"; //"scrn1Option" -> "scrn1Option,scrn2Option"
    //TODO: Validate the input
    if (!preg_match("/^[1-3,]+$/", $_SESSION[$id])) {
         $msg = " Error encountered. Please redial the code and attempt once more"; 
        $resp = array("USERID" => $ussd_id, "MSISDN" => $msisdn, "USERDATA" => $user_data, "MSG" => $msg, "MSGTYPE" => false);
         echo json_encode($resp); 
         session_destroy(); 
         die();
    }
    //TODO: Split the string of selected options by ","
    $selectedOptions = preg_split("/\,/", $_SESSION[$id]);
     //["scrn1Option","scrn2Option"]
   $emotion = [
        "1" => "not fine", 
        "2" => "feeling frisky", 
        "3" => "sad" ];
         $emotioncause = [ 
            "1" => "health", 
         "2" => "money", 
         "3" => "relationship", ];
         
         $emoKey = $selectedOptions[0]; 
         $emoCauseKey; 
         if (count($selectedOptions) >= $totalScreens) {
        $emoCauseKey = $selectedOptions[1];
    }

    if (count($selectedOptions) >= $totalScreens) { 
        $msg = "GOD is in control, you are {$emotion[$emoKey]} because of {$emotioncause[$emoCauseKey]} issues :(";

        $resp = array("USERID" => $ussd_id, "MSISDN" => $msisdn, "USERDATA" => $user_data, "MSG" => $msg, "MSGTYPE" => false); 
        sendResponse($resp);
    } else {
        $msg = "Why are you {$emotion[$emoKey]}?\n 1. Health\n 2. Money\n 3. Relationship"; $resp = array("USERID" => $ussd_id, "MSISDN" => $msisdn, 
        "USERDATA" => $user_data, "MSG" => $msg, "MSGTYPE" => true); 
        sendResponse($resp);
    }
}
// Initial dial
else { if (isset($_SESSION[$id]) and $msgtype == true) { 
    session_unset();
    }
    /** * Stores user inputs using sessions. * You may also store user inputs in a database */ $_SESSION[$id] = "";
    // Responds to request. MSG variable will be displayed on the user's screen
    $msg = "Welcome +{$msisdn}. \nEnjoy FRED's USSD app\nHow are you feeling today,friend\n 1. Not well\n 2. Feeling frisky\n 3. Sad"; $resp = array("USERID" 
    => $ussd_id, "MSISDN" => $msisdn, "USERDATA" => $user_data, "MSG" => $msg, "MSGTYPE" => true); 
    echo json_encode($resp);
}
header('Content-Type: application/json');
