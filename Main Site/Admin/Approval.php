<?php 
	include('../includes/header.php');
	include('../includes/dbcon.php');
	
	if(isset($_SESSION['auth_user'])) {
		$id = $_SESSION['auth_user']['id'];
		
		//sanitize
		$stmt = $con->prepare('SELECT * FROM users WHERE id LIKE ?');
		$stmt -> bind_param('s', $id);
		$stmt -> execute();
		
		$stmt_result = $stmt->get_result();
		$result = $stmt_result->num_rows;
		
		$badges = "";
		
		if($result == 0) {
			header("Location: /error/404.php");
			exit(0);
		}
		
		while($row_data = $stmt_result->fetch_assoc()) {
			$badges = $row_data['badges'];
		}
		
		if($badges != "0,0,0,0,0,0,0") {
			list($admin, $friendship, $homestead, $bricksmith, $veteran, $inviter, $bc) = explode(",", $badges);
		} else {
			$badges = "none";
		}
		
		if($badges != "none") {
			if($admin != "1") {
				header("Location: /error/404.php");
				exit(0);
			}
		} else {
			header("Location: /error/404.php");
			exit(0);
		}
	} else {
		header("Location: /error/404.php");
		exit(0);
	}
	
	$page = 0;
	
	$start = 25 + 25 * ($page - 1);
	$rows = 25;
	
	$stmt = $con->prepare('SELECT * FROM places WHERE pending = 1 LIMIT ?, ?');
	$stmt->bind_param('ss', $start, $rows);
	$stmt->execute();
	
	$result = $stmt->get_result();
	$num_rows = $result->num_rows;
	
	/*if($num_rows == "0") {
		$prev_page = ($page-1)+1;
		if($prev_page >= 1) {
			$page_url = "/Browse.php?page=$prev_page";
			header('Location:'.$page_url);
		}
	}*/
	
	$total_query = mysqli_query($con, "SELECT * FROM places WHERE pending = 1");
	$total_rows = mysqli_num_rows($total_query);
	$total_pages = (int)($total_rows / 26)+1;
?>
<div id="Body">
	<script>
		function reject(id){
			document.getElementById("id").value = id;
			document.getElementById("approve").value = 0;
		}
		
		function accept(id){
			document.getElementById("id").value = id;
			document.getElementById("approve").value = 1;
		}
	</script>
	
	<div id="GamesContainer">
		<div id="GamesContainerPanel">
			<form id="Games" action="/api.php" method="POST">
				<input type="hidden" id="id" name="id" value="-1" />
				<input type="hidden" id="approve" name="approve" value="-1" />
				<?php
					while($row = $result->fetch_assoc()) {
						/*echo '<tr class="GridItem">';
						$status = "Offline";
						if($row['status'] == 1) {
							$status = "Online";
						}*/
						
						$id = $row['id'];
						$name = $row['name'];
						$creator = $row['creator'];
						$pending = $row['pending'];
						
						$stmt_user = $con->prepare('SELECT * FROM users WHERE id LIKE ?');
						$stmt_user -> bind_param('s', $creator);
						$stmt_user -> execute();
						
						$stmt_result_user = $stmt_user->get_result();
						$result_user = $stmt_result_user->num_rows;
						
						while($row_user = $stmt_result_user->fetch_assoc()) {
							$creator_name = $row_user['username'];
						}
						
						
						
						echo "<div style='height: 175px; width: 180px; float: left;'>\n";
							echo "<div style='margin-left: 0px;'>\n";
								echo "<div class='GameThumbnail12' style='margin: 0; width: 160px; height: 100px; border: 1px solid black;'>\n";
									echo "<a id='Game' title='$name' style='cursor:pointer;'>\n";
										echo "<img src='https://asset.lambda.cam/place?id=$id&type=img' border='0' alt='$name' style='width: 100%;height: 100%;object-fit: fill;'>\n";
									echo "</a>\n";
								echo "</div>\n";
							echo "</div>\n";
							echo "<div class='GameDetails12' style='margin: 0; width: 160px;'>\n";
								echo "<div class='GameName'>\n";
									echo "<a id='GameName' href='/Place/View.php?id=$id'>$name</a>\n";
								echo "</div>\n";
								echo "<div class='GameCreator'>\n";
									echo "<span class='Label'>Creator: </span><span class='Detail'><a id='GameCreator' href='/User.php?id=$creator'>$creator_name</a></span>\n";
								echo "</div>\n";
								echo "<br>\n";
								echo "<button type='submit' class='Button' style='width: 80px;margin-left: 0px;' onclick='accept($id);'>Approve</button>\n";
								echo "<button type='submit' class='Button' style='width: 80px;float: right;' onclick='reject($id);'>Reject</button>\n";
							echo "</div>\n";
						echo "</div>\n";
					}
				?>
			</form>
		</div>
		
		<div style="clear: both;">
			<div style="padding-bottom: 10px;display: table;margin: 0 auto;">
				<div id="ctl00_cphRoblox_rbxGames_HeaderPagerPanel" class="HeaderPager">
					<span>Page 1 of 2:</span>
					<a>Next <span class="NavigationIndicators">&gt;&gt;</span></a>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
	include('../includes/footer.php');
