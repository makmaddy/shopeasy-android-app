<?php

include('db_connection.php');

$securecode =  htmlentities($_POST['securecode'] );
$prod_id =  htmlentities($_POST['prod_id'] );
$user_id =  htmlentities($_POST['user_id'] );
$qty =  htmlentities($_POST['prod_qty'] );

// remove back slash from the variable if any...


$securecode =  stripslashes($securecode);  //   "1234567890";//
$prod_id =   stripslashes($prod_id);   //  "1";//
$user_id =    stripslashes($user_id); // "12";//
$qty =  stripslashes($qty); 


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
	$totalPrice =0;
	
	$msg ="failled to edit product from cart quntity";
	$information =   array(  'status' =>  "failled to edit product from cart quntity", 
				'totalprice' =>   $totalPrice  ) ;
	
	
	
	//echo "inside if";
	
	
	
	// check userID exist or not
	$notExist = true;
	$qouteId = 1000;
	$rowUser_id = 0;
	$rowProdJsonArray = array();
	
	
	
	
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
	  /// no  userid doens't exist on table
		  
		$status =1;
		$msg =" No product exist on User cart";
		$information =  array(  'status' =>  "No product exist on User cart", 
					'totalprice' =>   $totalPrice  ) ;
		
	 
	 
	 }else{
	   /// yes userid exist
	   	
	   	$newjsonObject = array(
	 			 'prod_id' => $prod_id,
	 			  'qty' =>1,
	 			 'date' => $date  );
	 			 
	  	$oldarray = json_decode( $rowProdJsonArray, true) ;
	  	
	  	$prodIDexist = false;
	  	
	  	$i =0;
	  	 foreach($oldarray as $arraykey) {
			 //  echo "prod id ".$arraykey['prod_id'];
			   
			   if( $prod_id === $arraykey['prod_id'] ){
			   
			   	$prodIDexist = true;
			   	$oldarray [$i] ['qty'] = $qty;
			   	  
			   	//echo " prodId exist in table ";
			   }
			  // echo "    new ".$arraykey['price'] ."   qyt ".$oldarray [$i] ['qty'];
			   
			 $totalPrice = $totalPrice  + $arraykey['price'] *  	$oldarray [$i] ['qty'];
			 
			 $i++;  
			   
		  }
		  
		  if($prodIDexist){
		  
		  		//echo " don't update table";
		  	// rearrange json array then insert into database
		  	$oldarray = array_values($oldarray);
		  		
      			// echo " item edit array ". json_encode( $oldarray, true) ;
      			  $tempnewarray = 	 json_encode( $oldarray);
      				  
		 	 $stmt2 = $conn->prepare("UPDATE cartdetails SET prod_id=? WHERE user_id=?");
			 $stmt2->bind_param( si, $tempnewarray, $user_id );
			 $stmt2->execute();
			
		 
			$rows=$stmt2->affected_rows;
			//echo " row ".$rows;
			
			if($rows>0){	
			   		//echo " row affected is ".;
				   	$status =1;
				 	$msg = "Successfully  edit qty from cart";
				 	$information  =  array(  'status' =>  "successful update cart", 
					 			  'totalprice' =>   $totalPrice  ) ;
			    	
			
			
			}else{
			
			
				$status =1;
			 	$msg = " Fail to edit qty  from the cart.";
			 	$information  =  array(  'status' =>  "failled to edit cart item. Please try again", 
							'totalprice' =>   $totalPrice  ) ;
			
			}
				 	
		  }else{
		  		$status =1;
			 	$msg = " Fail to edit qty  from the cart.";
			 	$information  =  array(  'status' =>  "failled to edit cart item. Please try again", 
							'totalprice' =>   $totalPrice  ) ;
		  
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
	