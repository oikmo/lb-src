<?php
	$_loc = "Login";
	session_start();
	if(isset($_SESSION['authenticated'])) {
		header('Location: /index.php');
	}
	include('../includes/header.php');
?>
<div id="Body">
	<div id="FrameLogin" style="margin: 10px auto 150px auto; width: 500px; border: black thin solid; padding: 22px; background-color: white;">
		<div id="PaneNewUser">
			<h3>New User?</h3>
			<p>You need an account to play LAMBDA.</p>
			<p>If you aren't a LAMBDA member then <a href="/Account/Register.php">register</a>. It's easy and we do <em>not</em> share your personal information with anybody.</p>
		</div>
		<div id="PaneLogin" style="width:18em;">
			<h3>Log In</h3>
			<?php
				if(isset($_SESSION['status'])) {
					echo "<h4 style='color:red;'>".$_SESSION['status']."</h4>";
					unset($_SESSION['status']);
				}
			?>
			<form class="AspNet-Login" name="input" action="/api.php" method="POST" style="text-align: left;">
				<div class="AspNet-Login-UserPanel">
					<label class="Label"><em>U</em>ser Name:</label>
					<input type="text" name="username" class="Text" tabindex="1" accesskey="u">&nbsp;
				</div>
				<div class="AspNet-Login-PasswordPanel">
					<label class="Label"><em>P</em>assword:</label>
					<input type="password" name="password" class="Text" value="" tabindex="2" accesskey="p">&nbsp;
				</div>
				<div class="AspNet-Login-SubmitPanel">
					<input type="submit" name="login_btn" class="Button" style="border: solid 1px #777; color: #777;" value="Login">
				</div>
				<!-- LATER EPIK. -->
				<!--<div class="AspNet-Login-PasswordRecoveryPanel">
					<a href="https://web.archive.org/web/20070813034505/http://www.roblox.com/Login/ResetPasswordRequest.aspx" title="Password recovery">Forgot your password?</a>
				</div>-->
			</div>
		</div>
	</div>
</div>
<?php
	include('../includes/footer.php');
?>