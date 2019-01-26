<?php
session_start();
if(isset($_SESSION['loggedin']) && $_SESSION['loggedin'] == true){
	header('Location:./home.php');
}
include './connection/connection.php';
if(isset( $_POST['uname']) && isset($_POST['password']) && strlen($_POST['password'])> 5 && strlen($_POST['password'])<20 && strlen( $_POST['uname']) > 3 && strlen( $_POST['uname']) < 15)
{
  $uname = $_POST['uname'];
  $password = $_POST['password'];
  $options = [
                    'cost'=> 12,
                    'salt' => "qazxswedcvfrtgbnhyujmkiolp",
            ];
  $hash = password_hash($password, CRYPT_SHA256, $options);
  $query = $pdo->prepare('select * from member where uname=? and password=?');
  $query->bindValue(1, $uname);
  $query->bindValue(2, $hash);
  $query->execute();
  if(!$query->rowCount())
  {
    //echo "<script> alert('Sorry! You have entered the wrong username or password.'); </script>";
    header('Location:./SignIn.php');
    exit;
  }
  else
  {	
    session_start();
    $_SESSION['loggedin'] = true;
    $_SESSION['uname'] = $uname;
    $_SESSION['password'] = $password;
    header('Location:./home.php');
    exit;
  }
}
?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" type="text/css" href="../styles/style.css"/>
</head>
<body background="../images/bg.jpg" align="middle">
<br><br>
<img src="../styles/logo.png" height="170px" width="190px"/>
<h1>Welcome!</h1>
<form name="login" action="" method="post">
<input type="text" name="uname" placeholder="Username" > 
<input type="password" name="password" placeholder="Password" >
<input type="submit" name="login" value="Login">
</form>
Not a member? Signup <a href="SignUp.php">here</a>.
</body>
</html>