<?php
	
	session_start();
	
	if(!isset($_SESSION['authenticated'])) {
		$_SESSION['status'] = "not authenticated";
		header('Location: /index.php');
	}
	
	include('includes/header.php');
?>
<div id="Body" style="text-align: center; margin: 10px auto 150px auto; width: 855px; border: black thin solid; padding: 22px; background-color: white;">
	
	<h3>Place Uploader!</h3>
	<?php
		if(isset($_SESSION['status'])) {
			echo "<h4 style='color:red;'>".$_SESSION['status']."</h4>";
			unset($_SESSION['status']);
		}
	?>
	
	<form name="input" action="/api.php" method="POST" style="text-align: left;" enctype="multipart/form-data">
		<label class="Label"><em>N</em>ame:</label>
		<div class="AspNet-Login-PasswordPanel">
			<input name="name" type="text" tabindex="1" accesskey="n" class="TextBox">
		</div>
		
		<h4>Choose cover image!</h4>
		<input class="Button" type="file" name="cover" id="cover" accept="image/*">
		<br><br>
		<h4>Choose place file! MAX SIZE: 100MB (rbxl (XML FORMAT))</h4>
		<input class="Button" type="file" name="place" id="place" accept=".rbxl,.rbxlx">
		<br><br>
		<label class="Label"><em>D</em>escription:</label>
		<div class="AspNet-Login-PasswordPanel">
			<textarea type="text" name="description" class="TextBox" value="" tabindex="4" accesskey="d" style="min-width:180px; max-width:470px; width: 470px; min-height:16px; height: 150px;"></textarea>
		</div>
		<br>
		<label class="Label">Version: </label>
		<select id="gameversion" name="gameversion" class="TextBox" style="padding: 0px;">
			<option value="2008">2008</option>
			<option value="2009">2009</option>
			<option value="2010">2010</option>
			<option value="2011">2011</option>
			<option value="2012">2012</option>
			<option value="2013">2013</option>
		</select>
		<br><br>
		<input class="Button" type="submit" name="upload_place_btn" value="Upload place!">
	</form>
	
	<div id="ctl00_cphRoblox_ie6_peekaboo" style="clear: both"></div>
</div>
<?php
	include('includes/footer.php');
?> 