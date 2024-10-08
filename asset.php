<?php
	if(isset($_GET['type'])) {
		$type = $_GET['type'];
		$label = $_GET['label'];
		//echo "%AiSTLFaCXxH3QDNmXKtMEz6kwm6fWzwDAgqDgU0b+5lnvhOMA4k6yzj+ALst2hFXQ+O/ogU4ftiqw3C8cwnxpJTdXWD/15UlmDZzXxp9PwTJkYD4+jQjqPRfevTfcTMzixv24WJRemhD+vcLmkxJ3Xh+rnXQPe567Qgk5Osx92A=%\r\n";
		if($type == "face") {
			$filename = "assets/faces/$label.png"; 
			$handle = fopen($filename, "rb"); 
			$contents = fread($handle, filesize($filename)); 
			fclose($handle); 
			
			header("content-type: image/png"); 
			echo $contents;
		}
		if($type == "rbxm") {
			$filename = "assets/faces/$label.rbxm"; 
			$handle = fopen($filename, "rb"); 
			$contents = fread($handle, filesize($filename)); 
			fclose($handle); 
			
			header("content-type: text/plain"); 
			 
			echo $contents;
		}
		exit;
	}
?>