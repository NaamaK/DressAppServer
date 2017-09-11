<?php
	header('content-type: application/json; charset=utf-8');
	header("access-control-allow-origin: *");
    require_once 'phpPasswordHashingLib/passwordLib.php';
	require_once 'config.php';
	
	$msg = null;
	$res = null;

    //get values from login.php form
    if (!empty($_POST['email']) && !empty($_POST['username'])) {
        $email = $_POST['email'];
        $username = $_POST['username'];
        $permissions = "user";
        
        $findUserQuery = "SELECT * FROM users WHERE email = '$email'";
        $findUserResult = mysql_query( $findUserQuery, $conn );
        if (mysql_num_rows($findUserResult) == 0) {	//register
	        $password = "facebook";           //encrypt password before saving it to the DB

	        $query = "INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `my_closet`, `liked_outfits`, `account_permissions`, `active`, `is_first_time`) VALUES ('', '$username', '$email', '$password', NULL, NULL, '$permissions', 1, 0)";
	        
	        $retval = mysql_query( $query, $conn );
	        $insertId = mysql_insert_id();              
	        if(! $retval ) { 
	            die('Could not insert row: ' . mysql_error());
	            $msg = 'Sorry there must have been an issue creating your account';
	        }
	        else {
	            $query = "SELECT * FROM users WHERE email = '$email' ";
	            $result = mysql_query($query) or die("failed to login" .mysql_error());
	            $res = mysql_fetch_array($result);
	            $res['is_first_time'] = 1;
	        }
        }
        else { //login
        	$query = "SELECT email, username, user_id, active, is_first_time, password FROM users WHERE email = '$email' ";
	        $result = mysql_query($query) or die("failed to login" .mysql_error());
	        $row = mysql_fetch_array($result);
			
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

        if ($msg != null) 	echo '{"status": "Failed", "error": "' .$msg .'"}';
		else 				echo json_encode($res);
    }
	
	header('Content-type: application/json');
	
	
?>