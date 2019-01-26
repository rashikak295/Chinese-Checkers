<?php
if(isset($_POST['submit'])){
	if(!isset($_POST["uname"]) || !isset($_POST['password']) ){
		echo "<script> alert('All fields must be filled out!');</script>";
	}
	else{
		$uname = $_POST['uname'];
		$pass = $_POST['password'];
		include 'connection/connection.php';
		$query = $pdo->prepare('select * from member where uname=?');
		$query->bindValue(1, $uname);
		$query->execute();
		$patternPassword = preg_match("@[A-Za-z0-9]+@", $pass);
		$patternUname = preg_match("@[A-Za-z0-9]+@", $uname);
		if(!$patternUname||!$patternPassword||strlen($pass)<6||strlen($pass)>20||strlen($uname)<3||strlen($uname)>15){
  			echo "<script> alert('The username and password must contain only alphabets and numbers.\\nThe password length should be between 6 to 20 character.\\nThe username length should be between 3 to 15 characters.'); </script>";
		}
		else{
			if($query->rowCount()){
				echo "<script> alert('Username Taken!'); </script>";
			}
			else{
                $options = [
                	'cost'=> 12,
                	'salt' => "qazxswedcvfrtgbnhyujmkiolp",
                ];

                $hash = password_hash($pass, CRYPT_SHA256, $options);
                //echo $hash;
				$query= $pdo->prepare("insert into member(uname,password) values(?,?)");
				$query->bindValue(1, $uname);
				$query->bindValue(2, $hash);
				$query->execute();
				header('location:./SignIn.php');
			}
		}
	}
}
	echo '
	<title>Signup</title>
	<link rel="stylesheet" type="text/css" href="../styles/style.css" />
	</head> 
	<body background="../images/bg.jpg" align="middle">
	<br><br>
	<img src="../images/logo.png" height="170px" width="190px"/>
	<h1>Signup!</h1>
	<form name="signup" align="center" action="" enctype="multipart/form-data" method="post">
	<input type="text" name="uname"  placeholder="Username"  />
	<input type="password" name="password" id="password" placeholder="Password" />
	<br>
	<input type="submit" name="submit" value="Submit" />
	</form>
	';
	?>