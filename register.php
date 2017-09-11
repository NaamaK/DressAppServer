<?php
    header('content-type: application/json; charset=utf-8');
    header("access-control-allow-origin: *");
    require_once 'phpPasswordHashingLib/passwordLib.php';
    require_once 'config.php';
    
    $msg = null;
    $status = null;
    $res = null;

    if (!empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['username'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $username = $_POST['username'];
        $permissions = "user";
        
        $hash = md5(rand(0,1000));
        $password = password_hash($password,PASSWORD_BCRYPT);       //encrypt password before saving it to the DB

        $query = "INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `my_closet`, `liked_outfits`, `account_permissions`, `hash`) VALUES ('', '$username', '$email', '$password', NULL, NULL, '$permissions', '$hash')";
        
        $retval = mysql_query( $query, $conn );
        $insertId = mysql_insert_id();              
        if(! $retval ) { 
            die('Could not insert row: ' . mysql_error());
            $status = "Failed";
            $msg = 'Sorry there must have been an issue creating your account';
        }
        else {
            $query = "SELECT * FROM users WHERE email = '$email' ";
            $result = mysql_query($query) or die("failed to login" .mysql_error());
            $row = mysql_fetch_array($result);

            if ($row['active'] == 0) {
                $to      = $email;
                $subject = 'Your account is almost active!';


                $message = '<!DOCTYPE html>
                            <html>
                            <head>
                                <style type="text/css">
                                *{
                                    margin:3px auto !important;
                                    font-family: Tahoma,Verdana,Segoe,sans-serif; 
                                }
                                body {
                                    max-width: 800px;
                                }
                                h1 {
                                    color: grey;
                                    font-weight: normal;
                                }
                                .btn {
                                    background-color:#3379b7;
                                    -moz-border-radius:5px;
                                    -webkit-border-radius:5px;
                                    border-radius:5px;
                                    display:inline-block;
                                    cursor:pointer;
                                    color:#ffffff;
                                    font-family:Arial;
                                    font-size:21px;
                                    font-weight:bold;
                                    padding:10px 23px;
                                    text-decoration:none;
                                }
                                .btn:hover {
                                    background-color:#286090;
                                }
                                .btn:active {
                                    position:relative;
                                    top:1px;
                                }
                                </style>
                            </head>
                            <body style="text-align: center;">
                                <table id="tab">
                                    <tr>
                                        <img src="http://www.dressapp.org/pics/logo.png">
                                    </tr>
                                    <tr>
                                        <hr><br><br>
                                    </tr>
                                    <tr>
                                        <h1>Welcome to DressApp, ' .$row['username'] .'!</h1> <br><br>
                                    </tr>
                                    <tr>
                                        <p>Your account is almost ready. </p><br>
                                    </tr>
                                    <tr>
                                        <a href="http://www.dressapp.org/serverSide/verify.php?email='.$email.'&hash='.$hash .'" class="btn"><b>Activate your account</b></a><br><br>
                                    </tr>
                                </table>
                            </body>
                            </html>';
                                     
                $headers = 'From: DressApp' . "\r\n";
                $headers .= "Reply-To: support@dressapp.org" . "\r\n";
                $headers .= "MIME-Version: 1.0\r\n";
                $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";
                mail($to, $subject, $message, $headers);
            }
            else {
                $status = "Success";
                $msg = 'Go to your inbox and verify your account <br>';
            }
        }
    }

    if ($msg != null)   echo '{"status": "' .$status .'", "Message": "' .$msg .'"}';
    else                echo '{"status": "Success", "message": "created"}';

    header('Content-type: application/json');
    
    
?>