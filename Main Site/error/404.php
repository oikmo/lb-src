<?php
	include('../includes/header.php');
	$images = [
		"/images/error/bigrat.png",
		"/images/error/barney-smoove.gif",
		"/images/error/jerma-laughing.gif",
		"/images/error/jerma-ate.jpg",
		"/images/error/portalcake.jpg",
		"/images/error/twatt.jpg",
	];
	$rand_image = $images[array_rand($images)];
?>
<div id="Body" style="text-align: center; margin: 10px auto 150px auto; width: 855px; border: black thin solid; padding: 22px; background-color: white;">
	
	<h3>OH NOES!</h3>
	<h5>404</h5>
	<?php echo "<img title='OH NOES' src='$rand_image'>"; ?>
	
	<div id="ctl00_cphRoblox_ie6_peekaboo" style="clear: both"></div>
</div>
<?php
	include('../includes/footer.php');
?>