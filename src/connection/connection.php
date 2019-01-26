<?php
try{
	$pdo = new PDO('mysql:host=127.0.0.1;dbname=login','root','');
}catch(PDOException $e){
	exit('database error');
}
?>
