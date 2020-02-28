<?php

include('db_connection.php');


//  phone, password;


$phone =  htmlentities($_POST['phone'] );
$password =  htmlentities($_POST['password'] );
// remove back slash from the variable if any...


$phone =  stripslashes($phone);  //  "1234567890";//

$password = stripslashes($password);  //  "pass1234";  //



if(isset($phone) && isset($password)  && !empty($phone) && !empty($password)  ) {

global $conn;
	
	if($conn-> connect_error){
		die(" connecction has failed ". $conn-> connect_error)	;
	}
	// get current date
	date_default_timezone_set("Asia/Kolkata");
	$date = date("Y-m-d");
	$status =0;
	$msg ="Invalid login";
	$Information =  array( 'user_id' => "",
	 			'fullname' => "",
	 			 'email' => "",
	 			 'phone' => ""   ) ;
	
	// ORDER BY id ASC|DESC;

 	$stmt = $conn->prepare("SELECT userlogin.password, userp.user_id, userp.full_name, userp.phone_no, userp.email FROM user_profile userp, user_login userlogin WHERE userp.user_id = userlogin.user_id AND userp.phone_no =?");
 	$stmt-> bind_param("s", $phone);
 	$stmt->execute();
 	$stmt->store_result();
 	$stmt->bind_result ( $col1, $col2, $col3, $col4, $col5);
 
 	
 	while($stmt->fetch() ){
 	
 			//echo "  stam extecute ".$col1."  user password is ".$password;
 			
 			if($col1 === $password ){
 				$status =1;
				$msg =" sucessfull login";
				$Information =   array( 'user_id' => $col2,
	 			 			  'fullname' => $col3,
	 			 			   'email' => $col5,
	 			 			   'phone' => $col4   ) ;
 		
 			}
 				
 	}
  	
 	
 	$post_data = array(
 			 'status' => $status,
 			 'msg' => $msg,
 			 'Information' => $Information );
 	
 	
 	$post_data= json_encode( $post_data );
 	
 	echo $post_data;
 	
 	mysqli_close($conn);

 }	

?>