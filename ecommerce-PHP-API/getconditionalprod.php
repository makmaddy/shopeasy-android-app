<?php

include('db_connection.php');

$securecode =  htmlentities($_POST['securecode'] );

// remove back slash from the variable if any...


$securecode =   stripslashes($securecode);  //  "1234567890";//

//echo "  outside ";

if(isset($securecode)  && !empty($securecode) ) {

global $conn;
	
	if($conn-> connect_error){
		die(" connecction has failed ". $conn-> connect_error)	;
	}
	// get current date
	
	$status =0;
	$msg ="No product found";
	$Information = "No product found";
	$jsonarray =  array();
	$count =0;
	
	// ORDER BY id ASC|DESC;
	//echo "  inside ";
	
	///  select prod_id from trending where order by priority ASC
	
	// selectprod_name, prod_mp, prod_price, prod_rating from productdetails WHERE prod_id =  prod_id
	
		
 	$stmt = $conn->prepare("SELECT proddetail.prod_id,  proddetail.prod_name, proddetail.prod_mrp, proddetail.prod_price, proddetail.prod_rating, proddetail.prod_img_url  FROM conditional trend, productdetails proddetail  WHERE trend.prod_id = proddetail.prod_id ORDER BY trend.priority ASC");
 	
 	
 	//$stmt-> bind_param("s", $phone);
 	$stmt->execute();
 	$stmt->store_result();
 	$stmt->bind_result ( $col1, $col2, $col3, $col4,  $col5, $col6  );
 
 	
 	while($stmt->fetch() ){
 	
 			        //echo "  stam extecute ".$col1."  prod_name is  ".$col2;
 				$status =1;
				$msg =" new product details is here";
				$jsonarray[$count] = array(
	 					 'id' => $col1,
	 					 'name' => $col2,	 					 
	 					 'mrp' => $col3,
	 					 'price' => $col4,	 			 
	 					 'rating' => $col5,
	 					 'img_url' => $col6	 );
 			
 		
 		$count = $count+1;				
 	}
  	
	$Information = $jsonarray;

 	
 	mysqli_close($conn);
 	

 }else{
 
	$status =0;
	$msg ="";
	$information ="";
 
 
 
 
 
 }
 
 
 	
 	$post_data = array(
 			 'status' => $status,
 			 'msg' => $msg,
 			 'Information' => $Information );
 	
 	
 	$post_data= json_encode( $post_data );
 	
 	echo $post_data;
 	
 		

?>