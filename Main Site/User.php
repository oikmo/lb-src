<?php 
	include('includes/dbcon.php');
	
	if(!isset($_GET['id'])) {
		header('Location: /index.php');
	} else {
		if (!is_numeric($_GET['id'])) {
			header('Location: /index.php');
		} else {
			$id = $_GET['id'];
		}
	}
	
	//sanitize
	$stmt = $con->prepare('SELECT * FROM users WHERE id LIKE ?');
	$stmt -> bind_param('s', $id);
	$stmt -> execute();
	
	$stmt_result = $stmt->get_result();
	$result = $stmt_result->num_rows;
	
	$name = "";
	$last_login = "";
	$blurb = "";
	$status = "1";
	$badges = "";
	
	if($result == 0) {
		header('Location: /index.php');
	}
	
	while($row_data = $stmt_result->fetch_assoc()) {
		$name = $row_data['username'];
		$last_login = $row_data['last_online'];
		$blurb = $row_data['blurb'];
		$status = $row_data['status'];
		$badges = $row_data['badges'];
	}
	
	$last_login = str_replace("-", "/", $last_login);
	
	$blurb = str_replace("<","&lt", $blurb); 
	$blurb = str_replace(">","&gt", $blurb); 
	$blurb = str_replace("\n","<br/>",$blurb);
	
	$name = str_replace("<","&lt", $name); 
	$name = str_replace(">","&gt", $name);
	
	
	$title = "$name";
	$meta_image = "http://asset.lambda.cam/user?id=$id";
	$meta_description = $blurb;
	
	include('includes/header.php');
	
	if($badges != "0,0,0,0,0,0,0") {
		list($admin, $friendship, $homestead, $bricksmith, $veteran, $inviter, $bc) = explode(",", $badges);
	} else {
		$badges = "none";
	}
	$user_is_friend = false;
	$user_id = -1;
	if(isset($_SESSION['auth_user'])) {
		$user_id = $_SESSION['auth_user']['id'];
		$valid = "1";
		
		//sanitize
		$stmt = $con->prepare('SELECT * FROM friends WHERE (user1 = ? AND user2 = ?) OR (user2 = ? AND user1 = ?)');
		$stmt -> bind_param('ssss', $user_id, $id, $user_id, $id);
		$stmt -> execute();
		
		$stmt_result = $stmt->get_result();
		$result = $stmt_result->num_rows;
		
		if($result == 0) {
			// add friend
			$user_is_friend = -1;
		} else {
			// accept request if auth_user_id is user2
			$user_is_friend = 0;
			
			while($row_data = $stmt_result->fetch_assoc()) {
				$user1 = $row_data['user1'];
				$user2 = $row_data['user2'];
				$friend_status = $row_data['status'];
			}
			
			if($user1 == $user_id && $friend_status == 0) {
				$user_is_friend = 1;
			} 
			if($user2 == $user_id && $friend_status == 1) {
				$user_is_friend = 2;
			}
			
			if(($user2 == $user_id || $user1 == $user_id) && $friend_status == 1) {
				$user_is_friend = 2;
			}
		}
		
		
		$friend_link = "";
		if($user_is_friend == 2) {
			$friend_text = "Remove friend :[";
			$friend_link = "unfriend";
		} else if($user_is_friend == 1) {
			$friend_text = "Remove request!";
			$friend_link = "unfriend";
		} else if($user_is_friend == 0) {
			$friend_text = "Accept friend :D";
			$friend_link = "friend";
		} else {
			$friend_text = "Add as friend";
			$friend_link = "friend";
		}
	}
	
	$stmt_total = $con->prepare('SELECT * FROM friends WHERE (user1 = ? OR user2 = ?) AND status = 1');
	$stmt_total -> bind_param('ss', $id, $id);
	$stmt_total -> execute();
	
	$stmt_result_total = $stmt_total->get_result();
	$result_total = $stmt_result_total->num_rows;
?>
<script>
	function gameClick(id) {
		if(document.getElementById('place_content_'+id).style.display == "block") {
			document.getElementById('place_content_'+id).style.display = "none";
		} else if(document.getElementById('place_content_'+id).style.display == "none") {
			document.getElementById('place_content_'+id).style.display = "block";
			Array.from(document.getElementById('ShowcasePlacesAccordion').children).forEach(child => {
				console.log(child.id);
			  if(child.id.includes('place_content_')) {
				  console.log(child.id);
					if((child.id === ('place_content_'+id)) === false) {
						child.style.display = "none";
					}
			  }
			});
		}
		
	}
