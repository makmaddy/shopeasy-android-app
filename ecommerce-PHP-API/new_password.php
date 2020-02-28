<?php

include('db_connection.php');


//  userid, password;

$userid =  htmlentities($_POST['userid'] );
$password =  htmlentities($_POST['password'] );

$userid =  stripslashes($userid); 
$password =  stripslashes($password);


if(isset($userid) && !empty($userid)  && isset($password) && !empty($password)  ) {

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
	
	
	// check whether user password is a new password or not ?
	$notExist = true;
	
	$stmt11 = $conn->prepare("SELECT password FROM user_login WHERE user_id =?");
	$stmt11->bind_param( s, $userid);
	 $stmt11->execute();
	 $stmt11->store_result();
	 $stmt11->bind_result ( $col1);
	 	
	 while($stmt11->fetch() ){
	 
	 
	 	if($col1=== $password ){
	 	
			$notExist = false;
	 
	 	}
					
	 }	  	
	// echo "  not exist value is ".$notExist;	
		
	if($notExist)
	{
	
			// update tablename SET colname =?, sdf WHERE userrid=?
			
			 $stmt11 = $conn->prepare("UPDATE user_login SET password=? WHERE user_id=?") ;
			
			 $stmt11->bind_param( si, $password, $userid);
			
		
			 $stmt11->execute();
			
			   	 
			// check whether password already exist on same row or not
			   	
			  $rows=$stmt11->affected_rows;
			//echo " row ".$rows;
			if($rows>0){	
			   		//echo " row affected is ".;
				   	$status =1;
				 	$msg = "Password Update Successful";
				 	$information  = "Password Update Successful";
			    	
			
			
			}else{
			
			
				$status =0;
			 	$msg = "Falied to Update Password";
			 	$information  = "Please try again.";
			
			
			}
			
	
	
	}else{
	
			$status =0;
			$msg ="Password already exist, Please create new password";
			$information ="Password already exist, Please create new password";
	
	
	}
	
	
	
	
	$post_data = array(
	 			 'status' => $status,
	 			 'msg' => $msg,
	 			 'Information' => $information );
	 	
	 	
	 $post_data= json_encode( $post_data );
	 	
	 echo $post_data;
	 	
	 mysqli_close($conn);
	
	
	
	
	
	
	
  }
	
	
?>