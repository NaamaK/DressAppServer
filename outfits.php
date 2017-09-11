<?php
	header('content-type: application/json; charset=utf-8');
	header("access-control-allow-origin: *");
	require_once 'config.php';

	$category = "";
    $outfits = array();
    $offset = 0;
	if (isset($_POST['offset'])) $offset = $_POST['offset'];

    if(isset($_POST['category'])) {
        $category = $_POST["category"];
        $search_query= mysql_query("SELECT category, id, img FROM outfits WHERE category = '$category' ORDER BY `outfits`.`id` DESC LIMIT 5 OFFSET " .$offset);;
        while($outfit = mysql_fetch_assoc($search_query)){
	    	array_push($outfits, $outfit);
	    }
        echo json_encode($outfits);
    }
    else if (isset($_POST['stylist'])) {
    	$stylist = $_POST['stylist'];
        
        $load_query = "SELECT category, id, img FROM `outfits` WHERE `stylist` = ".$stylist." ORDER BY `outfits`.`id` DESC";
        $res = mysql_query($load_query);

        while($outfit = mysql_fetch_assoc($res)){
            array_push($outfits, $outfit);
        }

        if (empty($outfits)) echo '{"outfits": "none"}';
        else echo json_encode($outfits);
        

    }
    else if (isset($_POST['user_id'])) {
        $userId = $_POST['user_id'];

        $followingQuery = "SELECT `follow` FROM `users` WHERE `user_id` ='". $userId."'";
        $stylists = mysql_result(mysql_query($followingQuery) ,0);
        
        if (is_null($stylists) || $stylists=="") echo '{"outfits": "none"}';
        else {
            $stylists_outfits = "SELECT category, id, img FROM `outfits` WHERE `stylist` IN (".$stylists.") ORDER BY `outfits`.`id` DESC LIMIT 5 OFFSET " .$offset;
            $res = mysql_query($stylists_outfits);
            while($outfit = mysql_fetch_assoc($res)){
                array_push($outfits, $outfit);
            }
            echo json_encode($outfits);
        }
        
    }
    else {
    	$load_query = "SELECT category, id, img FROM `outfits` ORDER BY `outfits`.`id` DESC LIMIT 5 OFFSET " .$offset;
    	$result = mysql_query($load_query) or die("Query not retrieved:  " .mysql_error());
	    while($outfit = mysql_fetch_assoc($result)){
	    	array_push($outfits, $outfit);
	    }
	    echo json_encode($outfits);
	}
   
?>