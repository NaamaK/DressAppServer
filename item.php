<?php
	
	include_once('config.php');
	
	header('content-type: application/json; charset=utf-8');
	header("access-control-allow-origin: *");
	
	$item = null;
	
	if (!empty($_POST['qr_code'])) {
		$qr_code = $_POST['qr_code'];
		$item_image = '';

		$query = "SELECT * FROM items3 WHERE qr_code = '$qr_code' ";
		$result = mysql_query($query) or die("Query not retrieved:  " .mysql_error());
		$item =  mysql_fetch_array($result);

		if ($item['qr_code'] == $qr_code) {
			$item_image = $item['image'];
		}
		else echo '{"status": "Failed", "error": "There is no item with this qr-code"}';
			  
	}
	
	else if (!empty($_POST['item_id'])) {
		$item_id = $_POST['item_id'];
		$item_image = '';

		$query = "SELECT * FROM items3 WHERE item_id = '$item_id' ";
		$result = mysql_query($query) or die("Query not retrieved:  " .mysql_error());
		$item =  mysql_fetch_array($result);

		if ($item['item_id'] == $item_id) {
			$item_image = $item['image'];
		}
		else echo '{"status": "Failed", "error": "There is no item with this item-id"}';
			  
	}
	
	header('Content-type: application/json');
	if ($item == null) 	echo '{"status": "Failed", "error": "There is no GET request"}';
	else 				echo json_encode($item);

?>