<?php 
	
	include('../includes/dbcon.php');
	
	
	if(!isset($_GET['id'])) {
		header('Location: /Games.php');
	} else {
		if (!is_numeric($_GET['id'])) {
			header('Location: /Games.php');
		} else {
			$id = $_GET['id'];
		}
	}
	
	//sanitize
	$stmt = $con->prepare('SELECT * FROM places WHERE id LIKE ?');
	$stmt -> bind_param('s', $id);
	$stmt -> execute();
	
	$stmt_result = $stmt->get_result();
	$result = $stmt_result->num_rows;
	
	while($row = $stmt_result->fetch_assoc()) {
		$id = $row['id'];
		$creator = $row['creator'];
		$name = $row['name'];
		$pending = $row['pending'];
		$description = $row['description'];
		$version = $row['version'];
		
		$user_stmt = $con->prepare('SELECT * FROM users WHERE id LIKE ?');
		$user_stmt -> bind_param('s', $creator);
		$user_stmt -> execute();
		
		$user_stmt_result = $user_stmt->get_result();
		
		$creator_name = "";
							
		while($user = $user_stmt_result->fetch_assoc()) {
			$creator_name = $user['username'];
		}
	}
	$title = $name;
	if($result == 0) {
		header('Location: /Games.php');
	}
	
	$meta_image = "https://asset.lambda.cam/place?id=$id&type=img";
	$meta_description = "'" . $description . "'". " made by " . $creator_name . "!";
	
	$file_name = str_replace(" ","_", $name);
	
	include('../includes/header.php');
	
	if($pending == 1) {
		if($auth_user_badges == "none") {
			header('Location: /Games.php');
		} else {
			if($admin == 0) {
				header('Location: /Games.php');
			}
		}
	}
	
