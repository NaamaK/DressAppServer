<?php
	header('content-type: application/json; charset=utf-8');
	header("access-control-allow-origin: *");
	require_once 'config.php';
	
	if (isset($_POST['user_id'])) {
		$userId = $_POST['user_id'];
		if (isset($_POST['removeItem'])) {
	        $itemToRemove = $_POST['removeItem'];
	        $fetch_item_query = " SELECT `my_closet` FROM users WHERE `user_id` ='". $userId."'";
	        $result = mysql_result(mysql_query($fetch_item_query) ,0);
	        $items_in_closet = explode(",", $result);
	        foreach ($items_in_closet as $key => $value) {
	            if($value == $itemToRemove) {
	                unset($items_in_closet[$key]);
	                break;
	            }
	        }
	        $items_in_closet = implode(",", $items_in_closet);
	        $update_closet_query = "UPDATE `users` SET `my_closet` = '". $items_in_closet ."' WHERE `user_id` ='". $userId."'";
	        $retval = mysql_query( $update_closet_query, $conn );
	        if(! $retval ) { //if qurey execution didnt succeed
	            die('Could not enter data: ' . mysql_error());
	        }
	        echo '{"Status": "Succeed", "msg": "Item ' .$itemToRemove .' removed successfully"}';
	    }
	    else if (isset($_POST['addItemToCloset'])) {
	        $itemid = $_POST['addItemToCloset'];
	        
	        $fetch_item_query = " SELECT `my_closet` FROM users WHERE `user_id` ='". $userId."'";
	        $result = mysql_result(mysql_query($fetch_item_query) ,0);
	        if (strcmp($result,"") == 0) $add_closet_query = "UPDATE `users` SET `my_closet` = '$itemid' WHERE `user_id` ='". $userId."'";
	        else $add_closet_query = "UPDATE `users` SET `my_closet` = CONCAT_WS(',', my_closet, '$itemid') WHERE `user_id` ='". $userId."'";
	            
	        $retval = mysql_query( $add_closet_query, $conn );
	        $insertId = mysql_insert_id();
	        if(! $retval ) { //if qurey execution didnt succeed
	            die('Could not enter data: ' . mysql_error());
	        }
	        else echo '{"Status": "Succeed", "msg": "Item ' .$itemid .' removed successfully"}';
    	}
    	else if (isset($_POST['checkItemInCloset'])) {
	        $itemid = $_POST['checkItemInCloset'];
	        
	        $isInCloset = "no";
	        $fetch_item_query = " SELECT `my_closet` FROM users WHERE `user_id` ='". $userId."'";
	        $result = mysql_result(mysql_query($fetch_item_query) ,0);
	        $items_in_closet = explode(",", $result);
	        foreach ($items_in_closet as $key => $value) {
	            if($value == $itemid) {
	                $isInCloset = "yes";
	                break;
	            }
	        }
	        echo '{"isInCloset": "' .$isInCloset .'"}';
    	}
	    else {
			$find_closet_query = "SELECT `my_closet` FROM users WHERE `user_id` ='". $userId."'";
		    $closet = mysql_result(mysql_query($find_closet_query) ,0);
		    if (is_null($closet) || $closet=="") echo '{"items": "none"}';
		    else {
		    	$items_in_closet = explode(",", $closet);
				$myCloset = array();

			    foreach ($items_in_closet as $item) {
			        $fetch_item_query = " SELECT * FROM items3 WHERE `item_id` = '".$item."' ";
			        $result = mysql_query($fetch_item_query) or die("Query not retrieved:  " .mysql_error());
			        $itemRow = mysql_fetch_array($result);
			        array_push($myCloset, $itemRow);
			    }

			    echo json_encode($myCloset);
		    }
		    
	    }
	}
	

    header('Content-type: application/json');

?>