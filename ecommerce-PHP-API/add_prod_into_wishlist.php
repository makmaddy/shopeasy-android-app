<?php

include('db_connection.php');

$securecode =  htmlentities($_POST['securecode'] );
$prod_id =  htmlentities($_POST['prod_id'] );
$user_id =  htmlentities($_POST['user_id'] );

// remove back slash from the variable if any...


$securecode =  stripslashes($securecode);  //   "1234567890";//
$prod_id =    stripslashes($prod_id);   //  "1";//
$user_id =  stripslashes($user_id);  //"12";//

//echo "  outside ";

if(isset($securecode)  && !empty($securecode) && isset($prod_id)  && !empty($prod_id) && !empty($user_id) ) {

global $conn;
	
	if($conn-> connect_error){
		die(" connecction has failed ". $conn-> connect_error)	;
	}
	// get current date
	date_default_timezone_set("Asia/Kolkata");
	$date = date("Y-m-d");
	
	 $totalPrice  =0;
	$jsonarray =  array();
	$status =0;
	$msg ="failled to add product into wishlist";
	$information = array(  'prod_details' => $jsonarray, 
					   'totalprice' =>   $totalPrice  ) ;

	//echo "inside if";
	// check userID exist or not
	$notExist = true;

	$rowUser_id = 0;
	$rowProdJsonArray = array();
	
	
	
	 $stmt = $conn->prepare("SELECT user_id, prod_id FROM wishlist WHERE user_id=?");
	 $stmt ->bind_param(i, $user_id);
	 $stmt->execute();
	 $stmt->store_result();
	 $stmt->bind_result ( $col1, $col2);
	 
	 while($stmt->fetch() ){
	 
	 		$notExist = false;

	 		$rowUser_id = $col1;
	 		$rowProdJsonArray = $col2;
	 		
	 					
	 }
	
	 
	 if( $notExist){
			
		// create product id json array-
		
		$prod_json_array[0] =	array(
	 			 'prod_id' => $prod_id,
	 			 'date' => $date  );
	 			 
		//echo " prod array is ".	json_encode( $prod_json_array );
		 
		 $prod_jsonarray = json_encode( $prod_json_array );
	
		// add prod id into cartdetails table
			
		
	
	 	 $stmt2 = $conn->prepare("INSERT INTO wishlist ( user_id, prod_id )  VALUES (?,?)");
		 $stmt2->bind_param( is, $user_id, $prod_jsonarray);
		 $stmt2->execute();
		 
		
		//if(!empty($stmt2->insert_id)){
		
			//echo "  insert sus ";
		
			$status =1;
			$msg ="Successfully added product into wishlist.";
			$information =  array(  'prod_details' => $jsonarray, 
					   'totalprice' =>   $totalPrice  ) ;
		
				
		//}
	
	 
	 
	 }else{
	   /// yes userid exist
	   	
	   	$newjsonObject = array(
	 			 'prod_id' => $prod_id,
	 			 'date' => $date  );
	 			 
	  	$oldarray = json_decode( $rowProdJsonArray, true) ;
	  	
	  	$prodIDexist = false;
	  	
	  	 foreach($oldarray as $arraykey) {
			 //  echo "prod id ".$arraykey['prod_id'];
			   
			   if( $prod_id === $arraykey['prod_id'] ){
			   	$prodIDexist = true;
			   	//echo " prodId exist in table ";
			   }
			   
		  }
		  
		  if($prodIDexist){
		  
		  		//echo " don't update table";
		  		
		  		$status =1;
				 $msg = "Successfully Product Added into the wishlist";
				// $information  = $qouteId;
				 	
		  }else{
		  
		  	 array_push( $oldarray , $newjsonObject   );
		  	 
		  	 
		  	   $tempnewarray = 	 json_encode( $oldarray);
	 			  
	 	  
	 	 $stmt2 = $conn->prepare("UPDATE wishlist SET prod_id=? WHERE user_id=?");
		 $stmt2->bind_param( si, $tempnewarray, $user_id );
		 $stmt2->execute();
		
		 
		$rows=$stmt2->affected_rows;
			//echo " row ".$rows;
			
			if($rows>0){	
			   		//echo " row affected is ".;
				   	$status =1;
				 	$msg = "Successfully Product Added into the wishlist";
				 	//$information  = "Successfully Product Added into the wishlist";
			    	
			
			
			}else{
			
			
				$status =0;
			 	$msg = " Fail to add product into wishlist.";
			 	//$information  = "Please try again.";
			
			}
	 	
		  
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
	