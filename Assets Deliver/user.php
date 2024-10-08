<?php
	if(isset($_GET['id'])) {
		$id = $_GET['id'];
		$filename = "/var/www/asset.lambda.cam/users/$id.png"; 
		if(!file_exists($filename)) {
			$filename = "/var/www/asset.lambda.cam/users/default.png";
		} else{
			//chmod($filename, 0777);
			//cho $filename;
			//die();
			
		}
		$handle = fopen($filename, "rb"); 
		$contents = fread($handle, filesize($filename)); 
		fclose($handle); 
		
		header("content-type: image"); 
		 
		echo $contents;
		
		exit;
	}
?>