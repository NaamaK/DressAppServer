<?php
	header('content-type: application/json; charset=utf-8');
	header("access-control-allow-origin: *");
	require_once 'config.php';
	
	if (isset($_POST['stylistID'])) {
        $stylistID = $_POST['stylistID'];
        $stylist_query = "SELECT * FROM `stylists` WHERE stylist_id = '".$stylistID."'";
        $result = mysql_query($stylist_query) or die("failed to login " .mysql_error());
        $stylistRow = mysql_fetch_assoc($result);

        $user_info_query = "SELECT username, email FROM users WHERE `user_id` = ". $stylistID;
        $result_info = mysql_query($user_info_query) or die("failed to gather info about user " .mysql_error());
        $stylistInfo = mysql_fetch_assoc($result_info);

        $stylistRow['username'] = $stylistInfo['username'];
        $stylistRow['email'] = $stylistInfo['email'];
        echo json_encode($stylistRow);
    }
    else if (isset($_POST['user_id'])) {
		$userId = $_POST['user_id'];

	    if (isset($_POST['follow'])) {
	        $stylist = $_POST['follow'];

	        $fetch_follow_query = " SELECT `follow` FROM users WHERE `user_id` ='". $userId."'";
	        $result = mysql_result(mysql_query($fetch_follow_query) ,0);
	        if (strcmp($result,"") == 0) $follow_query = "UPDATE `users` SET `follow` = '$stylist' WHERE `user_id` ='". $userId."'";
	        else $follow_query = "UPDATE `users` SET `follow` = CONCAT_WS(',', follow, '$stylist') WHERE `user_id` ='". $userId ."'";
	            
	        $retval = mysql_query( $follow_query, $conn );
	        if(! $retval ) { //if qurey execution didnt succeed
	            die('Could not enter data: ' . mysql_error());
	        }
	        else echo '{"Status": "Succeed", "msg": "stylist ' .$stylist .' was followed"}';
	    }

	    else if (isset($_POST['unfollow'])) {
	        $stylist = $_POST['unfollow'];

	        $fetch_follow_query = " SELECT `follow` FROM users WHERE `user_id` ='". $userId."'";
		    $result = mysql_result(mysql_query($fetch_follow_query) ,0);
		    $following = explode(",", $result);
		    foreach ($following as $key => $value) {
		        if($value == $stylist) {
		            unset($following[$key]);
		            break;
		        }
		    }
		    $following = implode(",", $following);
		    $update_follow_query = "UPDATE `users` SET `follow` = '". $following ."' WHERE `user_id` ='". $userId."'";
		    $retval = mysql_query( $update_follow_query, $conn );       
		    if(! $retval ) { //if qurey execution didnt succeed
		        die('Could not enter data: ' . mysql_error());
		    }

		    echo '{"Status": "Succeed", "msg": "Stylist ' .$stylist .' was unfollowed"}';

	    }

	    else if (isset($_POST['checkIfFollowed'])) {
	        $stylist = $_POST['checkIfFollowed'];
	        
	        $isFollowed = "no";
	        $fetch_follow_query = " SELECT `follow` FROM users WHERE `user_id` ='". $userId."'";
	        $result = mysql_result(mysql_query($fetch_follow_query) ,0);
	        $following = explode(",", $result);
	        foreach ($following as $key => $value) {
	            if($value == $stylist) {
	                $isFollowed = "yes";
	                break;
	            }
	        }
	        echo '{"isFollowed": "' .$isFollowed .'"}';
    	}

    	else {
    		$stylistsArr = array();
			$obj = array();

	        $fetch_follow_query = " SELECT `follow` FROM users WHERE `user_id` ='". $userId."'";
		    $stylists = mysql_result(mysql_query($fetch_follow_query) ,0);

		    $get_stylists_query = "SELECT username, user_id FROM users WHERE `user_id` IN (".$stylists.") ";
		    $result = mysql_query($get_stylists_query) or die("Query not retrieved:  " .mysql_error());
		    while($stylistRow = mysql_fetch_assoc($result)){
		    	$fetch_image_query = " SELECT `profile_image` FROM stylists WHERE `stylist_id` ='". $stylistRow['user_id']."'";
		        $obj['profile_image'] = mysql_result(mysql_query($fetch_image_query) ,0);
		        $obj['username'] = $stylistRow['username'];
		        $obj['id'] = $stylistRow['user_id'];
		    	array_push($stylistsArr, $obj);
		    }
			
		    if (is_null($stylistsArr) || $stylistsArr=="") echo '{"stylists": "none"}';
		    else echo json_encode($stylistsArr);
	    }
	} 
	else {
		$stylists = array();
		$obj = array();

		$get_all_stylists_query = "SELECT username, user_id FROM users WHERE `account_permissions` ='stylist'";
	    $result = mysql_query($get_all_stylists_query) or die("Query not retrieved:  " .mysql_error());
	    while($stylist = mysql_fetch_assoc($result)){
	    	$fetch_image_query = " SELECT `profile_image` FROM stylists WHERE `stylist_id` ='". $stylist['user_id']."'";
	        $obj['profile_image'] = mysql_result(mysql_query($fetch_image_query) ,0);
	        $obj['username'] = $stylist['username'];
	        $obj['id'] = $stylist['user_id'];
	    	array_push($stylists, $obj);
	    }

	    if (is_null($stylists) || $stylists=="") echo '{"stylists": "none"}';
	    else echo json_encode($stylists);
	} 

    header('Content-type: application/json');

?>