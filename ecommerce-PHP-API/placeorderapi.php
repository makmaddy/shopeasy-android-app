<?php

include('db_connection.php');

$securecode =  htmlentities($_POST['securecode'] );

$user_id =  htmlentities($_POST['user_id'] );
$paymentorder_id =  htmlentities($_POST['paymentorder_id'] );
$payment_id =  htmlentities($_POST['payment_id'] );
$address_id =  htmlentities($_POST['address_id'] );
$total_price =  htmlentities($_POST['total_price'] );
$qoute_id =  htmlentities($_POST['qoute_id'] );
$deliverymode =  htmlentities($_POST['deliverymode'] );

// remove back slash from the variable if any...


$securecode =   stripslashes($securecode);  //   "12345"; //

$user_id =    stripslashes($user_id);  //"10010"; //
$paymentorder_id =   stripslashes($paymentorder_id); // "orderid124534"; //
$payment_id =  stripslashes($payment_id );  // "pay id 2548" ;  ///
$address_id =  stripslashes($address_id );  //  "1"; //
$total_price = stripslashes($total_price ); //  "150"; //
$qoute_id =   stripslashes($qoute_id );  // "1001"; //
$deliverymode =   stripslashes($deliverymode );

//echo "  outside ";

if(isset($securecode)  && !empty($securecode) && isset($address_id)   && !empty($user_id) && !empty($payment_id) && !empty($paymentorder_id)) {

global $conn;
	
	if($conn-> connect_error){
		die(" connecction has failed ". $conn-> connect_error)	;
	}
	// get current date
	date_default_timezone_set("Asia/Kolkata");
	$date = date("Y-m-d");	


	$status =0;
	$msg ="Transaction has failed. Please try again";
	$Information = array(  'status' => "Transaction has failed. Please try again",
	 			'orderId' => "" );
	 $prod_details = "";
	 			
	$status ="place"; 			
	
	 $stmt = $conn->prepare("SELECT prod_id FROM cartdetails WHERE user_id=?");
	 $stmt ->bind_param(i, $user_id);
	 $stmt->execute();
	 $stmt->store_result();
	 $stmt->bind_result ( $col1);
	 
	 while($stmt->fetch() ){
	 
	 		$prod_details = $col1;	
	 					
	 }

		
	 	
	
	// add  into orders table
	 	 $stmt2 = $conn->prepare("INSERT INTO orders ( user_id, status, prod_details, address_id, total_price, payment_orderid, payment_id, delivery_mode, qoute_id, create_date, update_date )  VALUES (?,?,?,?,?,?,?,?,?,?,?)");
		 $stmt2->bind_param( issiisssiss, $user_id,  $status, $prod_details, $address_id, $total_price, $paymentorder_id, $payment_id, $deliverymode, $qoute_id, $date, $date);
		 $stmt2->execute();
		 $stmt2->store_result();
		 
		// echo " insert row ".$stmt2->insert_id;
		
		if(!empty($stmt2->insert_id)){
		
			$status =1;
			$msg ="Order has placed Successful.";
			$Information =  array(  'status' => "Order has placed Successfully. We will SMS you the order confirmation with details.",
	 					'orderId' => $stmt2->insert_id );
	 					
	 		/// delete user cart details from cartdetails table	
	 		 $stmt3 = $conn->prepare("DELETE FROM cartdetails WHERE user_id=?");
			 $stmt3 ->bind_param(i, $user_id);
			 $stmt3->execute();
			
		
				
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