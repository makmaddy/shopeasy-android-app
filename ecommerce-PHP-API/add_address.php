<?php

include('db_connection.php');

$securecode =  htmlentities($_POST['securecode'] );
$user_id =  htmlentities($_POST['user_id'] );
$fullname =  htmlentities($_POST['fullname'] );
$address1 =  htmlentities($_POST['address1'] );
$address2 =  htmlentities($_POST['address2'] );
$city =  htmlentities($_POST['city'] );
$state =  htmlentities($_POST['state'] );
$pincode =  htmlentities($_POST['pincode'] );
$phone =  htmlentities($_POST['phone'] );

// remove back slash from the variable if any...


$securecode =   stripslashes($securecode);  //   "1234567890";//
$user_id =   stripslashes($user_id);  // "12";//
$fullname =   stripslashes($fullname);
$address1 =  stripslashes($address1);
$address2 =  stripslashes($address2);
$city =   stripslashes($city);
$state =  stripslashes($state);
$pincode = stripslashes($pincode);
$phone =  stripslashes($phone);


//echo "  outside ";

if(isset($securecode)  && !empty($securecode) && isset($fullname)  && !empty($fullname) && !empty($user_id) ) {

global $conn;
	
	if($conn-> connect_error){
		die(" connecction has failed ". $conn-> connect_error)	;
	}
	// get current date
	date_default_timezone_set("Asia/Kolkata");
	$date = date("Y-m-d");
	
	
	$jsonarray =  array();
	$addressid_count =1;
	
	$status =0;
	$msg ="faill to add User Address";
	$information = "faill to add User Address";

	//echo "inside if";
	// check userID exist or not
	$notExist = true;

	$rowUser_id = 0;
	$rowAddressArray = array();
	
	
	
	 $stmt = $conn->prepare("SELECT user_id, addressarray FROM address WHERE user_id=?");
	 $stmt ->bind_param(i, $user_id);
	 $stmt->execute();
	 $stmt->store_result();
	 $stmt->bind_result ( $col1, $col2);
	 
	 while($stmt->fetch() ){
	 
	 		$notExist = false;

	 		$rowUser_id = $col1;
	 		$rowAddressArray = $col2;
	 		
	 					
	 }
	
	 
	 if( $notExist){
			
		// create product id json array-
		
		$address_json_array[0] =	array(
		 			 'address_id' => $addressid_count,
		 			 'fullname' => $fullname,
		 			 'address1' => $address1,
		 			 'address2' => $address2,
		 			 'city' => $city,
		 			 'state' => $state,
		 			 'pincode' =>$pincode,
		 			 'phone' => $phone );
		 			 
		//echo " prod array is ".	json_encode( $prod_json_array );
		 
		 $address_jsonarray = json_encode( $address_json_array );
	
		// add prod id into cartdetails table
			
		
	
	 	 $stmt2 = $conn->prepare("INSERT INTO address ( user_id, addressarray )  VALUES (?,?)");
		 $stmt2->bind_param( is, $user_id, $address_jsonarray);
		 $stmt2->execute();
		 
		
		//if(!empty($stmt2->insert_id)){
		
			//echo "  insert sus ";
		
			$status =1;
			$msg ="Successfully added user address";
			$information = "Successfully added user address";
		
				
		//}
	
	 
	 
	 }else{
	   /// yes userid exist
	   	
	   			 
	  	$oldarray = json_decode( $rowAddressArray, true) ;
	  	
	  	
	  	foreach($oldarray as $arraykey) {
			 //  echo "prod id ".$arraykey['prod_id'];
			   
			
			   
			   $addressid_count = $addressid_count+1;
			   
		  }
		  
		  $newjsonObject = array(
		 			 'address_id' => $addressid_count,
		 			 'fullname' => $fullname,
		 			 'address1' => $address1,
		 			 'address2' => $address2,
		 			 'city' => $city,
		 			 'state' => $state,
		 			 'pincode' =>$pincode,
		 			 'phone' => $phone );
	 	
		  
		  
		 //echo " don't update table";
		  		
		 $status =1;
		 $msg = "New address added Successfully";
		// $information  = $qouteId;
				 	
		
		  
		 array_push( $oldarray , $newjsonObject   );
		  	 
		  	 
		 $tempnewarray = 	 json_encode( $oldarray);
	 			  
	 	  
	 	 $stmt2 = $conn->prepare("UPDATE address SET addressarray=? WHERE user_id=?");
		 $stmt2->bind_param( si, $tempnewarray, $user_id );
		 $stmt2->execute();
		
		 
		$rows=$stmt2->affected_rows;
			//echo " row ".$rows;
			
			if($rows>0){	
			   		//echo " row affected is ".;
				   	$status =1;
				 	$msg = "Address Added successfully.";
				 	$information = "Successfully added user address";
			    	
			
			
			}else{
			
			
				$status =0;
			 	$msg = " Fail to add new address.";
			 	$information = "Fail to add new address.";
			
			}
	 	
		  
		  
	  
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
	