?>

<!-- for later use (hi code peeker!!) -->
<!--<div id="ctl00_cphRoblox_rbxGames_GamesRepeater_ctl01_pGameCurrentPlayers">
<div class="GameCurrentPlayers"><span class="DetailHighlighted">331 players online</span></div>
</div>-->
<!--<span id="ctl00_cphRoblox_rbxGames_lGamesDisplaySet" class="GamesDisplaySet">Most Popular (Now)</span>-->
<!--<div class="DisplayFilters">
<h2>Games&nbsp;<a id="ctl00_cphRoblox_rbxGames_hlNewsFeed" href="https://web.archive.org/web/20090627023013/http://www.roblox.com/Games.aspx?feed=rss"><img src="./index_files/feed-icon-14x14.png" alt="RSS" border="0"></a></h2>
<div id="BrowseMode">
<h4>Browse</h4>
<ul>
<li><img id="ctl00_cphRoblox_rbxGames_MostPopularBullet" class="GamesBullet" src="./index_files/games_bullet.png" alt="Bullet" border="0"><a id="ctl00_cphRoblox_rbxGames_hlMostPopular" href="https://web.archive.org/web/20090627023013/http://www.roblox.com/Games.aspx?m=MostPopular&amp;t=Now"><b>Most Popular</b></a></li>
<li><a id="ctl00_cphRoblox_rbxGames_hlTopFavorites" href="https://web.archive.org/web/20090627023013/http://www.roblox.com/Games.aspx?m=TopFavorites&amp;t=AllTime">Top Favorites</a></li>
<li><a id="ctl00_cphRoblox_rbxGames_hlRecentlyUpdated" href="https://web.archive.org/web/20090627023013/http://www.roblox.com/Games.aspx?m=RecentlyUpdated">Recently Updated</a></li>
<li><a id="ctl00_cphRoblox_rbxGames_hlFeatured" href="https://web.archive.org/web/20090627023013/http://www.roblox.com/User.aspx?ID=1">Featured Games</a></li>
</ul>
</div>
<div id="ctl00_cphRoblox_rbxGames_pTimespan">
<div id="Timespan">
<h4>Time</h4>
<ul>
<li id="ctl00_cphRoblox_rbxGames_liNow"><img id="ctl00_cphRoblox_rbxGames_TimespanNowBullet" class="GamesBullet" src="./index_files/games_bullet.png" alt="Bullet" border="0"><a id="ctl00_cphRoblox_rbxGames_hlTimespanNow" href="https://web.archive.org/web/20090627023013/http://www.roblox.com/Games.aspx?m=MostPopular&amp;t=Now"><b>Now</b></a></li>
<li><a id="ctl00_cphRoblox_rbxGames_hlTimespanPastDay" href="https://web.archive.org/web/20090627023013/http://www.roblox.com/Games.aspx?m=MostPopular&amp;t=PastDay">Past Day</a></li>
<li><a id="ctl00_cphRoblox_rbxGames_hlTimespanPastWeek" href="https://web.archive.org/web/20090627023013/http://www.roblox.com/Games.aspx?m=MostPopular&amp;t=PastWeek">Past Week</a></li>
<li><a id="ctl00_cphRoblox_rbxGames_hlTimespanPastMonth" href="https://web.archive.org/web/20090627023013/http://www.roblox.com/Games.aspx?m=MostPopular&amp;t=PastMonth">Past Month</a></li>
<li><a id="ctl00_cphRoblox_rbxGames_hlTimespanAllTime" href="https://web.archive.org/web/20090627023013/http://www.roblox.com/Games.aspx?m=MostPopular&amp;t=AllTime">All-time</a></li>
</ul>
</div>
</div>
</div>-->