<?php 
	include('includes/header.php');
	include('includes/dbcon.php');
	
	$getpage = $_GET["page"];
	
	if(!isset($_GET["page"])) {
		$getpage = 1;
	}
	
	$page = $getpage - 1;
	
	$start = 16 + 16 * ($page - 1);
	$rows = 16;
	
	$stmt = $con->prepare('SELECT * FROM places WHERE pending = 0 LIMIT ?, ?');
	$stmt->bind_param('ss', $start, $rows);
	$stmt->execute();
	
	$result = $stmt->get_result();
	$num_rows = $result->num_rows;
	
	if($num_rows == 0) {
		$prev_page = ($page-1)+1;
		if($prev_page >= 1) {
			$page_url = "/Games.php?page=$prev_page";
			header("Location: ".$page_url);
		}
	}
	
	$total_query = mysqli_query($con, "SELECT * FROM places WHERE pending = 0");
	$total_rows = mysqli_num_rows($total_query);
	$total_pages = (int)($total_rows / 16)+1;
	
	$start_checklast = 16 + 16 * ($total_pages-2);
	$rows_checklast = 16;
	
	$stmt_checklast = $con->prepare('SELECT * FROM places WHERE pending = 0 LIMIT ?, ?');
	$stmt_checklast->bind_param('ss', $start_checklast, $rows_checklast);
	$stmt_checklast->execute();
	
	$result_checklast = $stmt_checklast->get_result();
	$num_rows_checklast = $result_checklast->num_rows;
	
	if($num_rows_checklast == 0) {
		$total_pages = $total_pages - 1;
	}
