<?php 
	include("../includes/header.php");
	
	if(!isset($_SESSION['authenticated'])) {
		header('Location: /index.php');
		exit();
	}
	
	if(!isset($_GET['id'])) {
		header('Location: /Games.php');
	} else {
		if (!is_numeric($_GET['id'])) {
			header('Location: /Games.php');
		} else {
			$id = $_GET['id'];
		}
	}
	
	$stmt = $con->prepare('SELECT * FROM places WHERE id LIKE ?');
	$stmt -> bind_param('s', $id);
	$stmt -> execute();
	
	$stmt_result = $stmt->get_result();
	$result = $stmt_result->num_rows;
	
	while($row = $stmt_result->fetch_assoc()) {
		$game_id = $row['id'];
		$creator = $row['creator'];
		$name = $row['name'];
		$pending = $row['pending'];
		$description = $row['description'];
	}
	
	if($creator != $auth_user_id) {
		header("Location: /User.php?id=$creator");
	}
	
	
?>
<div id="Body" style="text-align: center; margin: 10px auto 150px auto; width: 855px; border: black thin solid; padding: 22px; background-color: white;">
	<h3>Editing for "<?= $name ?>"</h3>
	<form name="input" action="/api.php" method="POST" style="text-align: left;" enctype="multipart/form-data">
		<div style="border: 2px dashed gray;width: 430px;float:right">
			<img width="430" height="230" src="https://asset.lambda.cam/place?id=<?= $id ?>&type=img" style="float: right;">
			<div style="border: dashed 1px #555;color: #555;font: normal 1em/normal Verdana, sans-serif;max-height: 75px;line-height: 1.7em;margin: 5px 0 0 0;padding: 5px 10px;overflow: auto;text-align: left;">
				<span style="">Description: <?= $description ?></span>
			</div>
		</div>
		<h4>Choose cover image!</h4>
		<input type="hidden" name="id" value="<?= $game_id ?>">
		<input class="Button" type="file" name="cover" id="cover" accept="image/*">
		<br><br>
		
		<label class="Label"><em>D</em>escription:</label>
		<div class="AspNet-Login-PasswordPanel">
			<textarea type="text" name="description" class="TextBox" value="" tabindex="4" accesskey="d" style="min-width: 180px; max-width: 470px; width: 384px; min-height: 16px; height: 219px;"><?= $description ?></textarea>
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
		<input class="Button" type="submit" name="update_place_btn" value="Update place!">
	</form>
	
	<div id="ctl00_cphRoblox_ie6_peekaboo" style="clear: both"></div>
</div>
<?php 
	include("../includes/footer.php");
?>