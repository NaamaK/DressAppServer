<?php
	header('content-type: application/json; charset=utf-8');
	header("access-control-allow-origin: *");
	require_once 'config.php';
	
	if (isset($_POST['user_id'])) {
		$userId = $_POST['user_id'];
		
		$liked_outfits_query = "SELECT `liked_outfits` FROM users WHERE `user_id` ='". $userId."'";
	    $outfits = mysql_result(mysql_query($liked_outfits_query) ,0);

	    if (is_null($outfits) || $outfits=="") echo '{"outfits": "none"}';
        else {
			$likedOutfits = explode(",", $outfits);
		    $likedOutfitsList = array();

		    foreach ($likedOutfits as $outfit) {
		        $fetch_outfit_query = " SELECT id, img, category FROM outfits WHERE `id` = '".$outfit."' ";
		        $result = mysql_query($fetch_outfit_query) or die("Query not retrieved:  " .mysql_error());
		        $outfitRow = mysql_fetch_array($result);
		        array_push($likedOutfitsList, $outfitRow);
		    }

		    echo json_encode($likedOutfitsList);
        }


	    
	}	

    header('Content-type: application/json');

?>