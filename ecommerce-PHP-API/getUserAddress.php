<?php

include('db_connection.php');

$securecode =  htmlentities($_POST['securecode'] );
$user_id =  htmlentities($_POST['user_id'] );

// remove back slash from the variable if any...


$securecode =  stripslashes($securecode);  //  "1234567890";//
$user_id =   stripslashes($user_id);


//echo "  outside ";

	$status =0;
	
	$rowAddressJsonArray = array();
	
	$msg ="No Address exist for User";
	$information = array(  'address_details' => $jsonarray
					   ) ;
	
	
	$notExist = true;
	
	

if(isset($securecode)  && !empty($securecode)  && !empty($user_id)  ) {

global $conn;
	
	if($conn-> connect_error){
		die(" connecction has failed ". $conn-> connect_error)	;
	}
	// get current date
	
	
	
	
	 $stmt = $conn->prepare("SELECT user_id, addressarray FROM address WHERE user_id=?");
	 $stmt ->bind_param(i, $user_id);
	 $stmt->execute();
	 $stmt->store_result();
	 $stmt->bind_result ( $col1, $col2);
	 
	 while($stmt->fetch() ){
	 
	 		$notExist = false;

	 		$rowUser_id = $col1;
	 		$rowAddressJsonArray  = $col2;
	 		
	 					
	 }
	 
	//$msg = "No Product exist on User 888 cart ". $notExist ; 
	 if( $notExist ){
	 		// user didn't add any product till now
	 		$status =0;
			$msg ="No Address exist for User. Please Add New Address.";
			$information = array(  'address_details' => $rowAddressJsonArray
					   					) ;
	 
	 
	 }else {
	 
	 		
	 	
	  	$prodIDexist = false;
	  	
	  	
	  		
	 	$addressarray = json_decode( $rowAddressJsonArray, true) ;
		  
		  $status =1;
		  $msg ="Address details for User";
		  $information =  array(  'address_details' => $addressarray 
					    ) ;
						
						//$jsonarray;
		//  $msg = "No Product exist on ---cart ". $notExist ;
		  
	 
	 }
	 
  	
	

 	
 	mysqli_close($conn);
 	

 }else{
 
			$status =1;
			$msg ="No Address exist for User";
			$information = array(  'address_details' => $rowAddressJsonArray
					   					) ;
 
 
 }
 
 
 	
 	$post_data = array(
 			 'status' => $status,
 			 'msg' => $msg,
 			 'Information' => $information );
 	
 	
 	$post_data= json_encode( $post_data );
 	
 	echo $post_data;
 	
 		

?>