?>
<div id="Body">
	<script>
		function setPort(port) {
			document.getElementById('port').value = port;
		}
	</script>
	<div id="ItemContainer">
		<div id="Item">
			<h2><?= $name; ?></h2>
            
			<div id="Details">
				<div id="Thumbnail_Place">
					<a title="<?= $name ?>" alt="" style="">
						<?php if($pending == -1) :?>
							<img src='/images/unapproved.png' style='width: 100%;height: 100%;object-fit: contain;background-color: #521a1a82;'>
						<?php endif ?>
						<?php if($pending == 0) :?>
							<img src="https://asset.lambda.cam/place?id=<?=$id?>&type=img" style="background-color: white;object-fit: fill;width: 100%;height: 100%;">
						<?php endif ?>
						<?php if($pending == 1) :?>
							<img src="/images/unreviewed.png" style="background-color: white;object-fit: contain;width: 100%;height: 100%;">
						<?php endif ?>
					</a>
				</div>
				<div id="Actions_Place">
					<a id="ctl00_cphRoblox_FavoriteThisPlaceButton" disabled="disabled">Favorite</a>
				</div>
				<div id="Summary" style="margin-top:-231px">
					<h3>LAMBDA Place</h3>
					<div id="Creator" class="Creator">
						<div class="Avatar">
							<a id="ctl00_cphRoblox_AvatarImage" title="<?= $creator_name; ?>" href="/User.php?id=<?= $creator; ?>" style="display:inline-block;cursor:pointer;"><img src="https://asset.lambda.cam/user?id=<?=$creator?>" style="margin-top:-10px;margin-bottom:-10px;" border="0" width="56" height="75" alt="<?= $creator_name;?>" blankurl="http://t6-cf.roblox.com/blank-100x100.gif"></a>
						</div>
					</div>
					Creator: <a id="ctl00_cphRoblox_CreatorHyperLink" href="/User.php?id=<?=$creator; ?>"><?= $creator_name; ?></a><!--<div id="LastUpdate">Updated: 2 hours ago</div>-->
					<div id="Favorited">Favorited: 295 times</div>
					<div class="Visited">Visited: 3,267 times</div>
					<div class="Visited">Version: <?= $version; ?></div>
					<?php if($creator == $auth_user_id): ?>
					<a href="/Place/Edit.php?id=<?= $id ?>" class="Visited">&#60;&#60;&#32;Edit&#32;&#62;&#62;</a>
					<?php endif ?>
					<br>
					<div id="ctl00_cphRoblox_DescriptionPanel">
						<div id="DescriptionLabel">Description:</div>
						<div id="Description"><?= $description ?></div>
					</div>
					<!--<div id="ReportAbuse">
						<div id="ctl00_cphRoblox_AbuseReportButton1_AbuseReportPanel" class="ReportAbusePanel">
							<span class="AbuseIcon"><a id="ctl00_cphRoblox_AbuseReportButton1_ReportAbuseIconHyperLink" href="https://web.archive.org/web/20090627215620/http://www.roblox.com/AbuseReport/Asset.aspx?ID=8206908&amp;RedirectUrl=http%3a%2f%2fwww.roblox.com%2fItem.aspx%3fID%3d8206908"><img src="./index_files/abuse.PNG" alt="Report Abuse" border="0"></a></span>
							<span class="AbuseButton"><a id="ctl00_cphRoblox_AbuseReportButton1_ReportAbuseTextHyperLink" href="https://web.archive.org/web/20090627215620/http://www.roblox.com/AbuseReport/Asset.aspx?ID=8206908&amp;RedirectUrl=http%3a%2f%2fwww.roblox.com%2fItem.aspx%3fID%3d8206908">Report Abuse</a></span>
						</div>
					</div>-->
				</div>
				<div style="clear: both;"></div>
				<div id="Summary2" style="margin: 10px 10px;width: 845px;background-color: #fff;border: dashed 1px #555;display: table;float: left;padding: 7px;/* margin-top:-231px; */">
					<form name="input" action="/api.php" method="POST" style="text-align: left;">
					<input type="hidden" id="place_id" name="place_id" value="<?= $id ?>">
					<input type="hidden" id="port" name="port" value="-1">
					<?php
						if(!isset($_SESSION['authenticated'])) {
							echo "<h3 style='padding:5px; text-align:center;'>Register to see running games!</h3>";
						}
						else {
							$stmt = $con->prepare('SELECT * FROM `rcc_status` WHERE game_id LIKE ?');
							$stmt->bind_param('s', $id);
							$stmt->execute();
							
							$result = $stmt->get_result();
							$num_rows = $result->num_rows;
							
							$stmt2 = $con->prepare('SELECT * FROM `rcc_requests` WHERE game_id LIKE ?');
							$stmt2->bind_param('s', $id);
							$stmt2->execute();
							
							$result2 = $stmt2->get_result();
							$num_rows2 = $result2->num_rows;
							
							if($servers == true) {
								if($num_rows == 0 && $num_rows2 == 0) {
									echo "<h3 style='padding:5px; text-align:center;'>No open games open at this current moment of time...</h3>";
									echo "<div class='AspNet-Login-SubmitPanel' style='display: block;width: 100%;text-align: center'>\n";
										echo "<input type='submit' name='create_request' class='Button' style='border: solid 1px #777; color: #777;' value='Open a new game!'>\n";
									echo "</div>\n";
								} else if($num_rows2 != 0 && $num_rows == 0) {
									echo "<h3 style='padding:5px; text-align:center;'>A game is currently being requested... <br>(Wait around 1 to 5s if you have just requested one!)</h3>";
									
								} else {
									echo "<br>";
									$servers_available = false;
									while($row = $result->fetch_assoc()) {
										$port = $row['port'];
										$content = $row['content'];
										
										echo "<div>";
										list($plrs_cont, $max_size_cont, $kill_time) = explode(",", $content);
										list($plr_tag, $plrs) = explode(":", $plrs_cont);
										echo "<span style='text-align: center;padding: 10px;'>Players: ($plrs/12)</span>";
										
										if(str_contains($content, "plr_count:12")) {
											echo "<button class='Button'>Full.</button>";
										} else {
											echo "<input type='submit' name='join_place' class='Button' onclick='setPort($port);' style='border: solid 1px #777; color: #777;' value='Join game'>\n";
											$servers_available = true;
										}
										
										echo "</div>";
										echo "<br>";
									}
									if($servers_available == false) {
										echo "<h4>Looks like the servers are full...</h4>\n";
										echo "<div class='AspNet-Login-SubmitPanel' style='display: block;width: 100%;text-align: center'>\n";
											echo "<input type='submit' name='create_request' class='Button' style='border: solid 1px #777; color: #777;' value='Open a new game!'>\n";
										echo "</div>\n";
									}
								}
							} else {
								echo "<h3 style='padding:5px; text-align:center;'>Servers down!</h3>";
									
							}
						}
						
					?>
					</form>
				</div>
			</div>
			
		</div>
	</div>
