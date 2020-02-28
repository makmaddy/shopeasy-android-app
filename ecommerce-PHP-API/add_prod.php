<?php

include('db_connection.php');

$securecode =  htmlentities($_POST['securecode'] );
$prod_id =  htmlentities($_POST['prod_id'] );
$user_id =  htmlentities($_POST['user_id'] );

// remove back slash from the variable if any...


$securecode =  "1234567890";// stripslashes($securecode);  //   "1234567890";//
$prod_id =      "1";//stripslashes($prod_id);   //  "1";//
$user_id =    "18";// stripslashes($user_id);  "12";//

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
	$Information = "failled to add product into cart";
	$detailsarray =  array();
	//echo "inside if";
	
	$notExist = true;
	$rowUserId =0; 
	$rowQouteId =0;
	$prod_id_array ==  array();
	
	 $stmt11 = $conn->prepare("SELECT user_id, prod_id, qoute_id FROM cartdetails WHERE user_id =?");
	 $stmt11->bind_param( i, $user_id);
	 $stmt11->execute();
	 $stmt11->store_result();
	 $stmt11->bind_result ( $col1, $col2, $col3);
	 	
	 while($stmt11->fetch() ){
	 	
	 	$notExist = false;
	 	$rowUserId = $col1;
	 	$prod_id_array = $col2;
	 	$rowQouteId = $col3;			
	 }
	 
	 if($notExist){
	 
	 	//echo " not exist";
	 	/// get last qoute id 
	
		 $stmt = $conn->prepare("SELECT qoute_id FROM cartdetails ORDER BY user_id DESC LIMIT 1");
		 $stmt->execute();
		 $stmt->store_result();
		 $stmt->bind_result ( $col4);
		 $qouteno = 1000;
		 	
		 while($stmt->fetch() ){
		 
		 		$qouteno = $col4;			
		 }
		 	
		  $qouteno =  $qouteno+1;	
		// echo " qute id ". $qouteno;	
	
	
	 	$prod_id_array[0] = array(
	 			 'prod_id' => $prod_id,
	 			 'date' => $date );
	 	
	 	
	 	$prod_id_array= json_encode( $prod_id_array );
	 	//echo " prod_id_array ".	$prod_id_array;
	 	
		// add prod id into cartdetails table
	 	 $stmt2 = $conn->prepare("INSERT INTO cartdetails ( user_id, prod_id, qoute_id )  VALUES (?,?,?)");
		 $stmt2->bind_param( isi, $user_id,  $prod_id_array, $qouteno);
		 $stmt2->execute();
		 $stmt2->store_result();
		
		if(!empty($stmt2->insert_id)){
		
			$status =1;
			$msg ="Successfully added product into cart.";
			$Information =  $qouteno;
		
				
		}

	 	
	
	}else{
		//echo "id exist ".$rowUserId." qoute id ".$rowQouteId. " products  ".	$prod_id_array;
		
		
		//$rowUserId; 
		//$prod_id_array;
		$oldArray = json_decode($prod_id_array, true);
 		$newEntry =  array(
	 			 'prod_id' => $prod_id,
	 			 'date' => $date );
	 			 
 		array_push( $oldArray, $newEntry );
		
		//echo " old arrays is ".json_encode( $oldArray);
		$newProdArray = json_encode( $oldArray);
		
		$stmt11 = $conn->prepare("UPDATE cartdetails SET prod_id=? WHERE user_id=?") ;			
		$stmt11->bind_param( si, $newProdArray, $rowUserId);
		$stmt11->execute();
		  	 
		// check whether password already exist on same row or not	   	
		 $rows=$stmt11->affected_rows;
		//echo " row ".$rows;
		if($rows>0){	
		   	//echo " row affected is ";
			$status =1;
			$msg ="Successfully added product into cart.";
			$Information =  $rowQouteId;
			
		}else{
			
			$status =0;
		 	$msg = "Falied to add product into cart";
		 	$Information  = "Please try again.";
		}
				
	
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
	