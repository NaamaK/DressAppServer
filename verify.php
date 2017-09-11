<?php 
    include 'config.php';

    $accountWasntFound = false;
    if(isset($_GET['email']) && !empty($_GET['email']) AND isset($_GET['hash']) && !empty($_GET['hash'])){
        
        $email = mysql_escape_string($_GET['email']);
        $hash = mysql_escape_string($_GET['hash']);
        
        $search_query = "SELECT email, hash, active, username FROM users WHERE email='".$email."' AND hash='".$hash."'";
        $result = mysql_query($search_query) or die("Query not retrieved:  " .mysql_error());
        $user = mysql_fetch_array($result);
        $match  = mysql_num_rows($result);

        if($match > 0){
            if ($user['active'] == 0) mysql_query("UPDATE users SET active='1' WHERE email='".$email."' AND hash='".$hash."' AND active='0'") or die(mysql_error());
            $username = $user['username'];
        }else{
            $accountWasntFound = true;
        }
    }

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <meta name="viewport" content="width=device-width" />
  <!-- IMPORTANT -->
  <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
  <title>Account Activation</title>

  <link rel="stylesheet" type="text/css" href="verify.css" />
</head>

<body bgcolor="#FFFFFF">

  <table class="body-wrap">
    <tr>
      <td></td>
      <td class="container" bgcolor="#FFFFFF">

        <div class="content">
          <table>
            <tr>
              <td align="center">
                <p><img  src="http://www.dressapp.org/pics/logo.png" alt="DressApp.org" /></p>
              </td>
            </tr>
            <tr>
              <td>
                <br/>
                <h2>Hi <?= $username ?>,</h3> <br/>
                <h4>Your Account was Successfully Activated.</h4><br><br/>
                <p style="text-align: left">Trouble activating? Contact us at <a href="mailto:support@dressapp.org?Subject=Need%20help" target="_top">support@dressapp.org</a></p>
              </td>
            </tr>
          </table>
        </div>
      </td>
    </tr>
  </table>  
</body>

</html>