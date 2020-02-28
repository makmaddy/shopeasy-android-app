<?php

include('db_connection.php');

$securecode =  htmlentities($_POST['securecode'] );
$prod_id =  htmlentities($_POST['prod_id'] );
$user_id =  htmlentities($_POST['user_id'] );
$prod_price =  htmlentities($_POST['prod_price'] );

// remove back slash from the variable if any...


$securecode = stripslashes($securecode);  //   "1234567890";//
$prod_id =  stripslashes($prod_id);   //  "1";//
$user_id =  stripslashes($user_id); // "12";//
$prod_price =    stripslashes($prod_price);

//echo "  outside ";

if(isset($securecode)  && !empty($securecode) && isset($prod_id)  && !empty($prod_id) && !empty($user_id) ) {

global $conn;
	
	if($conn-> connect_error){
		die(" connecction has failed ". $conn-> connect_error)	;
	}
	// get current date
	date_default_timezone_set("Asia/Kolkata");
	$date = date("Y-m-d");
	
	$status =0;
	$msg ="failled to add product into cart";
	$information = "failled to add product into cart";
	$detailsarray =  array();
	//echo "inside if";
	
	
	
	// check userID exist or not
	$notExist = true;
	$qouteId = 1000;
	$rowUser_id = 0;
	$rowProdJsonArray = array();
	$cartcount =0;
	
	
	
	 $stmt = $conn->prepare("SELECT user_id, prod_id, qoute_id FROM cartdetails WHERE user_id=?");
	 $stmt ->bind_param(i, $user_id);
	 $stmt->execute();
	 $stmt->store_result();
	 $stmt->bind_result ( $col1, $col2, $col3);
	 
	 while($stmt->fetch() ){
	 
	 		$notExist = false;

	 		$rowUser_id = $col1;
	 		$rowProdJsonArray = $col2;
	 		$qouteId = $col3;
	 					
	 }
	
	 
	 if( $notExist){
	    // echo  "userid doesn't exist on table";
	  
		 $stmt = $conn->prepare("SELECT qoute_id FROM cartdetails ORDER BY user_id DESC LIMIT 1");
		 $stmt->execute();
		 $stmt->store_result();
		 $stmt->bind_result ( $col4);
		 //$qouteno = 1000;
		 	
		 while($stmt->fetch() ){
		 
		 		$qouteId = $col4;
		 		//echo "last qoute id ".		$qouteId;		
		 }
		 	
		
		
		// create product id json array-
		
		$prod_json_array[0] =	array(
	 			 'prod_id' => $prod_id,
	 			 'qty' => 1,
	 			 'price' => $prod_price,
	 			 'date' => $date  );
	 			 
		//echo " prod array is ".	json_encode( $prod_json_array );
		 
		 $prod_jsonarray = json_encode( $prod_json_array );
	
		// add prod id into cartdetails table
		 $qouteId =  $qouteId+1;	
		// echo " qute id ". $qouteId;
	
	 	 $stmt2 = $conn->prepare("INSERT INTO cartdetails ( user_id, prod_id, qoute_id )  VALUES (?,?,?)");
		 $stmt2->bind_param( isi, $user_id, $prod_jsonarray, $qouteId);
		 $stmt2->execute();
		 
		 if (!($stmt2->insert_id) === 0 ||  !is_null($stmt2->insert_id)){
		//if(!empty($stmt2->insert_id)){
		
			//echo "  insert sus ";
			$cartcount =1;
			$status =1;
			$msg ="Successfully added product into cart.";
			$information =  array(  'qoute_id' => $qouteId, 
						'cart_count' =>  $cartcount ) ;
		
				
		}else{
			//echo " no display msg ";
		}
	
	 
	 
	 }else{
	     // echo " yes userid exist";
	   	
	   	$newjsonObject = array(
	 			 'prod_id' => $prod_id,
	 			  'qty' =>1,
	 			  'price' =>$prod_price,
	 			 'date' => $date  );
	 			 
	  	$oldarray = json_decode( $rowProdJsonArray, true) ;
	  	
	  	$prodIDexist = false;
	  	
	  	
	  	 foreach($oldarray as $arraykey) {
			 //  echo "prod id ".$arraykey['prod_id'];
			   
			   if( $prod_id === $arraykey['prod_id'] ){
			   	$prodIDexist = true;
			   	//echo " prodId exist in table ";
			   }
			   	
			  $cartcount = 	$cartcount+1;
			   
		  }
		 
		// echo " cart cou nt ".$cartcount ;
		  if($prodIDexist){
		  
		  		//echo " don't update table";
		  		
		  		$status =1;
				 $msg = "Successfully Product Added into the card";
				 $information  =  array(  'qoute_id' => $qouteId, 
						          'cart_count' => $cartcount ) ;
				 	
		  }else{
		  
		  	 array_push( $oldarray , $newjsonObject   );
		  	 
		  	 
		  	   $tempnewarray = 	 json_encode( $oldarray);
	 			  $cartcount = 	$cartcount+1; 
	 	  
	 	 $stmt2 = $conn->prepare("UPDATE cartdetails SET prod_id=? WHERE user_id=?");
		 $stmt2->bind_param( si, $tempnewarray, $user_id );
		 $stmt2->execute();
		
		 
		$rows=$stmt2->affected_rows;
			//echo " row ".$rows;
			
			if($rows>0){	
			   		//echo " row affected is ";
				   	$status =1;
				 	$msg = "Successfully Product Added into the card";
				 	$information  =  array(  'qoute_id' => $qouteId, 
						                 'cart_count' => $cartcount ) ;
			    	
			
			
			}else{
			
			
				$status =0;
			 	$msg = " Fail to add product into cart.";
			 	$information  =  array(  'qoute_id' => $qouteId, 
						         'cart_count' => $cartcount ) ;
			
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
	