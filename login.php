<?php
	header('content-type: application/json; charset=utf-8');
	header("access-control-allow-origin: *");
    require_once 'phpPasswordHashingLib/passwordLib.php';
	require_once 'config.php';
	
	$msg = null;
	$res = null;

    //get values from login.php form
    if (!empty($_POST['email']) && !empty($_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        
        //get the user info from the DB
        $query = "SELECT email, username, user_id, active, is_first_time, password FROM users WHERE email = '$email' ";
        $result = mysql_query($query) or die("failed to login" .mysql_error());
        $row = mysql_fetch_array($result);

		if ($row['email'] == $email) { //if user is found
			if (password_verify($password, $row['password'])) {
				$res = $row;
				foreach ($res as $key => $val) {
					if (is_numeric($key) || $key == 'password') unset($res[$key]);
				}

				if ($row['is_first_time'] == 1) {
					$user_query = "UPDATE `users` SET `is_first_time` = 0 WHERE `email` ='". $email."'";
				    $retval = mysql_query( $user_query, $conn );       
				    if(! $retval ) { //if qurey execution didnt succeed
				        die('Could not enter data: ' . mysql_error());
				    }
				}
				
			}
			else {
				$msg = "Sorry, wrong password";
			}
		}
        else $msg = "Sorry, those credentials don't match";

        if ($msg != null) 	echo '{"status": "Failed", "error": "' .$msg .'"}';
		else 				echo json_encode($res);
    }
	
	header('Content-type: application/json');
	
	
?>