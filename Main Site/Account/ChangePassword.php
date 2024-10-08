<?php
	$_loc = "ChangePassword";
	session_start();
	if(!isset($_SESSION['authenticated'])) {
		header('Location: /index.php');
	}
	include('../includes/header.php');
?>
<div id="Body">
	<div id="FrameLogin" style="margin: 10px auto 150px auto; border: black thin solid; padding: 22px; background-color: white;">
		<div id="PaneLogin">
			<h3 style="width: 400px;">Change your password!</h3>
				<?php
					if(isset($_SESSION['status'])) {
						echo "<h4 style='color:red;'>".$_SESSION['status']."</h4>";
						unset($_SESSION['status']);
					}
				?>
				<form name="input" action="/api.php" method="POST" style="text-align: left;">
				<div id="EnterPassword" style="padding-bottom:10px;width:618px">
					<fieldset title="Update your LAMBDA password">
						<legend>Update your LAMBDA password</legend>
						
						<div class="PasswordRow">
							<label class="Label">Original Password:</label>&nbsp;
							<input name="original_password" type="password" tabindex="2" class="TextBox">
						</div>
						
						<br>
						<hr>
						<br>
						
						<div class="PasswordRow">
							<label class="Label">Password:</label>&nbsp;
							<input name="password1" type="password" tabindex="2" class="TextBox">
						</div>
						<div class="ConfirmPasswordRow">
							<label class="Label">Confirm Password:</label>&nbsp;
							<input name="password2" type="password" tabindex="3" class="TextBox">
						</div>
					</fieldset>
				</div>
				<input type="submit" name="change_pass_btn" class="Button" style="border: solid 1px #777; color: #777;" value="Change">
			</form>
		</div>
	</div>
</div>
<?php
	include('../includes/footer.php');
?>