?>
<div id="Body">
	<div id="GamesContainer">
		<div id="GamesContainerPanel">
			<?php if(isset($_SESSION['authenticated'])) :?>
				<div style="clear:both;">
					<div style="padding-bottom: 10px;display: table;margin: 0 auto;">
						<div id="HeaderPagerPanel" class="HeaderPager" style="width: 205px;">
							<button name="upload" class="Button " onclick="location.href = '/Place/Upload.php';" style="float: left;">Upload your place!</button>
							<button name="opengames" class="Button " onclick="location.href = '/AvailableGames.php';" style="float: right;">Open games!</button>
						</div>
						
					</div>
					<div style="padding-bottom: 10px; display: table; margin: 0 auto;">
						<div id="ctl00_cphRoblox_rbxGames_HeaderPagerPanel" class="HeaderPager">
							<?php if($getpage != $total_pages): ?>
							<span>Page <?= $getpage ?> of <?= $total_pages ?>:</span>
							<a href="/Games.php?page=<?= $getpage+1 ?>">Next <span class="NavigationIndicators">&gt;&gt;</span></a>
							<?php endif ?>
							<?php if($getpage == $total_pages && $getpage == 1): ?>
							<span>Page <?= $getpage ?> of <?= $total_pages ?></span>
							<?php endif ?>
							<?php if($getpage == $total_pages && $getpage != 1): ?>
							<a href="/Games.php?page=<?= $getpage-1 ?>"><span class="NavigationIndicators">&#60;&#60;</span>Back</a>
							<span>Page <?= $getpage ?> of <?= $total_pages ?></span>
							<?php endif ?>
						</div>
					</div>
				</div>
			<?php endif ?>
			<div class="DisplayFilters">
				<h2>Games</h2>
				<div id="BrowseMode">
					<h4>Browse</h4>
					<ul>
						<li><img id="MostPopularBullet" class="GamesBullet" src="/images/games_bullet.png" alt="Bullet" border="0"><a id="MostPopular" href="Games.php?m=MostPopular&amp;t=Now"><b>Most Popular</b></a></li>
						<li><a id="TopFavorites" href="Games.php?m=TopFavorites&amp;t=AllTime">Top Favorites</a></li>
						<li><a id="RecentlyUpdated" href="Games.php?m=RecentlyUpdated">Recently Updated</a></li>
						<li><a id="Featured" href="User.php?id=1">Featured Games</a></li>
					</ul>
				</div>
				<div id="Timespan">
					<div id="Timespan">
						<h4>Time</h4>
						<ul>
							<li id="Now"><img class="GamesBullet" src="/images/games_bullet.png" alt="Bullet" border="0"><a href="Games.php?m=MostPopular&amp;t=Now"><b>Now</b></a></li>
							<li><a id="TimespanPastDay" href="Games.php?m=MostPopular&amp;t=PastDay">Past Day</a></li>
							<li><a id="TimespanPastWeek" href="Games.php?m=MostPopular&amp;t=PastWeek">Past Week</a></li>
							<li><a id="TimespanPastMonth" href="Games.php?m=MostPopular&amp;t=PastMonth">Past Month</a></li>
							<li><a id="TimespanAllTime" href="Games.php?m=MostPopular&amp;t=AllTime">All-time</a></li>
						</ul>
					</div>
				</div>
			</div>
			<div id="Games">
				<div style="clear: both;">
					
				</div>
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
						$version = $row['version'];
						$pending = $row['pending'];
						
						if($pending == 1) {
							continue;
						}
						
						$user_stmt = $con->prepare('SELECT * FROM users WHERE id LIKE ?');
						$user_stmt -> bind_param('s', $creator);
						$user_stmt -> execute();
						
						$user_stmt_result = $user_stmt->get_result();
						
						$creator_name = "";
						
						while($user = $user_stmt_result->fetch_assoc()) {
							$creator_name = $user['username'];
						}
						
						echo "<div style='height: 175px; width: 180px; float: left;'>\n";
						echo "<div style='margin-left: 18px;'>\n";
						
						echo "<div class='GameThumbnail'>\n";
						echo "<span class='GameVersion'>$version</span>\n";
						echo "<a id='Game' title='$name' href='/Place/View.php?id=$id' style='cursor:pointer;'>\n";
						
						if($pending == 1) {
							echo "<img src='/images/unreviewed.png' border='0' alt='$name' style='width: 100%;height: 100%;object-fit: contain;background-color: white;'>\n";
						} else if($pending == 0) {
							echo "<img src='https://asset.lambda.cam/place?id=$id' border='0' alt='$name' style='width: 100%;height: 100%;object-fit: fill;background-color: white;'>\n";
						} else {
							echo "<img src='/images/unapproved.png' border='0' alt='$name' style='width: 100%;height: 100%;object-fit: contain;background-color: #521a1a82;'>\n";
						}
						
						$stmt_online = $con->prepare('SELECT * FROM `rcc_status` WHERE game_id LIKE ?');
						$stmt_online->bind_param('s', $id);
						$stmt_online->execute();
						
						$result_online = $stmt_online->get_result();
						$num_rows_online = $result_online->num_rows;
						$players_online = 0;
						while($row_online = $result_online->fetch_assoc()) {
							$content = $row_online['content'];
							
							list($plrs_cont, $max_size_cont, $kill_time) = explode(",", $content);
							list($plr_tag, $plrs) = explode(":", $plrs_cont);
							
							$players_online += $plrs;
						}
						
						echo "</a>\n";
						echo "</div>\n";
						
						echo "<div class='GameDetails12' style='margin: 0; width: 160px;'>\n";
						echo "<div class='GameName'>\n";
						echo "<a id='GameName' href='/Place/View.php?id=$id'>$name</a>\n";
						echo "</div>\n";
						echo "<div class='GameCreator'>\n";
						echo "<span class='Label'>Creator:&#32;</span>\n";
						echo "<span class='Detail'><a id='GameCreator' href='/User.php?id=$creator'>$creator_name</a></span>\n";
						echo "<br>\n";
						echo "</div>\n";
						echo "<div class='GamePlays'>\n";
						echo "<span class='Label'>Favorited:&#32;</span><span class='Detail'>0 times</span><br>\n";
						echo "<span class='Label'>Played:&#32;</span><span class='Detail'>0 times</span>\n";
						echo "</div>\n";
						echo "<div>\n";
						if($players_online != 0) {
							if($players_online == 1) {
								echo "<div class='GameCurrentPlayers'><span class='DetailHighlighted'>$players_online player online</span></div>\n";
							} else {
								echo "<div class='GameCurrentPlayers'><span class='DetailHighlighted'>$players_online players online</span></div>\n";
							}
						} else {
							echo "<div class='GameCurrentPlayers'><span class='DetailHighlighted'>No players online</span></div>\n";
						}
						echo "</div>\n";
						echo "</div>\n";
						echo "</div>\n";
						echo "</div>\n";
						
						/*echo "<td>";
						echo "<a title='$username' href='/User.php?id=$id' style='display:inline-block;cursor:pointer;'><img src='/images/player.png' width='36px' height='48px' border='0' alt='$username'></a>";
						echo "</td>";
						echo "<td>";
						echo "<a href='/User.php?id=$id'>$username</a><br>";
						echo "<span style='display: block;width: 512px;word-wrap: break-word;margin: 0 auto;'>$blurb</span>";
						echo"</td>";
						echo "<td>";
						echo "<span>$status</span><br>";
						echo "</td>";
						echo "<td>";
						echo "<span>$last_online</span>";
						echo "</td>";
						echo "</tr>";*/
					}
				?>
				
			</div>
		</div>
		<div style="clear: both;">
			<div style="padding-bottom: 10px;display: table;margin: 0 auto;">
				<div id="ctl00_cphRoblox_rbxGames_HeaderPagerPanel" class="HeaderPager">
					<?php if($getpage != $total_pages): ?>
					<span>Page <?= $getpage ?> of <?= $total_pages ?>:</span>
					<a href="/Games.php?page=<?= $getpage+1 ?>">Next <span class="NavigationIndicators">&gt;&gt;</span></a>
					<?php endif ?>
					<?php if($getpage == $total_pages && $getpage == 1): ?>
					<span>Page <?= $getpage ?> of <?= $total_pages ?></span>
					<?php endif ?>
					<?php if($getpage == $total_pages && $getpage != 1): ?>
					<a href="/Games.php?page=<?= $getpage-1 ?>"><span class="NavigationIndicators">&#60;&#60;</span>Back</a>
					<span>Page <?= $getpage ?> of <?= $total_pages ?></span>
					<?php endif ?>
				</div>
			</div>
		</div>
	</div>
</div>
<?php
	include('includes/footer.php');
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