</script>
<div id="Body">
	<div id="UserContainer">
		<div id="LeftBank">
			<div id="ProfilePane">
				<table width="100%" bgcolor="lightsteelblue" cellpadding="6" cellspacing="0">
					<tbody>
						<?php if(isset($_SESSION['auth_user']['id']) && $_SESSION['auth_user']['id'] == $id) :?>
						<a href="/Account/Edit.php" style="padding-top: 10px; padding-right: 10px; float: right;position: relative; margin-bottom: -23px;margin-left: -10px;">[ Edit ]</a>
						<?php endif ?>
						<tr>
							<td>
								<span class="Title"><?= $name; ?></span><br>
								<?php if($status == "0") :?>
									<span class="UserOfflineMessage">[ Offline ]</span>
								<?php endif ?>
								<?php if($status == "1") :?>
									<span class="UserOfflineMessage">[ Online ]</span>
								<?php endif ?>
							</td>
						</tr>
						<tr>
							<td>
								<span id="ctl00_cphRoblox_rbxUserPane_lUserRobloxURL"><?= $name; ?>'s LAMBDA:</span><br>
								<a id="ctl00_cphRoblox_rbxUserPane_hlUserRobloxURL" href="https://www.lambda.cam/User.php?id=<?= $id; ?>">http://www.lambda.cam/User.php?id=<?= $id; ?></a><br>
								<br>
								Last login: <?= $last_login; ?>
								<br>
								<?php if($user_id != $id && $user_id != -1) :?>
								<form method="post" action="/api.php">
									<input type="hidden" name="<?= $friend_link; ?>" value="<?= $id; ?>" /> 
									<h5 style="background-color: #ffffff00;font-family: Verdana, Sans-Serif;">
										<b>
											<a onclick="this.parentNode.parentNode.parentNode.submit();"><?= $friend_text; ?></a>
										</b>
									</h5>
								</form>
								<?php endif ?>
								<?php if($user_id != $id && $user_id == -1) :?>
									<h5 style="background-color: #ffffff00;font-family: Verdana, Sans-Serif;">
										<b>
											<a>Register to add as friend!</a>
										</b>
									</h5>
								<?php endif ?>

								<table width="100%">
									<tbody>
										<tr>
											<td>
												<a id="ctl00_cphRoblox_rbxUserPane_AvatarImage" disabled="disabled" title="<?= $name; ?>" onclick="return false" style="display:inline-block;"><img src="https://asset.lambda.cam/user?id=<?=$id?>" border="0" onerror="return Roblox.Controls.Image.OnError(this)" alt="<?= $name; ?>" blankurl="http://t0-cf.roblox.com/blank-150x200.gif"></a><br>
											</td>
											<td>
												<input type="hidden" name="ctl00$cphRoblox$rbxUserPane$rbxPublicUser$rbxPlaceLauncher$HiddenField1" id="ctl00_cphRoblox_rbxUserPane_rbxPublicUser_rbxPlaceLauncher_HiddenField1">
												<p></p>
												<p></p>
												<p style="width: 259px;word-wrap: break-word;"><?= $blurb ?></p>
												<p></p>
												
											</td>
											<td>
												<div id="ctl00_cphRoblox_rbxUserPane_rbxPublicUser_rbxPlaceLauncher_Panel1" class="modalPopup" style="display: none">
													<div style="margin: 1.5em">
														<div id="Spinner" style="float:left;margin:0 1em 1em 0">
															<img id="ctl00_cphRoblox_rbxUserPane_rbxPublicUser_rbxPlaceLauncher_Image1" src="./index_files/ProgressIndicator2.gif" alt="Progress" border="0">
														</div>
														<div id="Starting" style="display: inline">
															Starting Roblox
														</div>
														<div id="Waiting" style="display: none">
															Waiting for a server
														</div>
														<div id="Loading" style="display: none">
															A server is loading the game
														</div>
														<div id="Joining" style="display: none">
															The server is ready. Joining the game...
														</div>
														<div id="Error" style="display: none">
															An error occured. Please try again later
														</div>
														<div id="Expired" style="display: none">
															There are no game servers available at this time. Please try again later
														</div>
														<div id="GameEnded" style="display: none">
															The game you requested has ended
														</div>
														<div id="GameFull" style="display: none">
															The game you requested is full. Please try again later
														</div>
														<div id="Updating" style="display: none">
															Roblox is updating. Please wait
														</div>
														<div id="Updated" style="display: none">
															Requesting a server
														</div>
														<div style="text-align: center; margin-top: 1em">
															<input id="Cancel" type="button" class="Button" value="Cancel">
														</div>
													</div>
												</div>
											</td>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
			<div id="UserBadgesPane">
				<div id="UserBadges">
					<h4><a href="">Badges</a></h4>
					<?php if($badges == "none") :?>
					<div>
						<p class="NoResults"><span id="ctl00_cphRoblox_rbxUserBadgesPane_lNoResults"><?= $name; ?> does not have any LAMBDA badges.</span></p>
					</div>
					<?php endif ?>
					
					<?php if($badges != "none") :?>
					<div style="overflow: hidden;">
						<?php if($admin == "1") :?>
						<div class="Badge">
							<div class="BadgeImage"><a href=""><img src="/images/Badges/Administrator.png" alt="This badge identifies an account as belonging to a Roblox administrator. Only official Roblox administrators will possess this badge. If someone claims to be an admin, but does not have this badge, they are potentially trying to mislead you. If this happens, please report abuse and we will delete the imposter's account." height="75" border="0"></a></div>
							<div class="BadgeLabel"><a href="">Administrator</a></div>
						</div>
						<?php endif ?>
						
						<?php if($friendship == "1") :?>
						<div class="Badge">
							<div class="BadgeImage"><a href=""><img src="/images/Badges/Friendship.jpg" alt="This badge is given to players who have embraced the Roblox community and have made at least 20 friends. People who have this badge are good people to know and can probably help you out if you are having trouble." height="75" border="0"></a></div>
							<div class="BadgeLabel"><a href="">Friendship</a></div>
						</div>
						<?php endif ?>
						
						<?php if($homestead == "1") :?>
						<div class="Badge">
							<div class="BadgeImage"><a href=""><img src="/images/Badges/Homestead.jpg" alt="The homestead badge is earned by having your personal place visited 100 times. Players who achieve this have demonstrated their ability to build cool things that other Robloxians were interested enough in to check out. Get a jump-start on earning this reward by inviting people to come visit your place." height="75" border="0"></a></div>
							<div class="BadgeLabel"><a href="">Homestead</a></div>
						</div>
						<?php endif ?>
						
						<?php if($bricksmith == "1") :?>
						<div class="Badge">
							<div class="BadgeImage"><a href=""><img src="/images/Badges/Bricksmith.jpg" alt="The Bricksmith badge is earned by having a popular personal place. Once your place has been visited 1000 times, you will receive this award. Robloxians with Bricksmith badges are accomplished builders who were able to create a place that people wanted to explore a thousand times. They no doubt know a thing or two about putting bricks together." height="75" border="0"></a></div>
							<div class="BadgeLabel"><a href="">Bricksmith</a></div>
						</div>
						<?php endif ?>
						
						<?php if($veteran == "1") :?>
						<div class="Badge">
							<div class="BadgeImage"><a href=""><img src="/images/Badges/Veteran.png" alt="This decoration is awarded to all citizens who have played ROBLOX for at least a year. It recognizes stalwart community members who have stuck with us over countless releases and have helped shape ROBLOX into the game that it is today. These medalists are the true steel, the core of the Robloxian history ... and its future." height="75" border="0"></a></div>
							<div class="BadgeLabel"><a href="">Veteran</a></div>
						</div>
						<?php endif ?>
						
						<?php if($inviter == "1") :?>
						<div class="Badge">
							<div class="BadgeImage"><a href=""><img src="/images/Badges/Inviter.png" alt="Robloxia is a vast uncharted realm, as large as the imagination. Individuals who invite others to join in the effort of mapping this mysterious region are honored in Robloxian society. Citizens who successfully recruit three or more fellow explorers via the Share Roblox with a Friend mechanism are awarded with this badge." height="75" border="0"></a></div>
							<div class="BadgeLabel"><a href="">Inviter</a></div>
						</div>
						<?php endif ?>
						
						<?php if($bc == "1") :?>
						<div class="Badge">
							<div class="BadgeImage"><a href=""><img src="/images/Badges/BuildersClub.png" alt="Members of the illustrious Builders Club display this badge proudly. The Builders Club is a paid premium service. Members receive several benefits: they get ten places on their account instead of one, they earn a daily income of 15 ROBUX, they can sell their creations to others in the ROBLOX Catalog, they get the ability to browse the web site without external ads, and they receive the exclusive Builders Club construction hat." height="75" border="0"></a></div>
							<div class="BadgeLabel"><a href="">Builders Club</a></div>
						</div>
						<?php endif ?>
					</div>
					<?php endif ?>
					
				</div>
			</div>
			<div id="UserStatisticsPane">
				<div id="UserStatistics">
					<h4>Statistics</h4>
					<div class="Statistic">
						<div class="Label"><acronym title="The number of this user's friends.">Friends</acronym>:</div>
						<div class="Value"><span id="ctl00_cphRoblox_rbxUserStatisticsPane_lFriendsStatistics">1 (1 last week)</span></div>
					</div>
					<div class="Statistic">
						<div class="Label"><acronym title="The number of posts this user has made to the ROBLOX forum.">Forum Posts</acronym>:</div>
						<div class="Value"><span id="ctl00_cphRoblox_rbxUserStatisticsPane_lForumPostsStatistics">0</span></div>
					</div>
					<div class="Statistic">
						<div class="Label"><acronym title="The number of times this user&#39;s profile has been viewed.">Profile Views</acronym>:</div>
						<div class="Value"><span id="ctl00_cphRoblox_rbxUserStatisticsPane_lProfileViewsStatistics">1 (1 last week)</span></div>
					</div>
						<div class="Statistic">
						<div class="Label"><acronym title="The number of times this user&#39;s place has been visited.">Place Visits</acronym>:</div>
					<div class="Value"><span id="ctl00_cphRoblox_rbxUserStatisticsPane_lPlaceVisitsStatistics"> ( last week)</span></div>
					</div>
					<div class="Statistic">
						<div class="Label"><acronym title="The number of times this user&#39;s character has destroyed another user&#39;s character in-game.">Knockouts</acronym>:</div>
						<div class="Value"><span> ( last week)</span></div>
					</div>
				</div>
			</div>
		</div>
	<div id="RightBank">
		<div id="UserPlacesPane" style="background-color: #f5f5f5;">
			<div id="UserPlaces">
				<h4>Showcase</h4>
				<?php 
					$stmt = $con->prepare('SELECT * FROM `places` WHERE creator LIKE ?');
					$stmt->bind_param('s', $id);
					$stmt->execute();
					
					$result = $stmt->get_result();
					$num_rows = $result->num_rows;
					
					if($num_rows != 0) {
						echo "<div id='ShowcasePlacesAccordion'>";
						while($row = $result->fetch_assoc()) {
							$place_name = $row['name'];
							$place_id = $row['id'];
							$description = $row['description'];
							echo "<div id='place_title_$place_id' class='AccordionHeader' onclick='gameClick($place_id);'>$place_name</div>\n";
							echo "<div id='place_content_$place_id'style='display:none;'>\n";
								echo "<div class='Place'>\n";
									echo "<br>\n";
									echo "<div>\n";
										echo "<div style='display: inline; width: 10px;'>\n";
											echo "<a disabled='disabled' title='$place_name' href='/Place.php?id=$place_id' style='display:inline-block;'>\n";
												echo "<img src='/images/Play.png' border='0' alt='$place_name'>\n";
											echo "</a>\n";
										echo "</div>\n";
									echo "</div>\n";
									echo "<div class='Statistics'>\n";
										echo "<span>Visited 0 times (0 last week)</span>\n";
									echo "</div>\n";
									echo "<div class='Thumbnail'>\n";
										echo "<a disabled='disabled' title='$place_name' href='/Place/View.php?id=$place_id' style='display:inline-block;'>\n";
											echo "<img src='https://asset.lambda.cam/place?id=$place_id&type=img' width='420' height='230' border='0' alt='$place_name'>\n";
										echo "</a>\n";
									echo "</div>\n";
									echo "<div>\n";
										echo "<div class='Description'>\n";
											echo "<span>$description</span>\n";
										echo "</div>\n";
									echo "</div>\n";
								echo "</div>\n";
							echo "</div>\n";
						}
					} else {
						echo "<div id='ShowcasePlacesAccordion' style='padding:10px;'>";
						echo "<span>$name does not have any games!</span>\n";
					}
					
					
					?>
					
				</div>
			</div>
		</div>
		<div id="FriendsPane" style="background-color: #f5f5f5;">
			<div id="Friends">
				<h4><?= $name ?>'s Friends <a href="/Friends.php?id=<?= $id ?>">See all...</a></h4>
				<div style="overflow: auto;" cellspacing="0" border="0" align="Center">
					<?php
						
						$valid = "1";
						$stmt = $con->prepare('SELECT * FROM friends WHERE (user1 = ? OR user2 = ?) AND status = ? LIMIT 0, 6');
						$stmt -> bind_param('sss', $id, $id, $valid);
						$stmt -> execute();
						
						$stmt_result = $stmt->get_result();
						$result = $stmt_result->num_rows;
						
						
						
						if($result == 0) {
							echo "<h5>$name does not have any friends.</h5>";
						} else {
							while($row = $stmt_result->fetch_assoc()) {
								
								$user1 = $row['user1'];
								$user2 = $row['user2'];
								
								if($user1 == $id) {
									$friend_id = $user2;
								} else {
									$friend_id = $user1;
								}
								
								$user_stmt = $con->prepare('SELECT * FROM users WHERE id LIKE ?');
								$user_stmt -> bind_param('s', $friend_id);
								$user_stmt -> execute();
								
								$user_stmt_result = $user_stmt->get_result();
								$user_result = $user_stmt_result->num_rows;
								
								$friend_name = "";
								
								$status = "Offline";
								while($user = $user_stmt_result->fetch_assoc()) {
									$friend_name = $user['username'];
									
									if($user['status'] == 1) {
										$status = "Online";
									}
								}
								
								if($user_result == 0) {
									$user_stmt = $con->prepare('DELETE FROM friends WHERE user1 LIKE ? or user2 LIKE ?');
									$user_stmt -> bind_param('ss', $friend_id, $friend_id);
									$user_stmt -> execute();
									continue;
								}
								echo "<div class='Friend'>";
									echo "<div class='Avatar' style='width: 100px;'>";
										echo "<a title='$friend_name' href='/User.php?id=$friend_id' style='display:inline-block;cursor:pointer;'>";
											echo "<img src='https://asset.lambda.cam/user?id=$friend_id' alt='$friend_name' style='width: 100px;' border='0'>";
										echo "</a>";
									echo "</div>";
									echo "<div class='Summary'>";
										echo "<span class='OnlineStatus'><img src='/images/OnlineStatusIndicator_Is$status.gif' alt='$friend_name is $status (last seen at 6/27/2009 7:57:01 AM).' border='0'></span>";
										echo "<span class='Name'><a href='/User.php?id=$friend_id'>$friend_name</a></span>";
									echo "</div>";
								echo "</div>";
							}
						}
					?>
					
				</div>
				<!--<a href='/Friends.php?id=$id' style='margin-bottom: 10px;display: block;'>See more friends...</a>-->
			</div>
		</div>
		<div id="FavoritesPane">
			<div id="ctl00_cphRoblox_rbxFavoritesPane_FavoritesPane">
				<div id="Favorites">
					<h4>Favorites</h4>
					<div id="FavoritesContent">
						<div id="ctl00_cphRoblox_rbxFavoritesPane_NoResultsPanel" class="NoResults" style="background-color: white;">
							<span id="ctl00_cphRoblox_rbxFavoritesPane_NoResultsLabel" class="NoResults"><?= $name; ?> has not chosen any favorite places.</span>
						</div>
					</div>
					<div class="PanelFooter">
						Category:&nbsp;
						<select name="ctl00$cphRoblox$rbxFavoritesPane$AssetCategoryDropDownList">
							<option value="17">Heads</option>
							<option value="18">Faces</option>
							<option value="19">Gear</option>
							<option value="8">Hats</option>
							<option value="2">T-Shirts</option>
							<option value="11">Shirts</option>
							<option value="12">Pants</option>
							<option value="13">Decals</option>
							<option value="10">Models</option>
							<option selected="selected" value="9">Places</option>
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="UserAssetsPane" style="border:0;"></div>
    <!-- LATER EPIK. -->
	<!--<div id="ctl00_cphRoblox_rbxUserAssetsPane_upUserAssetsPane">
	<div id="UserAssets">
	<h4>Stuff</h4>
	<div id="AssetsMenu">
	<div id="ctl00_cphRoblox_rbxUserAssetsPane_AssetCategoryRepeater_ctl00_AssetCategorySelectorPanel" class="AssetsMenuItem">
	<a id="ctl00_cphRoblox_rbxUserAssetsPane_AssetCategoryRepeater_ctl00_AssetCategorySelector" class="AssetsMenuButton" href="javascript:__doPostBack(&#39;ctl00$cphRoblox$rbxUserAssetsPane$AssetCategoryRepeater$ctl00$AssetCategorySelector&#39;,&#39;&#39;)">Heads</a>
	</div>
	<div id="ctl00_cphRoblox_rbxUserAssetsPane_AssetCategoryRepeater_ctl01_AssetCategorySelectorPanel" class="AssetsMenuItem">
	<a id="ctl00_cphRoblox_rbxUserAssetsPane_AssetCategoryRepeater_ctl01_AssetCategorySelector" class="AssetsMenuButton" href="javascript:__doPostBack(&#39;ctl00$cphRoblox$rbxUserAssetsPane$AssetCategoryRepeater$ctl01$AssetCategorySelector&#39;,&#39;&#39;)">Faces</a>
	</div>
	<div id="ctl00_cphRoblox_rbxUserAssetsPane_AssetCategoryRepeater_ctl02_AssetCategorySelectorPanel" class="AssetsMenuItem">
	<a id="ctl00_cphRoblox_rbxUserAssetsPane_AssetCategoryRepeater_ctl02_AssetCategorySelector" class="AssetsMenuButton" href="javascript:__doPostBack(&#39;ctl00$cphRoblox$rbxUserAssetsPane$AssetCategoryRepeater$ctl02$AssetCategorySelector&#39;,&#39;&#39;)">Gear</a>
	</div>
	<div id="ctl00_cphRoblox_rbxUserAssetsPane_AssetCategoryRepeater_ctl03_AssetCategorySelectorPanel" class="AssetsMenuItem_Selected">
	<a id="ctl00_cphRoblox_rbxUserAssetsPane_AssetCategoryRepeater_ctl03_AssetCategorySelector" class="AssetsMenuButton_Selected" href="javascript:__doPostBack(&#39;ctl00$cphRoblox$rbxUserAssetsPane$AssetCategoryRepeater$ctl03$AssetCategorySelector&#39;,&#39;&#39;)">Hats</a>
	</div>
	<div id="ctl00_cphRoblox_rbxUserAssetsPane_AssetCategoryRepeater_ctl04_AssetCategorySelectorPanel" class="AssetsMenuItem">
	<a id="ctl00_cphRoblox_rbxUserAssetsPane_AssetCategoryRepeater_ctl04_AssetCategorySelector" class="AssetsMenuButton" href="javascript:__doPostBack(&#39;ctl00$cphRoblox$rbxUserAssetsPane$AssetCategoryRepeater$ctl04$AssetCategorySelector&#39;,&#39;&#39;)">T-Shirts</a>
	</div>
	<div id="ctl00_cphRoblox_rbxUserAssetsPane_AssetCategoryRepeater_ctl05_AssetCategorySelectorPanel" class="AssetsMenuItem">
	<a id="ctl00_cphRoblox_rbxUserAssetsPane_AssetCategoryRepeater_ctl05_AssetCategorySelector" class="AssetsMenuButton" href="javascript:__doPostBack(&#39;ctl00$cphRoblox$rbxUserAssetsPane$AssetCategoryRepeater$ctl05$AssetCategorySelector&#39;,&#39;&#39;)">Shirts</a>
	</div>
	<div id="ctl00_cphRoblox_rbxUserAssetsPane_AssetCategoryRepeater_ctl06_AssetCategorySelectorPanel" class="AssetsMenuItem">
	<a id="ctl00_cphRoblox_rbxUserAssetsPane_AssetCategoryRepeater_ctl06_AssetCategorySelector" class="AssetsMenuButton" href="javascript:__doPostBack(&#39;ctl00$cphRoblox$rbxUserAssetsPane$AssetCategoryRepeater$ctl06$AssetCategorySelector&#39;,&#39;&#39;)">Pants</a>
	</div>
	<div id="ctl00_cphRoblox_rbxUserAssetsPane_AssetCategoryRepeater_ctl07_AssetCategorySelectorPanel" class="AssetsMenuItem">
	<a id="ctl00_cphRoblox_rbxUserAssetsPane_AssetCategoryRepeater_ctl07_AssetCategorySelector" class="AssetsMenuButton" href="javascript:__doPostBack(&#39;ctl00$cphRoblox$rbxUserAssetsPane$AssetCategoryRepeater$ctl07$AssetCategorySelector&#39;,&#39;&#39;)">Decals</a>
	</div>
	<div id="ctl00_cphRoblox_rbxUserAssetsPane_AssetCategoryRepeater_ctl08_AssetCategorySelectorPanel" class="AssetsMenuItem">
	<a id="ctl00_cphRoblox_rbxUserAssetsPane_AssetCategoryRepeater_ctl08_AssetCategorySelector" class="AssetsMenuButton" href="javascript:__doPostBack(&#39;ctl00$cphRoblox$rbxUserAssetsPane$AssetCategoryRepeater$ctl08$AssetCategorySelector&#39;,&#39;&#39;)">Models</a>
	</div>
	<div id="ctl00_cphRoblox_rbxUserAssetsPane_AssetCategoryRepeater_ctl09_AssetCategorySelectorPanel" class="AssetsMenuItem">
	<a id="ctl00_cphRoblox_rbxUserAssetsPane_AssetCategoryRepeater_ctl09_AssetCategorySelector" class="AssetsMenuButton" href="javascript:__doPostBack(&#39;ctl00$cphRoblox$rbxUserAssetsPane$AssetCategoryRepeater$ctl09$AssetCategorySelector&#39;,&#39;&#39;)">Places</a>
	</div>
	</div>
	<div id="AssetsContent">
	<div>
	<div id="AssetRecommender" style="border: solid 1px grey; background-color: White;">
	<h3>Recommendations</h3>
	<div style="font-size: x-small;">Here are some other Hats that we think you might like.</div>
	<table id="ctl00_cphRoblox_rbxUserAssetsPane_AssetRec_dlAssets" cellspacing="0" align="Center" border="0" height="200" width="100">
	<tbody>
	<tr>
	<td>
	<div id="ctl00_cphRoblox_rbxUserAssetsPane_AssetRec_dlAssets_ctl00_PortraitDiv" style="width: 140px; height: 170px; overflow:hidden;">
	<div class="AssetThumbnail" style="padding-left: 15px;">
	<a id="ctl00_cphRoblox_rbxUserAssetsPane_AssetRec_dlAssets_ctl00_AssetThumbnailHyperLink" title="Straw Hat" href="https://web.archive.org/web/20090628115400/http://www.roblox.com/Item.aspx?ID=1033722" style="display:inline-block;cursor:pointer;"><img src="./index_files/4fc3cb0157f6bdb17f51a42ca83ed946" border="0" onerror="return Roblox.Controls.Image.OnError(this)" alt="Straw Hat" blankurl="http://t6-cf.roblox.com/blank-110x110.gif"></a>
	</div>
	<div class="AssetDetails">
	<div class="AssetName"><a id="ctl00_cphRoblox_rbxUserAssetsPane_AssetRec_dlAssets_ctl00_AssetNameHyperLinkPortrait" href="https://web.archive.org/web/20090628115400/http://www.roblox.com/Item.aspx?ID=1033722">Straw Hat</a></div>
	<div class="AssetCreator"><span class="Label">Creator:</span> <span class="Detail"><a id="ctl00_cphRoblox_rbxUserAssetsPane_AssetRec_dlAssets_ctl00_CreatorHyperLinkPortrait" href="https://web.archive.org/web/20090628115400/http://www.roblox.com/User.aspx?ID=1">ROBLOX</a></span></div>
	</div>
	</div>
	</td>
	<td>
	<div id="ctl00_cphRoblox_rbxUserAssetsPane_AssetRec_dlAssets_ctl01_PortraitDiv" style="width: 140px; height: 170px; overflow:hidden;">
	<div class="AssetThumbnail" style="padding-left: 15px;">
	<a id="ctl00_cphRoblox_rbxUserAssetsPane_AssetRec_dlAssets_ctl01_AssetThumbnailHyperLink" title="Black Tricorne" href="https://web.archive.org/web/20090628115400/http://www.roblox.com/Item.aspx?ID=5808105" style="display:inline-block;cursor:pointer;"><img src="./index_files/3e8c0a3533901551e38a785cb63767bd" border="0" onerror="return Roblox.Controls.Image.OnError(this)" alt="Black Tricorne" blankurl="http://t6-cf.roblox.com/blank-110x110.gif"></a>
	</div>
	<div class="AssetDetails">
	<div class="AssetName"><a id="ctl00_cphRoblox_rbxUserAssetsPane_AssetRec_dlAssets_ctl01_AssetNameHyperLinkPortrait" href="https://web.archive.org/web/20090628115400/http://www.roblox.com/Item.aspx?ID=5808105">Black Tricorne</a></div>
	<div class="AssetCreator"><span class="Label">Creator:</span> <span class="Detail"><a id="ctl00_cphRoblox_rbxUserAssetsPane_AssetRec_dlAssets_ctl01_CreatorHyperLinkPortrait" href="https://web.archive.org/web/20090628115400/http://www.roblox.com/User.aspx?ID=1">ROBLOX</a></span></div>
	</div>
	</div>
	</td>
	<td>
	<div id="ctl00_cphRoblox_rbxUserAssetsPane_AssetRec_dlAssets_ctl02_PortraitDiv" style="width: 140px; height: 170px; overflow:hidden;">
	<div class="AssetThumbnail" style="padding-left: 15px;">
	<a id="ctl00_cphRoblox_rbxUserAssetsPane_AssetRec_dlAssets_ctl02_AssetThumbnailHyperLink" title="Propeller Beanie" href="https://web.archive.org/web/20090628115400/http://www.roblox.com/Item.aspx?ID=10911990" style="display:inline-block;cursor:pointer;"><img src="./index_files/81a3cc2fbe9c355c3b09cfb24d3f4fb4" border="0" onerror="return Roblox.Controls.Image.OnError(this)" alt="Propeller Beanie" blankurl="http://t6-cf.roblox.com/blank-110x110.gif"></a>
	</div>
	<div class="AssetDetails">
	<div class="AssetName"><a id="ctl00_cphRoblox_rbxUserAssetsPane_AssetRec_dlAssets_ctl02_AssetNameHyperLinkPortrait" href="https://web.archive.org/web/20090628115400/http://www.roblox.com/Item.aspx?ID=10911990">Propeller Beanie</a></div>
	<div class="AssetCreator"><span class="Label">Creator:</span> <span class="Detail"><a id="ctl00_cphRoblox_rbxUserAssetsPane_AssetRec_dlAssets_ctl02_CreatorHyperLinkPortrait" href="https://web.archive.org/web/20090628115400/http://www.roblox.com/User.aspx?ID=1">ROBLOX</a></span></div>
	</div>
	</div>
	</td>
	<td>
	<div id="ctl00_cphRoblox_rbxUserAssetsPane_AssetRec_dlAssets_ctl03_PortraitDiv" style="width: 140px; height: 170px; overflow:hidden;">
	<div class="AssetThumbnail" style="padding-left: 15px;">
	<a id="ctl00_cphRoblox_rbxUserAssetsPane_AssetRec_dlAssets_ctl03_AssetThumbnailHyperLink" title="Cake Hat" href="https://web.archive.org/web/20090628115400/http://www.roblox.com/Item.aspx?ID=1376467" style="display:inline-block;cursor:pointer;"><img src="./index_files/27fd80abd6a46ae4babc5eb2b39d181a" border="0" onerror="return Roblox.Controls.Image.OnError(this)" alt="Cake Hat" blankurl="http://t6-cf.roblox.com/blank-110x110.gif"></a>
	</div>
	<div class="AssetDetails">
	<div class="AssetName"><a id="ctl00_cphRoblox_rbxUserAssetsPane_AssetRec_dlAssets_ctl03_AssetNameHyperLinkPortrait" href="https://web.archive.org/web/20090628115400/http://www.roblox.com/Item.aspx?ID=1376467">Cake Hat</a></div>
	<div class="AssetCreator"><span class="Label">Creator:</span> <span class="Detail"><a id="ctl00_cphRoblox_rbxUserAssetsPane_AssetRec_dlAssets_ctl03_CreatorHyperLinkPortrait" href="https://web.archive.org/web/20090628115400/http://www.roblox.com/User.aspx?ID=1">ROBLOX</a></span></div>
	</div>
	</div>
	</td>
	</tr>
	</tbody>
	</table>
	</div>
	</div>
	</div>
	<div style="clear:both;"></div>
	</div>
	</div>
	</div>-->
	</div>
</div>
<?php 
	include('includes/footer.php');
?>
