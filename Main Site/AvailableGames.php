<?php 
	include("includes/header.php");
	include("includes/dbcon.php");
?>
<div id="Body">
	<script>
		function joingame(placeid,port) {
			document.getElementById('port').value = port;
			document.getElementById('place_id').value = placeid;
		}
	</script>
	<div id="ItemContainer">
		<div id="Item">
			<h2>Open Games...</h2>
			<div id="Details">
				<div id="Summary2" style="margin: 10px 10px;width: 845px;background-color: #fff;border: dashed 1px #555;display: table;float: left;padding: 7px;/* margin-top:-231px; */">
					<br>
					<form name="input" action="/api.php" method="POST" style="text-align: left;">
					<input type="hidden" id="place_id" name="place_id" value="-1">
					<input type="hidden" id="port" name="port" value="-1">
					<?php
						
						//$stmt = $con->prepare('DELETE FROM `rcc_status` WHERE sent_at < DATE_SUB(NOW(),INTERVAL 15 SECOND);');
						//$stmt -> execute();
						
						$stmt = $con->prepare('SELECT * FROM `rcc_status`');
						$stmt->execute();
						
						$result = $stmt->get_result();
						$num_rows = $result->num_rows;
						
						if($num_rows != 0) {
							while($row = $result->fetch_assoc()) {
								$game_id = $row['game_id'];
								$port = $row['port'];
								$content = $row['content'];
								
								
								$stmt_place = $con->prepare('SELECT * FROM `places` WHERE id = ?');
								$stmt_place->bind_param('s', $game_id);
								$stmt_place->execute();
								
								$result_place = $stmt_place->get_result();
								$num_rows_place = $result_place->num_rows;
								
								while($row_yeah = $result_place->fetch_assoc()) {
									$game_name = $row_yeah['name'];
									$version = $row_yeah['version'];
								}
											
								echo "<div>";
								list($plrs_cont, $max_size_cont, $kill_time) = explode(",", $content);
								list($plr_tag, $plrs) = explode(":", $plrs_cont);
								echo "<span style='text-align: center;padding: 10px;'><a href='/Place/View.php?id=$game_id'>$game_name</a> $version Players: ($plrs/12)</span>";
								
								if(isset($_SESSION['authenticated'])) {
									if(str_contains($content, "plr_count:12")) {
										echo "<button class='Button'>Full.</button>";
									} else {
										echo "<input type='submit' name='join_place' class='Button' onclick='joingame(".$game_id.", ".$port.");' style='border: solid 1px #777; color: #777;' value='Join game'>\n";
									}
								} else {
									echo "<button class='Button'>Login!</button>";
								}
								
								echo "</div>";
								echo "<br>";
							}
						} else {
							echo "<h3 style='text-align: center;'>Looks like there are no games open... Try the <a href='/Games.php'>games</a> section!</h3>";
						}
						
					?>
					</form>
				</div>
			</div>
		</div>
	</div>
</div>
<?php 
	include("includes/footer.php");
?>