<?php
	$_loc = "Register";
	session_start();
	if(isset($_SESSION['authenticated'])) {
		header('Location: /index.php');
	}
	include('../includes/header.php');
?>
<div id="Body" style="border: solid 1px #777; background-color:white;">
	<div id="Registration" style="margin:10px">
		<div id="Sidebars" style="margin-left: 10px; margin-bottom: 10px;">
			<div id="AlreadyRegistered">
				<h3>Already Registered?</h3>
				<p>If you just need to login, go to the <a href="/Account/Login.php">Login</a> page.</p>
				<p>If you have already registered but you still need to download the game installer, go directly to <a href="/Downloads.php">download</a>.</p>
			</div>
		</div>
		<form id="upAccountRegistration" name="input" action="/api.php" method="POST" style="text-align: left;">
			<h3>Create Account</h3>
			<?php
				if(isset($_SESSION['status'])) {
					echo "<h4 style='color:red;'>".$_SESSION['status']."</h4>";
					unset($_SESSION['status']);
				}
			?>
			<div id="EnterUsername" style="padding-bottom:10px;width:618px">
				<fieldset title="Enter an invite key!">
					<legend>Enter an invite key</legend>
					<div class="Suggestion">
						Ask Oikmo or staff for a key!
					</div>
					<div class="Validators">
						<div></div><div></div><div></div><div></div><div></div>
					</div>
					<div class="UsernameRow">
						<label class="Label">Key:</label>&nbsp;
						<input name="invite_key" type="text" tabindex="1" class="TextBox">
					</div>
				</fieldset>
			</div>
			<div id="EnterUsername" style="padding-bottom:10px;width:618px">
				<fieldset title="Choose a name for your LAMBDA character">
					<legend>Choose a name for your LAMBDA character</legend>
					<div class="Suggestion">
						Use 3-20 alphanumeric characters: A-Z, a-z, 0-9, no spaces
					</div>
					<div class="Validators">
						<div></div><div></div><div></div><div></div><div></div>
					</div>
					<div class="UsernameRow">
						<label class="Label">Character Name:</label>&nbsp;
						<input name="username" type="text" tabindex="1" class="TextBox">
					</div>
				</fieldset>
			</div>
			<div id="EnterPassword" style="padding-bottom:10px;width:618px">
				<fieldset title="Choose your LAMBDA password">
					<legend>Choose your LAMBDA password</legend>
					<div class="Suggestion">
						No spaces!
					</div>
					<div class="Validators"><div></div><div></div><div></div><div></div></div>
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
			<!--<div id="EnterEmail" style="padding-bottom:10px;width:618px">
				<fieldset title="Provide your email address">
					<legend>Provide your email address</legend>
					<div class="Suggestion">
						This will allow you to recover a lost password
					</div>
					<div class="Validators"><div></div><div></div><div></div></div>
					<div class="EmailRow">
						<label class="Label">Your Email:</label>&nbsp;
						<input name="email" type="text" tabindex="4" class="TextBox">
					</div>
				</fieldset>
			</div>-->
			<div class="AspNet-Login-SubmitPanel">
			<input type="submit" name="register_btn" class="Button" style="border: solid 1px #777; color: #777;" value="Register">
			</div>
		</form>
	</div>
	
	<div id="ctl00_cphRoblox_ie6_peekaboo" style="clear: both"></div>
</div>
<?php
	include('../includes/footer.php');
?>
