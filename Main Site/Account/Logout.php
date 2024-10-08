<?php
	
	session_start();
	include('../includes/dbcon.php');
	
	$raw_date = new DateTime(null, new DateTimeZone('Europe/London'));
	$status = "0";
	$date = $raw_date->format('Y-m-d H:i:s');
	$stmt = $con->prepare('UPDATE users SET status = ?, last_online = ? WHERE username LIKE ?');
	$stmt -> bind_param('sss', $status, $date, $_SESSION['auth_user']['username']);
	$stmt -> execute();
	
	unset($_SESSION['authenticated']);
	unset($_SESSION['auth_user']);
	session_destroy();
	header("Location: /index.php");
?>