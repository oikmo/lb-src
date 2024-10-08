<?php
	include('includes/header.php');
?>
<div id="Body">
	<script src="/js/Marquee.js" type="text/javascript"></script>
	<div class="FrontPagePanel" id="SignInPane">
		<div id="LoginViewContainer">
			<div class="AspNet-Login">
			<?php if(isset($_SESSION['authenticated'])) :?>
				<div id="LoginView">
					<h5>Hello <?=$_SESSION['auth_user']['username'] ?>!</h5>
					<div class="AspNet-Login">
						<div class="AspNet-Login">
							<form name="input" action="/api.php" method="POST">
								<img src="https://asset.lambda.cam/user?id=<?=$_SESSION['auth_user']['id']?>">
							</form>
							<h5 style="border: 0;background-color: #eee;margin-top:-5px;"><b><a style="display: block;text-align: center;" href="/Account/Logout.php">[ Logout ]</a></b></h5>
						</div>
					</div>
				</div>
			<?php endif	?>
			<?php if(!isset($_SESSION['authenticated'])) :?>
				<div id="LoginView">
					<h5>Member Login</h5>
					<?php 
						if(isset($_SESSION['status'])) {
							unset($_SESSION['status']);
						}
					?>
					<div class="AspNet-Login">
						<div class="AspNet-Login">
							<form name="input" action="/api.php" method="POST">
								<table>
									<tbody>
										<tr>
											<div class="AspNet-Login-UserPanel">
												<label class="Label">Character Name</label>
												<input name="username" type="text" tabindex="1" class="Text">
											</div>
										</tr>
										<tr>
											<div class="AspNet-Login-PasswordPanel">
												<label class="Label">Password</label>
												<input name="password" type="password" tabindex="2" class="Text">
											</div>
										</tr>
										<tr>
											<td></td>
											<div class="AspNet-Login-SubmitPanel">
												<input type="submit" name="login_home_btn" class="Button" tabindex="3" value="Login" style="border: solid 1px #777; color: #777;">
											</div>
										</tr>
									</tbody>
								</table>
								<!--div class="AspNet-Login-RememberMePanel"></div> -->
								
								<div align="center">
									<br>
									<a tabindex="4" class="Button" href="/Account/Register.php">Register</a>
								</div>
								<!-- LATER EPIK. -->
								<!--<div class="AspNet-Login-PasswordRecoveryPanel">
									<a tabindex="5" href="https://web.archive.org/web/20090604133254/http://www.roblox.com/Login/ResetPasswordRequest.aspx">Forgot your password?</a>
								</div>-->
							</form>
						</div>
					</div>
				</div>
			<?php endif	?>
			</div>
		</div>
	</div>
	<div class="FrontPagePanel" id="Movie">
		<object width="424" height="250" data="https://www.bitview.net/embed?v=NDY3ZEWSxjl&amp;hl=en&amp;fs=1&amp;rel=0&amp;color1=0x3a3a3a&amp;color2=0x999999"></object>
	</div>
	<div class="FrontPagePanel" id="FrontPageRectangleAd">
		<div style="overflow: hidden;">
			<img src="/images/ad_mac.png" width="300" height="250">
		</div>
	</div>
	<div class="FrontPagePanel" id="SalesPitch" style="border:0;">
		<a href="/Games.php"><img src="/images/lambdablox_ad.png" style="border-color:black;" border="1"></a>   
	</div>
	<div class="FrontPagePanel" id="RandomFacts">
		<div>
			<h3 style="text-align: center;">LAMBDA Facts</h3>
			<div id="marqueecontainer" onmouseover="copyspeed=pausespeed" onmouseout="copyspeed=marqueespeed">
				<div id="vmarquee" style="position: absolute; top: -90px;">
					<!--YOUR SCROLL CONTENT HERE-->
					<div class="RandomFactoid">
						<img src="/images/Admin.png"> <b>0</b> forum moderators are providing help in the forums
					</div>
					<div class="RandomFactoid">
						<img src="/images/Shirt.png"> <b>0</b> <a href="">bombastic <b>fiery</b> items</a> are available in the shirts section of the catalog
					</div>
					<div class="RandomFactoid"><img src="/images/Pants.png">
						<b>0</b> <a href="">bombastic <b>fiery</b> items</a> are available in the pants section of the catalog
					</div>
								<div class="RandomFactoid">
										<img src="/images/ShoppingBag.png"> the average bid for a user-run <b>rectangle</b> ad is <b>0</b> tickets
								</div>
										<div class="RandomFactoid"><img src="/images/Bux.png"> 0 lambux buys about <b>836</b> tickets on the <a href="">Currency Exchange</a> right now
								</div>
					<div class="RandomFactoid">
						<img src="/images/House.png"> <a href=""><b>MY GOD DAMN HOUSE</b></a> has been BROKEN INTO <b>19907</b> times today
					</div>
					<!--YOUR SCROLL CONTENT HERE-->
				</div>
			</div>
		</div>
	</div>
	<div class="FrontPagePanel" id="WhatsNew">
		<?php 
			$stmt = $con->prepare('SELECT * FROM places WHERE pending = 0');
			//$stmt->bind_param('ss', $start, $rows);
			$stmt->execute();
			
			$result = $stmt->get_result();
			$num_rows = $result->num_rows;
			
			$rand_place = rand(1,$num_rows);
			
			$stmt = $con->prepare('SELECT * FROM places WHERE id = ?');
			$stmt->bind_param('s', $rand_place);
			$stmt->execute();
			
			$result = $stmt->get_result();
			$num_rows = $result->num_rows;
			
			while($row = $result->fetch_assoc()) {
				$creator = $row['creator'];
				$name = $row['name'];
						
				$user_stmt = $con->prepare('SELECT * FROM users WHERE id LIKE ?');
				$user_stmt -> bind_param('s', $creator);
				$user_stmt -> execute();
						
				$user_stmt_result = $user_stmt->get_result();
						
				while($user = $user_stmt_result->fetch_assoc()) {
					$creator_name = $user['username'];
				}
			}
		?>
		<div style="text-align: center;"><h3>Featured Free Game: <span><?= $name; ?></span></h3></div>
			<div style="float: left;">
				<div style="margin: 0px 5px 5px 5px; ">
					<a disabled="disabled" title="<?= $name; ?> - a LAMBDA free game" href="/Place.php?id=<?= $rand_place; ?>" style="display:inline-block;"><img width="420" height="230" src="https://asset.lambda.cam/place?id=<?=$rand_place?>" border="0" alt="<?= $name; ?> - a LAMBDA free game"></a>
				</div>
			</div>
			<div style="float: right;">
				<div style="margin: 0px 5px 5px 2px; text-align: center;">
					<a title="Play this free game!" href="/Place/View.php?id=<?= $rand_place;?>"><img title="Play this free game!" src="/images/PlayThis.png" border="0"></a>
					<div id="Favorited">Favorited: - times</div>
					<div class="Visited">Visited: - times</div>
					<div id="Creator" class="Creator">
						<div class="Avatar">
							<a title="<?= $creator_name ?>" href="" style="display:inline-block;cursor:pointer;"><img style="object-fit:contain;" src="https://asset.lambda.cam/user?id=<?=$creator?>" width="100" height="100" border="0" alt="<?= $creator_name ?>"></a>
						</div>
						Creator: <a href="/User.php?id=<?=$creator?>"><?= $creator_name ?></a>
					</div>
				</div>
			</div>
	</div>
	<div class="FrontPagePanel" id="ParentsCorner">
		<div id="Inside">
			<img class="ShieldImage" src="/images/SuperSafe32.png" border="0">
			<div style="float:left; font-size: x-large; height: 42px; width: 220px; text-align: center;">Lambda Corner</div>
			<div style="clear: left;"></div>
			<p>HAVE YOU HEARD OF HALF LIFE? NO?!?? THEN GO ON STEAM RIGHT NOW AND GET IT.</p>
		</div>
	</div>
</div>
<?php
	include('includes/footer.php');
?>