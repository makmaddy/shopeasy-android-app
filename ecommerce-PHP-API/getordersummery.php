<?php

include('db_connection.php');

$securecode =  htmlentities($_POST['securecode'] );
$user_id =  htmlentities($_POST['user_id'] );
$qoute_id =  htmlentities($_POST['qoute_id'] );
// remove back slash from the variable if any...


$securecode =     stripslashes($securecode);  //  "1234567890";//
$user_id =     stripslashes($user_id);
$qoute_id =   stripslashes($qoute_id);

//echo "  outside ";

	$status =0;
	$jsonarray =  array();
	$rowProdJsonArray = array();
	$subtotal =0;
	$ordertotal =0;
	$msg ="No Product exist on User cart";
	$information = array(  		  'prod_details' => $jsonarray, 
					   'subtotal' =>   $subtotal ,
					   'shippingfee' =>   '$10', 
					   'tax' =>   '$2' ,
					   'ordertotal' =>   $ordertotal ) ;
	
	$count =0;
	$notExist = true;
	
	

if(isset($securecode)  && !empty($securecode)  && !empty($user_id)  && !empty($qoute_id) ) {

global $conn;
	
	if($conn-> connect_error){
		die(" connecction has failed ". $conn-> connect_error)	;
	}
	// get current date
	
	
	
	
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
	 
	//$msg = "No Product exist on User 888 cart ". $notExist ; 
	 if( $notExist ){
	 		// user didn't add any product till now
	 		$status =1;
			$msg ="No Product exist on User cart";
			$information = array(  		  'prod_details' => $jsonarray, 
							   'subtotal' =>   $subtotal ,
							   'shippingfee' =>   '$10', 
							   'tax' =>   '$2' ,
							   'ordertotal' =>  $ordertotal  ) ;
	 
	 
	 }else {
	 
	 		
	 	$oldarray = json_decode( $rowProdJsonArray, true) ;
	  	
	  	$prodIDexist = false;
	  	
	  	 foreach($oldarray as $arraykey) {
			 //  echo "prod id ".$arraykey['prod_id'];
			 // for each product id get product details from table productdetails  
			 
			 
			 $stmt = $conn->prepare("SELECT prod_id, prod_name, prod_price FROM productdetails WHERE prod_id=?");
			 $stmt ->bind_param(i, $arraykey['prod_id']);
			 $stmt->execute();
			 $stmt->store_result();
			 $stmt->bind_result ( $col1, $col2, $col3);
			  
			   while($stmt->fetch() ){
	 
			 			
						$msg =" user cart details is here";
			
			        $subtotal =  $subtotal  + $col3 * $arraykey['qty'];
			       
				$jsonarray[$count] = array(
	 					 'id' => $col1,
	 					 'name' => $col2,				 	
	 					 'price' => $col3,	 			 
	 					  'qty' => $arraykey['qty']	 );
	 					 
			 	$count = $count+1;				
			 }
			  
			  
			   
		  }
		  
		  $status =1;
		  
		  $ordertotal = $subtotal + 10 + 2;
		  
		  $information = array(  'prod_details' => $jsonarray, 
					   'subtotal' =>   $subtotal ,
					   'shippingfee' =>   '$10', 
					   'tax' =>   '$2' ,
					   'ordertotal' =>   $ordertotal  ) ;
						
						//$jsonarray;
		//  $msg = "No Product exist on ---cart ". $notExist ;
		  
	 
	 }
	 
  	
	

 	
 	mysqli_close($conn);
 	

 }else{
 
			$status =1;
			$msg ="No Product exist on User cart";
			 $information = array(  'prod_details' => $jsonarray, 
					   'subtotal' =>   $subtotal ,
					   'shippingfee' =>   '$10', 
					   'tax' =>   '$2' ,
					   'ordertotal' =>   $ordertotal  ) ;
 
 
 }
 
 
 	
 	$post_data = array(
 			 'status' => $status,
 			 'msg' => $msg,
 			 'Information' => $information );
 	
 	
 	$post_data= json_encode( $post_data );
 	
 	echo $post_data;
 	
 		

?>