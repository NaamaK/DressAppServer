<?php
	header('content-type: application/json; charset=utf-8');
	header("access-control-allow-origin: *");
	require_once 'config.php';
	
	if (isset($_POST['user_id'])) {
		$userId = $_POST['user_id'];

		if (isset($_POST['outfit'])) {
		    $outfitId = $_POST['outfit'];

		    $outfitQuery = "SELECT * FROM `outfits` WHERE id = '$outfitId' ";
		    $result = mysql_query($outfitQuery) or die("Query not retrieved:  " .mysql_error());
		    $items = mysql_fetch_array($result);

		    $outfit = new stdClass();
		    foreach ($items as $key => $value) {
		        if (!is_numeric($key)) {
		            $val = explode(",", $value);
		            if (sizeof($val) > 1) {
		                $i=0;
		                foreach ($val as $info) $outfit->{$key}[$i++] = $info;
		            } else {
		                $outfit->{$key} = $val[0];
		            }
		        }
		    }

		    $items = array();
		    $itemsId = implode(",",$outfit->items);
		    $itemQuery = "SELECT * FROM `items3` WHERE item_id in ($itemsId) ";
		    $result = mysql_query($itemQuery) or die("Query not retrieved:  " .mysql_error());
		    while ($itemRow = mysql_fetch_array($result)) array_push($items, $itemRow);

		    $isLiked = false;
		    $fetch_item_query = " SELECT `liked_outfits` FROM users WHERE `user_id` ='". $userId."'";
		    $result = mysql_result(mysql_query($fetch_item_query) ,0);
		    $liked_outfits = explode(",", $result);
		    foreach ($liked_outfits as $key => $value) {
		        if($value == $outfitId) {
		            $isLiked = true;
		            break;
		        }
		    }

		    $username_query = "SELECT `username` FROM users WHERE `user_id` = ". $outfit->stylist;
        	$stylistUsername = mysql_result(mysql_query($username_query) ,0);

        	$img_query = "SELECT `profile_image` FROM stylists WHERE `stylist_id` = ". $outfit->stylist;
        	$stylistImg = mysql_result(mysql_query($img_query) ,0);

		    $result = array();
		    $result['image'] = $outfit->img;
		    $result['isLiked'] = $isLiked;
		    $result['items'] = $items;
		    $result['stylist']['id'] = $outfit->stylist;
		    $result['stylist']['image'] = $stylistImg;
		    $result['stylist']['username'] = $stylistUsername;

		    echo json_encode($result);
		}

		else if (isset($_POST['likedOutfit'])) {
		    $likedOutfit = $_POST['likedOutfit'];
		    
		    $fetch_item_query = " SELECT `liked_outfits` FROM users WHERE `user_id` ='". $userId."'";
		    $result = mysql_result(mysql_query($fetch_item_query) ,0);
		    if (strcmp($result,"") == 0) $like_outfit_query = "UPDATE `users` SET `liked_outfits` = '$likedOutfit' WHERE `user_id` ='". $userId."'";
		    else $like_outfit_query = "UPDATE `users` SET `liked_outfits` = CONCAT_WS(',', liked_outfits, '$likedOutfit') WHERE `user_id` ='". $userId."'";
		    
		    $retval = mysql_query( $like_outfit_query, $conn );       
		    if(! $retval ) { //if qurey execution didnt succeed
		        die('Could not enter data: ' . mysql_error());
		    }
		    echo '{"Status": "Succeed", "msg": "oufit ' .$likedOutfit .' was liked"}';
		}

		else if (isset($_POST['dislikedOutfit'])) {
		    $dislikedOutfit = $_POST['dislikedOutfit'];
		    
		    $fetch_item_query = " SELECT `liked_outfits` FROM users WHERE `user_id` ='". $userId."'";
		    $result = mysql_result(mysql_query($fetch_item_query) ,0);
		    $liked_outfits = explode(",", $result);
		    foreach ($liked_outfits as $key => $value) {
		        if($value == $dislikedOutfit) {
		            unset($liked_outfits[$key]);
		            break;
		        }
		    }
		    $liked_outfits = implode(",", $liked_outfits);
		    $like_outfit_query = "UPDATE `users` SET `liked_outfits` = '". $liked_outfits ."' WHERE `user_id` ='". $userId."'";
		    $retval = mysql_query( $like_outfit_query, $conn );       
		    if(! $retval ) { //if qurey execution didnt succeed
		        die('Could not enter data: ' . mysql_error());
		    }

		    echo '{"Status": "Succeed", "msg": "oufit ' .$dislikedOutfit .' was disliked"}';
		}

		else if (isset($_POST['checkOutfitLiked'])) {
	        $outfitId = $_POST['checkOutfitLiked'];
	        
	        $isLiked = "no";
	        $fetch_item_query = " SELECT `liked_outfits` FROM users WHERE `user_id` ='". $userId."'";
	        $result = mysql_result(mysql_query($fetch_item_query) ,0);
	        $liked_outfits = explode(",", $result);
	        foreach ($liked_outfits as $key => $value) {
	            if($value == $outfitId) {
	                $isLiked = "yes";
	                break;
	            }
	        }
	        echo '{"isOutfitLiked": "' .$isLiked .'"}';
    	}
	}

    header('Content-type: application/json');

?>