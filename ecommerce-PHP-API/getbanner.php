<?php

include('db_connection.php');

$securecode =  htmlentities($_POST['securecode'] );

// remove back slash from the variable if any...


$securecode =  "1234";//  stripslashes($securecode);  //  "1234567890";//

//echo "  outside ";

if(isset($securecode)  && !empty($securecode) ) {

global $conn;
	
	if($conn-> connect_error){
		die(" connecction has failed ". $conn-> connect_error)	;
	}
	// get current date
	
	$status =0;
	$msg =" no banner ads";
	$Information =  array(); 
	$jsonarray =   array();
	$count =0;
	
	// ORDER BY id ASC|DESC;
	//echo "  inside ";
	
	///  select prod_id from trending where order by priority ASC
	
	// selectprod_name, prod_mp, prod_price, prod_rating from productdetails WHERE prod_id =  prod_id
	
		
 	$stmt = $conn->prepare("SELECT img_url FROM adsbanner");
 	
 	
 	//$stmt-> bind_param("s", $phone);
 	$stmt->execute();
 	$stmt->store_result();
 	$stmt->bind_result ( $col1  );
 
 	
 	while($stmt->fetch() ){
 	
 			        //echo "  stam extecute ".$col1."  prod_name is  ".$col2;
 				$status =1;
				$msg =" banner image is here";
				$jsonarray[$count] = array(
	 					 'imgurl' => $col1		 );
 			
 		
 		$count = $count+1;				
 	}
  	
	//$Information = $jsonarray;

 	
 	mysqli_close($conn);
 	

 }
 
 	
 	$post_data = array(
 			 'status' => $status,
 			 'msg' => $msg,
 			 'Information' => $jsonarray );
 	
 	
 	$post_data= json_encode( $post_data );
 	
 	echo $post_data;
 	
 		

?>