</div>
<?php 
	include('../includes/footer.php');
?>

<!-- LATER PURPOSES (hello again!) -->
<!--<div style="margin: 10px; width: 703px; border: solid 1px black; background-color: White;">
				<div style="padding: 5px;">
				<div id="AssetRecommender" style="border: solid 1px grey; background-color: White;">
				<h3>Recommendations</h3>
				<div style="font-size: x-small;">Here are some other free games that we think you might like.</div>
				<table id="ctl00_cphRoblox_AssetRec_dlAssets" cellspacing="0" align="Center" border="0" height="200" width="600">
				<tbody><tr>
				<td>
				<div id="ctl00_cphRoblox_AssetRec_dlAssets_ctl00_LandscapeDiv" style="width: 162px; height: 170px; overflow:hidden;">
				<div class="AssetThumbnail">
				<a id="ctl00_cphRoblox_AssetRec_dlAssets_ctl00_AssetImage2" title="GO CAMPING!!!! V4 (VIPS!!)" href="https://web.archive.org/web/20090627215620/http://www.roblox.com/Item.aspx?ID=270411" style="display:inline-block;cursor:pointer;"><img src="./index_files/e6de7abb69a88e97e87fd46986df4c58" border="0" onerror="return Roblox.Controls.Image.OnError(this)" alt="GO CAMPING!!!! V4 (VIPS!!)"></a>
				</div>
				<div class="AssetDetails">
				<div class="AssetName"><a id="ctl00_cphRoblox_AssetRec_dlAssets_ctl00_AssetNameHyperLinkLandscape" href="https://web.archive.org/web/20090627215620/http://www.roblox.com/Item.aspx?ID=270411">GO CAMPING!!!! V4 (VIPS!!)</a></div>
				<div class="AssetCreator"><span class="Label">Creator:</span> <span class="Detail"><a id="ctl00_cphRoblox_AssetRec_dlAssets_ctl00_CreatorHyperLinkLandscape" href="https://web.archive.org/web/20090627215620/http://www.roblox.com/User.aspx?ID=129421">Roebot56</a></span></div>  
				</div>
				</div>
				</td><td>
				<div id="ctl00_cphRoblox_AssetRec_dlAssets_ctl01_LandscapeDiv" style="width: 162px; height: 170px; overflow:hidden;">
				<div class="AssetThumbnail">
				<a id="ctl00_cphRoblox_AssetRec_dlAssets_ctl01_AssetImage2" title="Build ur own FIREWORKS!" href="https://web.archive.org/web/20090627215620/http://www.roblox.com/Item.aspx?ID=336400" style="display:inline-block;cursor:pointer;"><img src="./index_files/0694865bc76b0b027ca46b091e591c2b" border="0" onerror="return Roblox.Controls.Image.OnError(this)" alt="Build ur own FIREWORKS!"></a>
				</div>
				<div class="AssetDetails">
				<div class="AssetName"><a id="ctl00_cphRoblox_AssetRec_dlAssets_ctl01_AssetNameHyperLinkLandscape" href="https://web.archive.org/web/20090627215620/http://www.roblox.com/Item.aspx?ID=336400">Build ur own FIREWORKS!</a></div>
				<div class="AssetCreator"><span class="Label">Creator:</span> <span class="Detail"><a id="ctl00_cphRoblox_AssetRec_dlAssets_ctl01_CreatorHyperLinkLandscape" href="https://web.archive.org/web/20090627215620/http://www.roblox.com/User.aspx?ID=163451">Wemmi0</a></span></div>  
				</div>
				</div>
				</td><td>
				<div id="ctl00_cphRoblox_AssetRec_dlAssets_ctl02_LandscapeDiv" style="width: 162px; height: 170px; overflow:hidden;">
				<div class="AssetThumbnail">
				<a id="ctl00_cphRoblox_AssetRec_dlAssets_ctl02_AssetImage2" title="Zombie Ocean: Rescue mission - Aero - (Free VIP.)" href="https://web.archive.org/web/20090627215620/http://www.roblox.com/Item.aspx?ID=148016" style="display:inline-block;cursor:pointer;"><img src="./index_files/6901f775fb124f0fee7be9723960d74c" border="0" onerror="return Roblox.Controls.Image.OnError(this)" alt="Zombie Ocean: Rescue mission - Aero - (Free VIP.)"></a>
				</div>
				<div class="AssetDetails">
				<div class="AssetName"><a id="ctl00_cphRoblox_AssetRec_dlAssets_ctl02_AssetNameHyperLinkLandscape" href="https://web.archive.org/web/20090627215620/http://www.roblox.com/Item.aspx?ID=148016">Zombie Ocean: Rescue mission - Aero - (Free VIP.)</a></div>
				<div class="AssetCreator"><span class="Label">Creator:</span> <span class="Detail"><a id="ctl00_cphRoblox_AssetRec_dlAssets_ctl02_CreatorHyperLinkLandscape" href="https://web.archive.org/web/20090627215620/http://www.roblox.com/User.aspx?ID=65105">FoxMcBanjo</a></span></div>  
				</div>
				</div>
				</td>
				</tr>
				</tbody></table>
				</div>
				</div>
			</div>
			<div style="margin: 10px; width: 703px;">
			<div class="ajax__tab_xp" id="ctl00_cphRoblox_TabbedInfo">
			<div id="ctl00_cphRoblox_TabbedInfo_header">
			<span id="__tab_ctl00_cphRoblox_TabbedInfo_GamesTab">
			<h3>Games</h3>
			</span><span id="__tab_ctl00_cphRoblox_TabbedInfo_CommentaryTab">
			<h3>Commentary</h3>
			</span>
			</div><div id="ctl00_cphRoblox_TabbedInfo_body">
			<div id="ctl00_cphRoblox_TabbedInfo_GamesTab">
			<div id="ctl00_cphRoblox_TabbedInfo_GamesTab_RunningGamesUpdatePanel">
			<p style="text-align: center;">There are no running games for this place.</p>
			<div class="FooterPager" style="text-align: center;">
			</div>
			<div class="RefreshRunningGames">
			<input type="submit" name="ctl00$cphRoblox$TabbedInfo$GamesTab$RefreshRunningGamesButton" value="Refresh" id="ctl00_cphRoblox_TabbedInfo_GamesTab_RefreshRunningGamesButton" class="Button">
			</div>
			</div>
			</div><div id="ctl00_cphRoblox_TabbedInfo_CommentaryTab" style="display:none;">
			<div id="ctl00_cphRoblox_TabbedInfo_CommentaryTab_CommentsPane_CommentsUpdatePanel">
			<div class="CommentsContainer">
			<h3>Comments (166)</h3>
			<div id="ctl00_cphRoblox_TabbedInfo_CommentaryTab_CommentsPane_CommentsRepeater_ctl00_HeaderPagerPanel" class="HeaderPager">
			<span id="ctl00_cphRoblox_TabbedInfo_CommentaryTab_CommentsPane_CommentsRepeater_ctl00_HeaderPagerLabel">Page 1 of 17</span>
			<a id="ctl00_cphRoblox_TabbedInfo_CommentaryTab_CommentsPane_CommentsRepeater_ctl00_HeaderPageSelector_Next" href="javascript:__doPostBack('ctl00$cphRoblox$TabbedInfo$CommentaryTab$CommentsPane$CommentsRepeater$ctl00$HeaderPageSelector_Next','')">Next <span class="NavigationIndicators">&gt;&gt;</span></a>
			</div>
			<div class="Comments">
			<div class="Comment">
			<div class="Commenter">
			<div class="Avatar">
			<a id="ctl00_cphRoblox_TabbedInfo_CommentaryTab_CommentsPane_CommentsRepeater_ctl01_AvatarImage" title="heidi2223" href="https://web.archive.org/web/20090627215620/http://www.roblox.com/User.aspx?ID=2972103" style="display:inline-block;cursor:pointer;"><img src="./index_files/f7d657c4bb6b4fa8434349a27be73bc3" border="0" onerror="return Roblox.Controls.Image.OnError(this)" alt="heidi2223" blankurl="http://t6-cf.roblox.com/blank-100x100.gif"></a></div>
			</div>
			<div class="Post">
			<div class="Audit">
			Posted
			4 hours ago
			by
			<a id="ctl00_cphRoblox_TabbedInfo_CommentaryTab_CommentsPane_CommentsRepeater_ctl01_UsernameHyperLink" href="https://web.archive.org/web/20090627215620/http://www.roblox.com/User.aspx?ID=2972103">heidi2223</a>
			</div>
			<div class="Content">kool</div>
			</div>
			<div style="clear: both;"></div>
			</div>
			<div class="AlternateComment">
			<div class="Commenter">
			<div class="Avatar">
			<a id="ctl00_cphRoblox_TabbedInfo_CommentaryTab_CommentsPane_CommentsRepeater_ctl02_AvatarImage" title="Willinator" href="https://web.archive.org/web/20090627215620/http://www.roblox.com/User.aspx?ID=752955" style="display:inline-block;cursor:pointer;"><img src="./index_files/eb492af683585291c78e20d350e5a326" border="0" onerror="return Roblox.Controls.Image.OnError(this)" alt="Willinator" blankurl="http://t6-cf.roblox.com/blank-100x100.gif"></a></div>
			</div>
			<div class="Post">
			<div class="Audit">
			Posted
			5 hours ago
			by
			<a id="ctl00_cphRoblox_TabbedInfo_CommentaryTab_CommentsPane_CommentsRepeater_ctl02_UsernameHyperLink" href="https://web.archive.org/web/20090627215620/http://www.roblox.com/User.aspx?ID=752955">Willinator</a>
			</div>
			<div class="Content">yeh i got banned so wat and there was an ad because i put over 2000 tix in it</div>
			</div>
			<div style="clear: both;"></div>
			</div>
			<div class="Comment">
			<div class="Commenter">
			<div class="Avatar">
			<a id="ctl00_cphRoblox_TabbedInfo_CommentaryTab_CommentsPane_CommentsRepeater_ctl03_AvatarImage" title="Blockfromdownthelane" href="https://web.archive.org/web/20090627215620/http://www.roblox.com/User.aspx?ID=1534253" style="display:inline-block;cursor:pointer;"><img src="./index_files/542d9a88dffcb0175900b711271f646e" border="0" onerror="return Roblox.Controls.Image.OnError(this)" alt="Blockfromdownthelane" blankurl="http://t6-cf.roblox.com/blank-100x100.gif"></a></div>
			</div>
			<div class="Post">
			<div class="Audit">
			Posted
			11 hours ago
			by
			<a id="ctl00_cphRoblox_TabbedInfo_CommentaryTab_CommentsPane_CommentsRepeater_ctl03_UsernameHyperLink" href="https://web.archive.org/web/20090627215620/http://www.roblox.com/User.aspx?ID=1534253">Blockfromdownthelane</a>
			</div>
			<div class="Content">...why is there an ad for this?</div>
			</div>
			<div style="clear: both;"></div>
			</div>
			<div class="AlternateComment">
			<div class="Commenter">
			<div class="Avatar">
			<a id="ctl00_cphRoblox_TabbedInfo_CommentaryTab_CommentsPane_CommentsRepeater_ctl04_AvatarImage" title="Fuzzbump" href="https://web.archive.org/web/20090627215620/http://www.roblox.com/User.aspx?ID=521492" style="display:inline-block;cursor:pointer;"><img src="./index_files/cba24afa36940a390cc9587faab268cc" border="0" onerror="return Roblox.Controls.Image.OnError(this)" alt="Fuzzbump" blankurl="http://t6-cf.roblox.com/blank-100x100.gif"></a></div>
			</div>
			<div class="Post">
			<div class="Audit">
			Posted
			14 hours ago
			by
			<a id="ctl00_cphRoblox_TabbedInfo_CommentaryTab_CommentsPane_CommentsRepeater_ctl04_UsernameHyperLink" href="https://web.archive.org/web/20090627215620/http://www.roblox.com/User.aspx?ID=521492">Fuzzbump</a>
			</div>
			<div class="Content">what?? owner got banned?? o.o</div>
			</div>
			<div style="clear: both;"></div>
			</div>
			<div class="Comment">
			<div class="Commenter">
			<div class="Avatar">
			<a id="ctl00_cphRoblox_TabbedInfo_CommentaryTab_CommentsPane_CommentsRepeater_ctl05_AvatarImage" title="Willinator" href="https://web.archive.org/web/20090627215620/http://www.roblox.com/User.aspx?ID=752955" style="display:inline-block;cursor:pointer;"><img src="./index_files/eb492af683585291c78e20d350e5a326" border="0" onerror="return Roblox.Controls.Image.OnError(this)" alt="Willinator" blankurl="http://t6-cf.roblox.com/blank-100x100.gif"></a></div>
			</div>
			<div class="Post">
			<div class="Audit">
			Posted
			17 hours ago
			by
			<a id="ctl00_cphRoblox_TabbedInfo_CommentaryTab_CommentsPane_CommentsRepeater_ctl05_UsernameHyperLink" href="https://web.archive.org/web/20090627215620/http://www.roblox.com/User.aspx?ID=752955">Willinator</a>
			</div>
			<div class="Content">one more thing is that i listen to u people i will make it rainbow if u'd like that better but the colorful the longer it will be until its done</div>
			</div>
			<div style="clear: both;"></div>
			</div>
			<div class="AlternateComment">
			<div class="Commenter">
			<div class="Avatar">
			<a id="ctl00_cphRoblox_TabbedInfo_CommentaryTab_CommentsPane_CommentsRepeater_ctl06_AvatarImage" title="Willinator" href="https://web.archive.org/web/20090627215620/http://www.roblox.com/User.aspx?ID=752955" style="display:inline-block;cursor:pointer;"><img src="./index_files/eb492af683585291c78e20d350e5a326" border="0" onerror="return Roblox.Controls.Image.OnError(this)" alt="Willinator" blankurl="http://t6-cf.roblox.com/blank-100x100.gif"></a></div>
			</div>
			<div class="Post">
			<div class="Audit">
			Posted
			17 hours ago
			by
			<a id="ctl00_cphRoblox_TabbedInfo_CommentaryTab_CommentsPane_CommentsRepeater_ctl06_UsernameHyperLink" href="https://web.archive.org/web/20090627215620/http://www.roblox.com/User.aspx?ID=752955">Willinator</a>
			</div>
			<div class="Content">the spaws that i'm using i was still testing and they failed as u can see so i'm goin to use regular spawns for now on</div>
			</div>
			<div style="clear: both;"></div>
			</div>
			<div class="Comment">
			<div class="Commenter">
			<div class="Avatar">
			<a id="ctl00_cphRoblox_TabbedInfo_CommentaryTab_CommentsPane_CommentsRepeater_ctl07_AvatarImage" title="Willinator" href="https://web.archive.org/web/20090627215620/http://www.roblox.com/User.aspx?ID=752955" style="display:inline-block;cursor:pointer;"><img src="./index_files/eb492af683585291c78e20d350e5a326" border="0" onerror="return Roblox.Controls.Image.OnError(this)" alt="Willinator" blankurl="http://t6-cf.roblox.com/blank-100x100.gif"></a></div>
			</div>
			<div class="Post">
			<div class="Audit">
			Posted
			17 hours ago
			by
			<a id="ctl00_cphRoblox_TabbedInfo_CommentaryTab_CommentsPane_CommentsRepeater_ctl07_UsernameHyperLink" href="https://web.archive.org/web/20090627215620/http://www.roblox.com/User.aspx?ID=752955">Willinator</a>
			</div>
			<div class="Content">okay look everyone who posted i'm sorry but it is closed for major update and i will add peoples birthday eventually</div>
			</div>
			<div style="clear: both;"></div>
			</div>
			<div class="AlternateComment">
			<div class="Commenter">
			<div class="Avatar">
			<a id="ctl00_cphRoblox_TabbedInfo_CommentaryTab_CommentsPane_CommentsRepeater_ctl08_AvatarImage" title="Mryoyoyo5" href="https://web.archive.org/web/20090627215620/http://www.roblox.com/User.aspx?ID=434222" style="display:inline-block;cursor:pointer;"><img src="./index_files/cb6c27f05ac3c6f26e22dc8112e2d0e8" border="0" onerror="return Roblox.Controls.Image.OnError(this)" alt="Mryoyoyo5" blankurl="http://t6-cf.roblox.com/blank-100x100.gif"></a></div>
			</div>
			<div class="Post">
			<div class="Audit">
			Posted
			17 hours ago
			by
			<a id="ctl00_cphRoblox_TabbedInfo_CommentaryTab_CommentsPane_CommentsRepeater_ctl08_UsernameHyperLink" href="https://web.archive.org/web/20090627215620/http://www.roblox.com/User.aspx?ID=434222">Mryoyoyo5</a>
			</div>
			<div class="Content">You need to add more spawn points, because when I die I start WAAAAY back more than expected.</div>
			</div>
			<div style="clear: both;"></div>
			</div>
			<div class="Comment">
			<div class="Commenter">
			<div class="Avatar">
			<a id="ctl00_cphRoblox_TabbedInfo_CommentaryTab_CommentsPane_CommentsRepeater_ctl09_AvatarImage" title="footballfear" href="https://web.archive.org/web/20090627215620/http://www.roblox.com/User.aspx?ID=1937068" style="display:inline-block;cursor:pointer;"><img src="./index_files/873f993b4c1669a18e003f7eaf87128b" border="0" onerror="return Roblox.Controls.Image.OnError(this)" alt="footballfear" blankurl="http://t6-cf.roblox.com/blank-100x100.gif"></a></div>
			</div>
			<div class="Post">
			<div class="Audit">
			Posted
			18 hours ago
			by
			<a id="ctl00_cphRoblox_TabbedInfo_CommentaryTab_CommentsPane_CommentsRepeater_ctl09_UsernameHyperLink" href="https://web.archive.org/web/20090627215620/http://www.roblox.com/User.aspx?ID=1937068">footballfear</a>
			</div>
			<div class="Content">Posted 2 minutes ago by footballfear 
			<br>WANNA COME TO A FUN AND HARD OBBY? THEN COME TO FOOTBALLFEARS PLACE.. CYA THERE!! PLZ PLZ COME TO MY OBBY PLZ IT IS LONG, FUN, AND HARD!</div>
			</div>
			<div style="clear: both;"></div>
			</div>
			<div class="AlternateComment">
			<div class="Commenter">
			<div class="Avatar">
			<a id="ctl00_cphRoblox_TabbedInfo_CommentaryTab_CommentsPane_CommentsRepeater_ctl10_AvatarImage" title="KakashiHitake" href="https://web.archive.org/web/20090627215620/http://www.roblox.com/User.aspx?ID=2066800" style="display:inline-block;cursor:pointer;"><img src="./index_files/7bc4419eb6710a1681af53851d238bc3" border="0" onerror="return Roblox.Controls.Image.OnError(this)" alt="KakashiHitake" blankurl="http://t6-cf.roblox.com/blank-100x100.gif"></a></div>
			</div>
			<div class="Post">
			<div class="Audit">
			Posted
			18 hours ago
			by
			<a id="ctl00_cphRoblox_TabbedInfo_CommentaryTab_CommentsPane_CommentsRepeater_ctl10_UsernameHyperLink" href="https://web.archive.org/web/20090627215620/http://www.roblox.com/User.aspx?ID=2066800">KakashiHitake</a>
			</div>
			<div class="Content">U suck at obbys</div>
			</div>
			<div style="clear: both;"></div>
			</div>
			</div>
			<div id="ctl00_cphRoblox_TabbedInfo_CommentaryTab_CommentsPane_CommentsRepeater_ctl11_FooterPagerPanel" class="FooterPager">
			<span id="ctl00_cphRoblox_TabbedInfo_CommentaryTab_CommentsPane_CommentsRepeater_ctl11_FooterPagerLabel">Page 1 of 17</span>
			<a id="ctl00_cphRoblox_TabbedInfo_CommentaryTab_CommentsPane_CommentsRepeater_ctl11_FooterPageSelector_Next" href="javascript:__doPostBack('ctl00$cphRoblox$TabbedInfo$CommentaryTab$CommentsPane$CommentsRepeater$ctl11$FooterPageSelector_Next','')">Next <span class="NavigationIndicators">&gt;&gt;</span></a>
			</div>
			</div>
			</div>
			</div>
			</div>
			</div>
			</div>
			-->
