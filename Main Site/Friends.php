<?php
	include('includes/dbcon.php');
	session_start();
	if(!isset($_GET['id'])) {
		if(isset($_SESSION['auth_user'])) {
			$id = $_SESSION['auth_user']['id'];
		} else {
			header('Location: /index.php');
		}
	} else {
		if (!is_numeric($_GET['id'])) {
			$id = $_SESSION['auth_user']['id'];
		} else {
			$id = $_GET['id'];
		}
	}
	
	if(!isset($_GET['tab'])) {
		$tab = 0;
	} else {
		if (!is_numeric($_GET['tab'])) {
			header('Location: /Friends.php');
		} else {
			$tab = $_GET['tab'];
			if($tab < 0 || $tab > 2) {
				$tab = 0;
			}
		}
	}
	
	//sanitize
	$stmt = $con->prepare('SELECT * FROM users WHERE id LIKE ?');
	$stmt -> bind_param('s', $id);
	$stmt -> execute();
	
	$stmt_result = $stmt->get_result();
	$result = $stmt_result->num_rows;
	
	$name = "";
	$status = "1";
	
	if($result == 0) {
		header('Location: /index.php');
	}
	
	while($row_data = $stmt_result->fetch_assoc()) {
		$name = $row_data['username'];
		$status = $row_data['status'];
	}
	
	$title = "$name's Friends";
	$loc = "yeh";
	include('includes/header.php');
	
	$name = str_replace("<","&lt", $name); 
	$name = str_replace(">","&gt", $name);
	
?>

<script>
	function reject(id){
		document.getElementById("id_"+id).value = id;
		document.getElementById("id_"+id).name = "unfriend";
	}
	
	function accept(id){
		document.getElementById("id_"+id).value = id;
		document.getElementById("id_"+id).name = "friend";
	}
</script>

<div id="Body" style="text-align: center; margin: 10px auto 150px auto; width: 855px; border: black thin solid; padding: 22px; background-color: white;">
	
	<div id="Friends">
		<?php if($tab == 0 && $auth_user_id != $id) :?>
		<h4><?= $name; ?>'s Friends</h4>
		<?php endif ?>
		<?php if($tab == 0 && $auth_user_id == $id) :?>
		<h4>Your Friends</h4>
		<?php endif ?>
		
		<?php if($id == $auth_user_id) :?>
		<?php if($tab == 1) :?>
		<h4>Your requests.</h4>
		( Incoming / Outgoing )
		<form id="Friends_Request" action="/api.php" method="POST">
		<input type="hidden" id="return" name="return" value="<?= $id ?>" />
		<?php endif ?>
		<?php 
			$stmt_total = $con->prepare('SELECT * FROM friends WHERE (user1 = ? OR user2 = ?) AND status = 0');
			$stmt_total -> bind_param('ss', $id, $id);
			$stmt_total -> execute();
			
			$stmt_result_total = $stmt_total->get_result();
			$result_total = $stmt_result_total->num_rows; 
		?>
		<div><a href="/Friends.php?id=<?=$id; ?>&tab=0">All friends</a> | <a href="/Friends.php?id=<?=$id; ?>&tab=1">Requests (<?= $result_total; ?>)</a></div>
		<?php endif ?>
		<div style="overflow: auto;" cellspacing="0" border="0" align="Center">
			<?php
				
				$valid = "1";
				
				if($tab == 0) {
					$stmt = $con->prepare('SELECT * FROM friends WHERE (user1 = ? OR user2 = ?) AND status = ?');
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
				} else if($tab == 1) {
					if($auth_user_id != $id) {
						header("Location: /Friends.php?id=$id");
						exit();
					}
					$stmt = $con->prepare('SELECT * FROM friends WHERE (user1 = ? OR user2 = ?) AND status = 0');
					$stmt -> bind_param('ss', $id, $id);
					$stmt -> execute();
					
					$stmt_result = $stmt->get_result();
					$result = $stmt_result->num_rows;
					if($result == 0) {
						echo "<h5>You do not have any requests.</h5>";
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
							echo "<div class='Friend' style='width:120px'>";
								echo "<div class='Avatar' style='width: 100px;'>";
									echo "<a title='$friend_name' href='/User.php?id=$friend_id' style='display:inline-block;cursor:pointer;'>";
										echo "<img src='https://asset.lambda.cam/user?id=$friend_id' alt='$friend_name' style='width: 100px;' border='0'>";
									echo "</a>";
								echo "</div>";
								echo "<div class='Summary'>";
									echo "<span class='OnlineStatus'><img src='/images/OnlineStatusIndicator_Is$status.gif' alt='$friend_name is $status (last seen at 6/27/2009 7:57:01 AM).' border='0'></span>";
									echo "<span class='Name'><a href='/User.php?id=$friend_id'>$friend_name</a></span>";
								echo "</div>";
								echo "<div class='Summary'>";
									echo "<span class='Name'>";
										if($user2 == $id) {
											echo "<input type='hidden' id='id_$friend_id' name='id' value='-1' />";
											echo "<button type='submit' class='Button' onclick='accept($friend_id);'>Accept</button>";
											echo "<button type='submit' class='Button' style='margin-left: 0px;float: right;' onclick='reject($friend_id);'>Reject</button>";
										} else {
											echo "<input type='hidden' id='id_$friend_id' name='id' value='-1' />";
											echo "<button type='submit' class='Button' onclick='reject($friend_id);'>Cancel</button>";
										}
									echo"</span>";
								echo "</div>";
							echo "</div>";
						}
					}
				} else if($tab == 2) {
					// show outgoing requests
				}
			?>
		</div>
		<?php if($tab == 1) :?>
		</form>
		<?php endif ?>
	</div>
</div>
<?php
	include('includes/footer.php');
?>
