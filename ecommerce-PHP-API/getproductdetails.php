<?php

include('db_connection.php');

$securecode =  htmlentities($_POST['securecode'] );
$prod_id =  htmlentities($_POST['prod_id'] );

// remove back slash from the variable if any...


$securecode =   stripslashes($securecode);  //  "1234567890";//
$prod_id =     stripslashes($prod_id);   //  "1";//

//echo "  outside ";

if(isset($securecode)  && !empty($securecode) && isset($prod_id)  && !empty($prod_id) ) {

global $conn;
	
	if($conn-> connect_error){
		die(" connecction has failed ". $conn-> connect_error)	;
	}
	// get current date
	
	$status =0;
	$msg ="No product found";
	$Information = "No product found";
	$detailsarray =  array();
	//$count =0;
	

	$review =0;
	$subcat_id = 0;
	
	$relatedprod = array();
	$relatedcount = 0;
	
	
		
 	$stmt = $conn->prepare("SELECT  proddetail.prod_name, proddetail.prod_desc, proddetail.prod_mrp, proddetail.prod_price, proddetail.prod_rating, proddetail.prod_rating_count, proddetail.prod_img_url, proddetail.review_id, producttable.prod_subcat_id, proddetail.prod_fulldetail, producttable.prod_stock FROM product producttable, productdetails proddetail WHERE producttable.prod_id = proddetail.prod_id AND  proddetail.prod_id=?");
 	
 	
 	$stmt-> bind_param(i, $prod_id );
 	$stmt->execute();
 	$stmt->store_result();
 	$stmt->bind_result ( $col1, $col2, $col3, $col4,  $col5, $col6, $col7, $col8, $col9, $col21, $col22  );
 
 	
 	while($stmt->fetch() ){
 	
 			        //echo "  stam extecute ".$col1."  prod_name is  ".$col2;
 				$status =1;
				$msg ="product details is here";
				$detailsarray = array(
	 					 'name' => $col1,
	 					 'desc' => $col2,	 					 
	 					 'mrp' => $col3,
	 					 'price' => $col4,	 			 
	 					 'rating' => $col5,
	 					 'rating_count' => $col6,
	 					 'img_url' => $col7,
	 					 'fulldetail' => $col21,
	 					 'stock' => $col22	 );
	 					 
 				$review = $col8;
 				$subcat_id = $col9;
 		
 						
 	}
 	
 	
 	
 	// get review from review table
 	$stmt11 = $conn->prepare("SELECT review_array FROM review WHERE review_id =?");
	$stmt11->bind_param( i, $review );
	 $stmt11->execute();
	 $stmt11->store_result();
	 $stmt11->bind_result ( $col10);
	 
	 
	 while($stmt11->fetch() ){
 	
 		//echo "review is ".$col10;
  	}
  	
  	
  	//echo "review is ".$review."---".$col9;
	
	/// get related product
	
	$relatedarray =  array();
	$relatedcount =0;
	
	$stmt = $conn->prepare("SELECT proddetail.prod_id,  proddetail.prod_name, proddetail.prod_mrp, proddetail.prod_price, proddetail.prod_rating, proddetail.prod_img_url  FROM product producttable, productdetails proddetail  WHERE producttable.prod_subcat_id = ? AND producttable.prod_id = proddetail.prod_id AND producttable.prod_id <> ? LIMIT 20");
 	
 	
 	$stmt-> bind_param(ii, $subcat_id, $prod_id);
 	$stmt->execute();
 	$stmt->store_result();
 	$stmt->bind_result ( $col11, $col12, $col13, $col14,  $col15, $col16  );
 
 	
 	while($stmt->fetch() ){
 	
 			       // echo "  stam extecute ".$col11. "prod name ".$col12;
 				//$status =1;
				//$msg =" new product details is here";
				$relatedarray[$relatedcount] = array(
	 					 'id' => $col11,
	 					 'name' => $col12,	 					 
	 					 'mrp' => $col13,
	 					 'price' => $col14,	 			 
	 					 'rating' => $col15,
	 					 'img_url' => $col16	 );
 			
 		
 	   $relatedcount = $relatedcount+1;	
  	}
	
	
	//echo " related arrray is ".$relatedarray;
	
	
	 
	
	
	
	$Information = array(
	
			'details' => $detailsarray,
			'review' => $col10,
			'relatedprod' => $relatedarray   );
			

 	
 	mysqli_close($conn);
 	

 }else{
 
	$status =0;
	$msg ="No product found";
	$information ="[]";
  
 }
 

 	
 	$post_data = array(
 			 'status' => $status,
 			 'msg' => $msg,
 			 'Information' => $Information );
 	
 	
 	$post_data= json_encode( $post_data );
 	
 	echo $post_data;
 		

?>