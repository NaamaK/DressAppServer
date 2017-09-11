<?php
	header('content-type: application/json; charset=utf-8');
	header("access-control-allow-origin: *");
	require_once 'config.php';

	$outfitsArr = array();
	$msg = null;

    if (isset($_POST['item_id'])) {
        $itemID = $_POST['item_id'];

        $find_outfit_query = "SELECT * FROM `outfits`";
        $result = mysql_query($find_outfit_query) or die("Query not retrieved:  " .mysql_error());

        while ($row = mysql_fetch_assoc($result)) {
            $items = explode(",", $row['items']);
            foreach ($items as $item) {
                if ($item == $itemID) {
                    array_push($outfitsArr, $row);
                }
            }
        }

        if (sizeof($outfitsArr) == 0) $msg = 'No matching outfits was found';       
    }
    else $msg = 'Request not found';

    if ($msg != null) 	echo '{"Status": "Failed", "Error": "' .$msg .'"}';
	else 				echo json_encode($outfitsArr);

    header('Content-type: application/json');

?>