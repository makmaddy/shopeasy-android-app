<?php

include('db_connection.php');

//echo "php ser".phpversion();
// fullname, phone, username, password;

$fullname =  htmlentities($_POST['fullname'] );
$email =  htmlentities($_POST['email'] );
$phone =  htmlentities($_POST['phone'] );
$username =  htmlentities($_POST['username'] );
$password =  htmlentities($_POST['password'] );
// remove back slash from the variable if any...

$fullname =   stripslashes($fullname);  //  "kamal B"; //
$email =  stripslashes($email); 
$phone =  stripslashes($phone);  //  "9144040888";//
$username =  stripslashes($username);  // " kamalbunkar123"; //
$password =  stripslashes($password);  //  "test123";  //



if(isset($phone) && isset($username)  && !empty($phone) && !empty($username)  ) {

global $conn;
	
	if($conn-> connect_error){
		die(" connecction has failed ". $conn-> connect_error)	;
	}
	// get current date
	date_default_timezone_set("Asia/Kolkata");
	$date = date("Y-m-d");
	$status =0;
	$msg ="";
	
	// check whether user phone number already exist or not?
	$notExist = true;
	
	$stmt11 = $conn->prepare("SELECT user_id FROM user_profile WHERE phone_no =?");
	$stmt11->bind_param( s, $phone);
	 $stmt11->execute();
	 $stmt11->store_result();
	 $stmt11->bind_result ( $col1);
	 	
	 while($stmt11->fetch() ){
	 	
	 	$notExist = false;			
	 }
	  	
	// echo "  not exist value is ".$notExist;	
	
	
	if($notExist)
	{
	
		
		/// get last user id 
	
	 	$stmt = $conn->prepare("SELECT user_id FROM user_profile ORDER BY sno DESC LIMIT 1");
	 	$stmt->execute();
	 	$stmt->store_result();
	 	$stmt->bind_result ( $col1);
	 	$rowsno = 10000;
	 	
	 	while($stmt->fetch() ){
	 	
	 			$rowsno = $col1;			
	 	}
	  	
	 	$rowsno = $rowsno +1;
	 	$address ="";
	 	
	 	//echo "row id is ".$rowsno."  fullname ". $fullname."  addrs ".$address." phone --". $phone."  date ". $date  ;
	 	
	 	$stmt2 = $conn->prepare("INSERT INTO user_profile( full_name, address, email, phone_no, user_id, date )  VALUES (?,?,?,?,?,?)");
	 	$stmt2->bind_param( ssssis, $fullname,  $address, $email, $phone,  $rowsno, $date );
	 	$stmt2->execute();
	 	
	 	// echo " insert row id is  ".$stmt2->insert_id;
	 	
	 	
	 	//isset()  empty()
	 	if(!empty($stmt2->insert_id)){
	 	// now insert data into user_login table
	 	
	 		$stmt3 = $conn->prepare("INSERT INTO user_login( user_id, username, password )  VALUES (?,?,?)");
		 	$stmt3->bind_param( iss, $rowsno, $username, $password );
		 	$stmt3->execute();
		 	
		 	$status =1;
		 	$msg = "New User registered Successfully";
		 	$userid = $rowsno;
	 	
	 		// echo " insert username password row id is  ".$stmt3->insert_id;
	 	}else {
	 	
	 		$msg = "Failled to register new user";
	 		$userid = "";
	 	}
	 	
	 	
	 	$post_data = array(
	 			 'status' => $status,
	 			 'msg' => $msg,
	 			 'Information' => array( 'user_id' => $userid,
	 			 			  'fullname' => $fullname,
	 			 			   'email' => $email,
	 			 			   'phone' =>$phone   ) 
	 			 
	 			                                                 );
	 	
	 	
	 	$post_data= json_encode( $post_data );
	 	
	 	echo $post_data;
	 	
	 	mysqli_close($conn);
 	
 	}else{
 	
 		$post_data = array(
	 			 'status' => 0,
	 			 'msg' => "user already exist. Please try to signin",
	 			 'Information' => array( 'user_id' => "",
	 			 			  'fullname' => "",
	 			 			   'email' => "",
	 			 			   'phone' =>""   ) 
	 			 
	 			                                                 );
	 	
	 	
	 	$post_data= json_encode( $post_data );
	 	
	 	echo $post_data;
 	
 	}

 }	

?>