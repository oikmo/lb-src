<?php
	if(isset($_GET['type'])) {
		$type = $_GET['type'];
		$id = $_GET['id'];
		if($type == "img") {
			$filename = "/var/www/asset.lambda.cam/places/$id/cover"; 
			$handle = fopen($filename, "rb"); 
			$contents = fread($handle, filesize($filename)); 
			fclose($handle); 
			
			header("content-type: image"); 
			 
			echo $contents;
		}
		if($type == "place") {
			$filename = "/var/www/asset.lambda.cam/places/$id/place.rbxl"; 
			$handle = fopen($filename, "rb"); 
			$contents = fread($handle, filesize($filename)); 
			fclose($handle); 
			
			header("content-type: text/plain"); 
			//echo "%AiSTLFaCXxH3QDNmXKtMEz6kwm6fWzwDAgqDgU0b+5lnvhOMA4k6yzj+ALst2hFXQ+O/ogU4ftiqw3C8cwnxpJTdXWD/15UlmDZzXxp9PwTJkYD4+jQjqPRfevTfcTMzixv24WJRemhD+vcLmkxJ3Xh+rnXQPe567Qgk5Osx92A=%\r\n".$contents;
			echo $contents;
		}
		exit;
	} else {
		$id = $_GET['id'];
		$filename = "/var/www/asset.lambda.cam/places/$id/cover"; 
		$handle = fopen($filename, "rb"); 
		$contents = fread($handle, filesize($filename)); 
		fclose($handle); 
		
		header("content-type: image"); 
		 
		echo $contents;
	}
?>