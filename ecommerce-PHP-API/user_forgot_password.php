<?php

include('db_connection.php');


//  phone;

$phone =  htmlentities($_POST['phone'] );

$phone =  stripslashes($phone); 


if(isset($phone) && !empty($phone)  ) {

global $conn;
	
	if($conn-> connect_error){
		die(" connecction has failed ". $conn-> connect_error)	;
	}
	// get current date
	date_default_timezone_set("Asia/Kolkata");
	$date = date("Y-m-d");
	$status =0;
	$msg ="";
	$information ="";
		
	$userExist = false;	
	$userID = "";	

	$stmt11 = $conn->prepare("SELECT user_id FROM user_profile WHERE phone_no =?");
	$stmt11->bind_param( s, $phone);
	 $stmt11->execute();
	 $stmt11->store_result();
	 $stmt11->bind_result ( $col1);
	
	 	
	 while($stmt11->fetch() ){
	 	
	 	$userExist = true;
	 	$userID   = $col1;			
	 }
	
	 //echo " col 1 value ".$col1 ."  flag value ".$userExist; 
	 if($userExist)
	 {
	 
	 	$OTPvalue =   substr( md5(microtime()), rand(0,26),8  );
	 
	 
	 	$status =1;
	 	$msg = "send OTP successful";
	 	$information = array(
	 			 'otp' => $OTPvalue,
	 			 'user_id' => $userID	 );
	 	//------- send sms to user phone number
	 	
	 	// Your authentication key

			$authKey = $OTPauthKey;


			// Multiple mobiles numbers separated by comma

			$mobileNumber = $phone;

			// Sender ID,While using route4 sender id should be 6 characters long.

			$senderId = "KAMALB";

			// Your message to send, Add URL encoding here.

			$message = urlencode("Your OTP is $OTPvalue");

			// Define route

			$route = "4";

			// Prepare you post parameters

			$postData = array(
				'authkey' => $authKey,
				'mobiles' => $mobileNumber,
				'message' => $message,
				'sender' => $senderId,
				'route' => $route
			);

			// API URL

			$url = "https://control.msg91.com/api/sendhttp.php";

			// init the resource

			$ch = curl_init();
			curl_setopt_array($ch, array(
				CURLOPT_URL => $url,
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => $postData

				// ,CURLOPT_FOLLOWLOCATION => true

			));

			// Ignore SSL certificate verification

			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);


			// get response echo " before output ";
			$output = curl_exec($ch);

			// Print error if any

			if (curl_errno($ch))
				{
				echo 'error:' . curl_error($ch);
				}

			curl_close($ch);
	 			 
	 	
	 
	 
	 }else{
	 	$status =0;
	 	$msg = "phone number not exist";
	 	$information  = "Not a registered user phone number";
	 
	 
	 
	 }
	 
	
	 
	 
	$post_data = array(
	 			 'status' => $status,
	 			 'msg' => $msg,
	 			 'Information' => $information );
	 	
	 	
	 $post_data= json_encode( $post_data );
	 	
	 echo $post_data;
	 	
	 mysqli_close($conn);
	 
	 
	 
	 /// send opt sms
	 /// send response with otp and user_id
	 
	// { userid : 123422,  opt : optno } 	

 }	

?>
