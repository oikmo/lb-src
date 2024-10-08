<?php
	session_start();
	include('../includes/dbcon.php');
	
	if(isset($_GET['code']) && $_GET['code'] == "chatomg") {
		if(!isset($_GET['id']) && !isset($_GET['port']) && !isset($_GET['msg']) && isset($_GET['insert']) && isset($_GET['game_id'])) {
			//echo "yeah;.";
			if($_GET['insert'] == "1") {
				$stmt = $con->prepare('SELECT * FROM `rcc_status` WHERE game_id = ?');
				$stmt -> bind_param('s', $_GET['game_id']);
				$stmt -> execute();
				
				$stmt_result = $stmt->get_result();
				$result = $stmt_result->num_rows;
				
				$available = false;
				while($row_data = $stmt_result->fetch_assoc()) {
					$content = $row_data['content'];
					
					list($plrs_cont, $max_size_cont, $kill_time) = explode(",", $content);
					list($plr_tag, $plrs) = explode(":", $plrs_cont);
					
					if($plrs < 12) {
						$available = true;
					}
				}
				if($available == false) {
					$stmt = $con->prepare('INSERT INTO `rcc_requests`(`game_id`) VALUES (?);');
					$stmt -> bind_param('s', $_GET['game_id']);
					$stmt -> execute();
				}
			} else if($_GET['insert'] == "0") {
				$stmt = $con->prepare('SELECT * FROM `rcc_requests`;');
				$stmt -> execute();
				
				$stmt_result = $stmt->get_result();
				$result = $stmt_result->num_rows;
				//echo $result;
				if($result != 0) {
					$total_string = "";
					while($row_data = $stmt_result->fetch_assoc()) {
						$id = $row_data['game_id'];
						
						$stmt_version = $con->prepare('SELECT * FROM `places` WHERE id = ?;');
						$stmt_version -> bind_param('s', $id);
						$stmt_version -> execute();
						
						$stmt_version_result = $stmt_version->get_result();
						$result_version = $stmt_version_result->num_rows;
						
						while($row_version_data = $stmt_version_result->fetch_assoc()) {
							$version = $row_version_data['version'];
						}
						
						if($result == 1) {
							$total_string = "$id:$version";
						} else {
							$total_string .= "$id:$version,";
						}
					}
					if($result != 1) {
						$total_string = substr($total_string, 0, -1);
					}
					echo $total_string;
				} else {
					echo "none";
				}
			} else if($_GET['insert'] == "-1") {
				$stmt = $con->prepare('DELETE FROM `rcc_requests` WHERE game_id LIKE ?');
				$stmt -> bind_param('s', $_GET['game_id']);
				$stmt -> execute();
			}
		}
		else if(isset($_GET['clear'])) {
			if($_GET['clear'] == "1") {
				$stmt = $con->prepare('DELETE FROM `rcc_status` WHERE sent_at < DATE_SUB(NOW(),INTERVAL 15 SECOND);');
				$stmt -> execute();
				echo "cleared!";
			}
		}
		//id=0&port=25565&msg=1&insert=1&game_id=1
		else if(isset($_GET['id']) && isset($_GET['port']) && isset($_GET['msg']) && isset($_GET['insert']) || isset($_GET['game_id'])) {
			//echo "yay";
			//exit(0);
			if($_GET['insert'] == "1") {
				if(!isset($_GET['game_id'])) {
					$stmt = $con->prepare('INSERT INTO `rcc_status`(`rcc_id`, `content`) VALUES (?,?);');
					$stmt -> bind_param('ss', $_GET['id'], $_GET['msg']);
					$stmt -> execute();
				} else {
					$stmt = $con->prepare('SELECT * FROM `rcc_status` WHERE rcc_id LIKE ?');
					$stmt -> bind_param('s', $_GET['id']);
					$stmt -> execute();
					
					$stmt_result = $stmt->get_result();
					$result = $stmt_result->num_rows;
					if($result == 0) {
						$stmt = $con->prepare('INSERT INTO `rcc_status`(`port`, `rcc_id`, `content`, `game_id`) VALUES (?,?,?,?);');
						$stmt -> bind_param('ssss', $_GET['port'], $_GET['id'], $_GET['msg'], $_GET['game_id']);
						$stmt -> execute();
					} else {
						$stmt = $con->prepare('UPDATE `rcc_status` SET `content` = ? WHERE `rcc_id` = ?;');
						$stmt -> bind_param('ss', $_GET['msg'], $_GET['id']);
						$stmt -> execute();
					}
				}
			} else if ($_GET['insert'] == "0") {
				$stmt = $con->prepare('SELECT * FROM `rcc_status` WHERE rcc_id LIKE ?');
				$stmt -> bind_param('s', $_GET['id']);
				$stmt -> execute();
				
				$stmt_result = $stmt->get_result();
				$result = $stmt_result->num_rows;
				
				while($row_data = $stmt_result->fetch_assoc()) {
					$id = $row_data['rcc_id'];
					$content = $row_data['content'];
					$game_id = $row_data['game_id'];
				}
				
				if($game_id == "-1") {
					echo json_encode([
						'id' => $id,
						'content' => $content,
					]);
				} else {
					echo json_encode([
						'id' => $id,
						'content' => $content,
						'game_id' => $game_id,
					]);
				}
				
			} else if($_GET['insert'] == "-1") {
				$stmt = $con->prepare('DELETE FROM `rcc_status` WHERE rcc_id LIKE ? AND port LIKE ?');
				$stmt -> bind_param('ss', $_GET['id'], $_GET['port']);
				$stmt -> execute();
			}
			echo "shut up";
		}
		
		else if(isset($_GET['remove'])) {
			if(isset($_GET['user_id'])) {
				$user_id = $_GET['user_id'];
				
				$stmt = $con->prepare("DELETE FROM `image_requests` WHERE user_id = ? AND type = 'user'");
				$stmt -> bind_param('s', $user_id);
				$stmt -> execute();
			}
		}
		
		else if(isset($_GET['getimages']) && isset($_GET['type'])) {
			if($_GET['type'] == "users") {
				$stmt = $con->prepare('SELECT * FROM `image_requests` WHERE type = "user"');
				$stmt -> execute();
				
				$stmt_result = $stmt->get_result();
				$result = $stmt_result->num_rows;
				$total_string = "";
				if($result != 0) {
					while($row_data = $stmt_result->fetch_assoc()) {
						$user = $row_data['user_id'];
						//echo $user;
						
						$stmt_color = $con->prepare('SELECT * FROM `users` WHERE id = ?');
						$stmt_color -> bind_param('s', $user);
						$stmt_color -> execute();
						
						$stmt_result_color = $stmt_color->get_result();
						$result_color = $stmt_result_color->num_rows;
						
						while($row_data_color = $stmt_result_color->fetch_assoc()) {
							$colors = $row_data_color['skin_colors'];
						}
						
						$colors = rtrim($colors);
						
						if($result == 1) {
							$total_string = "{\"user\":$user, \"colors\":\"$colors\"}";
						} else {
							$total_string .= "{\"user\":$user, \"colors\":\"$colors\"}, ";
						}
						
					}
					$total_string = rtrim($total_string);
					if($result != 1) {
						$total_string = substr($total_string, 0, -1);
					}
				} else {
					$total_string = "none";
				}
				echo $total_string;
				exit(0);
			}
		}
		else {
			$htmlyes = true;
		}
	}
	else {
		 echo '<pre>'; print_r($_POST); echo '</pre>';

	}
?>
<?php if(isset($htmlyes)) :?>
<?php $title = "API"; include('includes/header.php'); ?>
<div id="Body" style="text-align: center; margin: 10px auto 150px auto; width: 855px; border: black thin solid; padding: 22px; background-color: white;">
	<h3>LambdaAPI</h3>
	<p>does stuff idk</p>
</div>
<?php include('includes/footer.php'); ?>
<?php endif ?>
