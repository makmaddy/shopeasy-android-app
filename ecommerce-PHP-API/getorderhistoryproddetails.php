<?php

include('db_connection.php');

$securecode =  htmlentities($_POST['securecode'] );
$user_id =  htmlentities($_POST['user_id'] );
$order_id =  htmlentities($_POST['order_id'] );

// remove back slash from the variable if any...


$securecode = stripslashes($securecode);  //  "1234567890";//
$user_id =    stripslashes($user_id);
$order_id =    stripslashes($order_id);


//echo "  outside ";

	
	$orderid ="";
	$address = "";
	$price ="";
	$date ="";
	
	$status =0;
	$msg ="Unable to find product.";
	$information = array();
	$prodJsonarray_new = array();
	$subtotal = 0;
	$shippingfee = 10;
	$grandtotal = 0;
	
	$count =0;
	
	$emptyorderhistory = true;
	
	

if(isset($securecode)  && !empty($securecode)  && !empty($user_id) && !empty($order_id)  ) {

global $conn;
	
	if($conn-> connect_error){
		die(" connecction has failed ". $conn-> connect_error)	;
	}
	// get current date
	
	
	
	
	 $stmt = $conn->prepare("SELECT prod_details, address_id, total_price, create_date FROM orders WHERE user_id=? AND order_id=?");
	 $stmt ->bind_param(ii, $user_id, $order_id);
	 $stmt->execute();
	 $stmt->store_result();
	 $stmt->bind_result ( $col1, $col2, $col3, $col4);
	 
	 while($stmt->fetch() ){
	 
	 		//echo "  first query ".$col1;
	 		$emptyorderhistory = false;
			$prod_array  = $col1;
	 		$address  = $col2;
	 		$price  = $col3;
	 		$date   = $col4;
	 		
	 
	 }	
	
	  // get prod name from table product details using prod id
		  
		$prodJsonarray = json_decode( $prod_array , true) ;
	  	
	  	$prodIDexist = false;
	  	$countobj =0;
	  	//echo " prodjsonarray ". $prodJsonarray;
	  	
	  	 foreach($prodJsonarray as $arraykey) {
			 
			 $prod_id = $arraykey['prod_id'];
			// echo " prod id is ". $prod_id;
			
			$subtotal =  $subtotal  + $arraykey['price'] * $arraykey['qty'];
			
			 $stmt = $conn->prepare("SELECT prod_name FROM productdetails WHERE prod_id=?");
			 $stmt ->bind_param(i, $prod_id);
			 $stmt->execute();
			 $stmt->store_result();
			 $stmt->bind_result ( $col5);
			 
			 while($stmt->fetch() ){
			 			//echo " prod name ".$col5;
			 			
			 		//array_push($prodJsonarray[$countobj]['image'],  $col5);
				 $prodJsonarray[$countobj]['prod_name'] =  $col5;
				
			}
			 $status = 1;
			 $msg =" order product details is here";
			
			$countobj = $countobj+1;
			   
		  } // foreach close
		 
	 $prodJsonarray_new =  $prodJsonarray ;
    // echo "new array ".  $prodJsonarray_new;
 	
 	mysqli_close($conn);
 	

 }
 
 
 	  $grandtotal = $subtotal + 10 + 2;
 	  
 	$post_data = array(
 			 'status' => $status,
 			 'msg' => $msg,
 			 'Information' => $prodJsonarray_new,
 			 'subtotal' => $subtotal,
 			  'shippingfee' =>   '$12',
 			  'grandtotal' => $grandtotal );
 	
 	
 	$post_data= json_encode( $post_data );
 	
 	echo $post_data;
 	
 		

?>