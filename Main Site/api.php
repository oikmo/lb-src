<?php
	session_start();
	include('includes/dbcon.php');
	
	if(isset($_POST['register_btn'])) {
		$invite_key = $_POST['invite_key'];
		$username = $_POST['username'];
		$password1 = $_POST['password1'];
		$password2 = $_POST['password2'];
		
		//sanitize to prevent SQL injection
		$stmt_key = $con->prepare('SELECT * FROM invite_keys WHERE invite_key = ?');
		$stmt_key->bind_param('s', $invite_key);
		$stmt_key->execute();
		$stmt_key->store_result();
		
		//get number of rows associated with name
		$keys = $stmt_key->num_rows;
		
		//sanitize to prevent SQL injection
		$stmt = $con->prepare('SELECT * FROM users WHERE username = ?');
		$stmt->bind_param('s', $username);
		$stmt->execute();
		$stmt->store_result();
		
		//get number of rows associated with name
		$usernames = $stmt->num_rows;
		
		$errormsg = "";
		
		$errorcheck = false;
		
		$username = str_replace('\n', '', $username);
		
		if($keys == 0) {
			$errorcheck = true;
			$errormsg .= "Key was not valid!\n";
		}
		
		if(!preg_match('/^[a-zA-Z0-9]*$/', $username)) {
			$errorcheck = true;
			$errormsg .= "Username is not valid!\n";
		}
		
		if($usernames > 0) {
			$errorcheck = true;
			$errormsg .= "Username already exists!\n";
		}
		
		if(strlen($username) > 20) {
			$errorcheck = true;
			$errormsg .= "Username was too long! (20 max)\n";
		}
		if(strlen($username) < 3) {
			$errorcheck = true;
			$errormsg .= "Username was too short (3 min)!\n";
		}
		
		if(empty($username) == true) {
			$errorcheck = true;
			$errormsg .= "Username field was empty!\n";
		}
		
		if(empty($password1) == true) {
			$errorcheck = true;
			$errormsg .= "Password field was empty!\n";
		}
		
		if(strlen($password1) < 7) {
			$errorcheck = true;
			$errormsg .= "Password was too short (7 min)!\n";
		}
		
		if($password1 != $password2) {
			$errorcheck = true;
			$errormsg .= "Password was not equal!\n";
		}

		if($errorcheck == false) {
			//encypts password with default crypt library
			$password = password_hash($password1, PASSWORD_DEFAULT);
			
			//sanitize to prevent SQL injection (inserts user into database)
			$stmt = $con -> prepare('INSERT INTO users (username, password) VALUES (?,?)');
			$stmt -> bind_param('ss', $username, $password);
			
			$id = $con -> insert_id;
			
			if($stmt -> execute()) {
				//$_SESSION['status'] = "Registration successful!'$username'";
				$_SESSION['authenticated'] = TRUE;
				$_SESSION['auth_user'] = [
					'username' => $username,
					'id' => $id,
				];
				$stmt = $con->prepare('DELETE FROM invite_keys WHERE invite_key = ?;');
				$stmt -> bind_param('s', $invite_key);
				$stmt -> execute();
				
				header("Location: /index.php");
			} else {
				$_SESSION['status'] = "Registration failed.";
				header("Location: /Account/Register.php");
			}
		} else {
			$_SESSION['status'] = $errormsg;
			header("Location: /Account/Register.php");
		}
	}

	else if(isset($_POST['login_btn']) || isset($_POST['login_home_btn'])) {
		
		$username = $_POST['username'];
		$password = $_POST['password'];
		
		if(!empty(trim($username)) && !empty(trim($password))) {
			//sanitize
			$stmt = $con->prepare('SELECT * FROM users WHERE username LIKE ?');
			$stmt -> bind_param('s', $username);
			$stmt -> execute();
			
			
			$stmt_result = $stmt->get_result();
			$result = $stmt_result->num_rows;
			
			$name = "";
			$pw = "";
			$id = -1;
			$colors = "";
			
			$row_data = null;
			while($row_data = $stmt_result->fetch_assoc()) {
				$name = $row_data['username'];
				$pw = $row_data['password'];
				$id = $row_data['id'];
				$colors = $row_data['skin_colors'];
			}
			
			if($result > 0 && password_verify($password, $pw) == true) {
				$_SESSION['authenticated'] = TRUE;
				$_SESSION['auth_user'] = [
					'username' => $name,
					'id' => $id,
				];
				
				$_SESSION['auth_user']['character']['colors'] = $colors;
				$status = "1";
				$stmt = $con->prepare('UPDATE users SET status = ? WHERE username LIKE ?');
				$stmt -> bind_param('ss', $status, $_SESSION['auth_user']['username']);
				$stmt -> execute();
				
				header("Location: /index.php");
				exit(0);
			} else {
				$higher = $result > 0;
				$pw_verify = password_verify($password, $pw) == true;
				$_SESSION['status'] = "Invalid username or password (or user doesn't exist)!";
				header("Location: /Account/Login.php");
				exit(0);
			}
		} else {
			$_SESSION['status'] = "All fields are mandatory :P";
			header("Location: /Account/Login.php");
			exit(0);
		}
		
	}
	
	else if(isset($_POST['edit_btn'])) {
		$blurb = $_POST['blurb'];
		
		$errormsg = "Error! ";
		
		$username = $_SESSION['auth_user']['username'];
		$id = $_SESSION['auth_user']['id'];
		$colors = $_POST['colorPayload'];
		
		$max_blurb = 512;
		
		$blurb = str_replace("<","&lt", $blurb); 
		$blurb = str_replace(">","&gt", $blurb); 
		
		if(strlen($blurb) <= $max_blurb) {
			if(!(strlen($blurb) <= 0)) {
				$stmt = $con->prepare('UPDATE users SET blurb = ? WHERE username LIKE ?');
				$stmt -> bind_param('ss', $blurb, $username);
				$stmt -> execute();
			}
			
			$stmt = $con->prepare('UPDATE users SET skin_colors = ? WHERE username LIKE ?');
			$stmt -> bind_param('ss', $colors, $username);
			$stmt -> execute();
			
			if($_SESSION['auth_user']['character']['colors'] != $colors) {
				$_SESSION['auth_user']['character']['colors'] = $colors;
				$stmt = $con->prepare('INSERT INTO `image_requests`(`user_id`) VALUES (?);');
				$stmt -> bind_param('s', $id);
				$stmt -> execute();
			}
			
			
			
			header("Location: /User.php?id=$id");
			exit(0);
		} else {
			if(strlen($blurb) > $max_blurb) {
				$errormsg .= "Blurb was wayy too long! ($max_blurb chars max!)";
			}
			
			header("Location: /Account/Edit.php");
			exit(0);
		}
		
		
	}
	
	else if(isset($_POST['upload_place_btn'])) {
		$name = $_POST['name'];
		$description = $_POST['description'];
		
		if(strlen($description) > 0 && strlen($description) <= 255 && strlen($name) > 0 && strlen($name) <= 64) {
			
			$errorcheck = false;
			
			$errormsg = "";
			
			if($_FILES['cover']['tmp_name'] != null) {
				$image = getimagesize($_FILES['cover']['tmp_name']);
				if ($image == null) {
					$errormsg .= "That was not a valid image!";
					$errorcheck = true;
				}
			} else {
				$errorcheck = true;
			}
			
			if($_FILES['place']['tmp_name'] != null) {
				$content = file_get_contents($_FILES['place']['tmp_name']);
				
				if($content == null) {
					$_SESSION['status'] = "Place file was not a valid file!";
					header("Location: Upload.php");
					exit(0);
				} else {
					if(substr($content, 0, 7) != "<roblox" && !str_contains(substr($content, 0, 7), "<") || substr($content, -9) != "</roblox>") {
						$_SESSION['status'] = "Place file was not a valid XML!";
						header("Location: Upload.php");
						exit(0);
					}
				}
			} else {
				$errormsg .= "Place was not real...";
				$errorcheck = true;
			}
			
			if(!$errorcheck) {
				$version = $_POST['gameversion'];
				$description = str_replace("<","&lt", $description); 
				$description = str_replace(">","&gt", $description); 
				$name = str_replace("<","&lt", $name); 
				$name = str_replace(">","&gt", $name); 
				
				$stmt = $con->prepare('INSERT INTO places (creator, name, description, version, pending) VALUES (?,?,?,?, 1)');
				$stmt -> bind_param('ssss', $_SESSION['auth_user']['id'], $name, $description, $version);
				$stmt -> execute();
				$id = $stmt->insert_id;
				
				$stmt_result = $stmt->get_result();
				$result = $stmt_result->num_rows;
				
				$root_path = '/var/www/asset.lambda.cam/places'; 
				echo "$root_path/$id/";
				if (!is_dir("$root_path/$id/")) {
					mkdir("$root_path/$id/", 0777, true);
					echo "not dir";
				} else {
					echo "dir";
				}
				
				$upload_cover_path = $root_path."/$id/cover";
				list($width, $height, $type, $attr) = $image; 
				move_uploaded_file($_FILES['cover']['tmp_name'],$upload_cover_path);	
				
				$upload_place_path = $root_path."/$id/place.rbxl";
				
				if($content != null) {
					if(substr($content, 0, 7) == "<roblox" && substr($content, -9) == "</roblox>") {
						move_uploaded_file($_FILES['place']['tmp_name'],$upload_place_path);
					} else {
						if(substr($content, 0, 7) != "<roblox" && !str_contains(substr($content, 0, 7), "<") || substr($content, -9) != "</roblox>") {							
							$_SESSION['status'] = "Place file was not a valid file!";
							header("Location: Upload.php");
							exit(0);
						}
					}
				} 
				
				$_SESSION['status'] = "Uploaded successfully!";
				header("Location: Games.php");
				exit(0);
			} else {
				$_SESSION['status'] = $errormsg;
				header("Location: Upload.php");
				exit(0);
			}
		} else {
			$errortext = "Error! : ";
			
			if(isset($description)) {
				if(strlen($description) == 0) {
					$errortext .= "Description was empty!";
				}
				
				if(strlen($description) > 255) {
					$errortext .= "Description was too long! (255 max!)";
				}
			}	
			
			if(isset($name)) {
				if(strlen($name) == 0) {
					$errortext .= "Name was empty!";
				}
				
				if(strlen($name) > 64) {
					$errortext .= "Name was too long! (64 max!)";
				}
			}
			
			
			$_SESSION['status'] = $errortext;
			header("Location: Upload.php");
			exit(0);
		}
	}
	
	else if(isset($_POST['update_place_btn'])) {
		$description = $_POST['description'];
		$id = $_POST['id'];
		
		$pending = 1;
		if($_FILES['cover']['tmp_name'] == null) {
			$pending = 0;
		}
		
		if(strlen($description) > 0 && strlen($description) <= 255) {
			
			$errorcheck = false;
			
			$errormsg = "";
			
			if($_FILES['cover']['tmp_name'] != null) {
				$image = getimagesize($_FILES['cover']['tmp_name']);
				if ($image == null) {
					$errormsg .= "That was not a valid image!";
					$errorcheck = true;
				}
			}
			
			if(!$errorcheck) {
				$version = $_POST['gameversion'];
				$description = str_replace("<","&lt", $description); 
				$description = str_replace(">","&gt", $description); 
				
				$stmt = $con->prepare('UPDATE `places` SET `description`= ?,`pending`= ?, `version` = ? WHERE id = ?');
				$stmt -> bind_param('ssss', $description, $pending, $version, $id);
				$stmt -> execute();
				$id = $stmt->insert_id;
				
				$stmt_result = $stmt->get_result();
				$result = $stmt_result->num_rows;
				
				$root_path = '/var/www/asset.lambda.cam/places'; 
				echo "$root_path/$id/";
				if (!is_dir("$root_path/$id/")) {
					mkdir("$root_path/$id/", 0777, true);
					echo "not dir";
				} else {
					echo "dir";
				}
				
				if($_FILES['cover']['tmp_name'] != null) {
					$upload_cover_path = $root_path."/$id/cover";
					list($width, $height, $type, $attr) = $image; 
					move_uploaded_file($_FILES['cover']['tmp_name'],$upload_cover_path);	
				}
				
				$_SESSION['status'] = "Uploaded successfully!";
				header("Location: /Place/View.php?id=$id");
				exit(0);
			} else {
				$_SESSION['status'] = $errormsg;
				header("Location: /Place/Edit.php?id=$id");
				exit(0);
			}
		} else {
			$errortext = "Error! : ";
			
			if(isset($description)) {
				if(strlen($description) == 0) {
					$errortext .= "Description was empty!";
				}
				
				if(strlen($description) > 255) {
					$errortext .= "Description was too long! (255 max!)";
				}
			}
			
			
			$_SESSION['status'] = $errortext;
			header("Location: /Place/Edit.php?id=$id");
			exit(0);
		}
	}
	
	else if(isset($_POST['id']) && isset($_POST['approve'])) {
		$id = $_POST['id'];
		$approve = $_POST['approve'];
		
		$pending = 0;
		
		if($approve == 0) {
			$pending = -1;
			unlink("./places/$id/cover");
		}
		
		$stmt = $con->prepare('UPDATE places SET pending = ? WHERE id LIKE ?');
		$stmt -> bind_param('ss', $pending, $id);
		$stmt -> execute();
		
		header("Location: /Admin/Approval.php");
		exit();
	}
	
	else if(isset($_POST['friend']) || isset($_POST['unfriend'])) {
		if(isset($_SESSION['auth_user'])) {
			$auth_user_id = $_SESSION['auth_user']['id'];
			
			if(isset($_POST['friend'])) {
				$player_id = $_POST['friend'];
				
				$return_link = "/User.php?id=$player_id";
				
				if(isset($_POST['return'])) {
					$return = $_POST['return'];
					$return_link = "/Friends.php?id=$return&tab=1";
				}
				
				//sanitize
				$stmt = $con->prepare('SELECT * FROM friends WHERE (user1 = ? AND user2 = ?) OR (user2 = ? AND user1 = ?)');
				$stmt -> bind_param('ssss', $auth_user_id, $player_id, $auth_user_id, $player_id);
				$stmt -> execute();
				
				$stmt_result = $stmt->get_result();
				$result = $stmt_result->num_rows;
				
				$toaccept = "0";
				$accepted = "1";
				
				if($result == 0) {
					$stmt = $con->prepare('INSERT INTO friends (user1, user2, status) VALUES (?,?,?)');
					$stmt -> bind_param('sss', $auth_user_id, $player_id, $toaccept);
					$stmt -> execute();
					
					header("Location: $return_link");
					exit(0);
				} else {
					// accept request if auth_user_id is user2
					
					$status = "";
					$user2 = "";
					
					while($row_data = $stmt_result->fetch_assoc()) {
						$status = $row_data['result'];
						$user2 = $row_data['user2'];
					}
					
					if($user2 == $auth_user_id && $status == 0) {
						
						$stmt = $con->prepare('UPDATE friends SET status = ? WHERE user1 = ? and user2 = ?');
						$stmt -> bind_param('sss', $accepted, $player_id, $auth_user_id);
						$stmt -> execute();
						header("Location: $return_link");
						exit(0);
					} else {
						header("Location: $return_link");
						exit(0);
					}
				}
			}
			
			else if(isset($_POST['unfriend'])) {
				$player_id = $_POST['unfriend'];
				
				$return_link = "/User.php?id=$player_id";
				
				if(isset($_POST['return'])) {
					$return = $_POST['return'];
					$return_link = "/Friends.php?id=$return&tab=1";
				}
				
				//sanitize
				$stmt = $con->prepare('SELECT * FROM friends WHERE (user1 = ? AND user2 = ?) OR (user2 = ? AND user1 = ?)');
				$stmt -> bind_param('ssss', $auth_user_id, $player_id, $auth_user_id, $player_id);
				$stmt -> execute();
				
				$stmt_result = $stmt->get_result();
				$result = $stmt_result->num_rows;
				
				if($result != 0) {
					
					while($row_data = $stmt_result->fetch_assoc()) {
						$status = $row_data['status'];
						$user1 = $row_data['user1'];
						$user2 = $row_data['user2'];
					}
					
					if($status == 1) {
						// remove friend
						$valid_status = 1;
						
						echo "should be removing friend...";
						
						//sanitize
						$stmt = $con->prepare('DELETE FROM friends WHERE user2 = ? AND user1 = ? AND status = ?;');
						$stmt -> bind_param('sss', $auth_user_id, $player_id, $valid_status);
						$stmt -> execute();
					} else if($user1 == $auth_user_id && $status == 0) {
						// remove friend
						$valid_status = 0;
						
						echo "should be removing request...";
						
						//sanitize
						$stmt = $con->prepare('DELETE FROM friends WHERE user2 = ? AND user1 = ? AND status = ?;');
						$stmt -> bind_param('sss', $player_id, $auth_user_id, $valid_status);
						$stmt -> execute();
					}
					
					header("Location: $return_link");
					exit(0);
				} else {
					// reject and ignore
					echo "You aren't friends with this person?";
				}
			}
			
			else {
				$htmlyes = true;
			}
		} 
		else {
			$htmlyes = true;
		}
	}
	
	else if(isset($_POST['place_id']) && isset($_POST['create_request'])) {
		$stmt = $con->prepare('SELECT * FROM `rcc_status` WHERE game_id = ?');
		$stmt -> bind_param('s', $_POST['place_id']);
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
			$stmt -> bind_param('s', $_POST['place_id']);
			$stmt -> execute();
		}
		$ibeez = $_POST['place_id'];
		header("Location: /Place/View.php?id=$ibeez");
		exit(0);
	}
	
	else if(isset($_POST['port']) && isset($_POST['place_id']) && isset($_POST['join_place'])) {
		$stmt = $con->prepare('DELETE FROM `rcc_status` WHERE sent_at < DATE_SUB(NOW(),INTERVAL 15 SECOND);');
		$stmt -> execute();
		
		$stmt = $con->prepare('SELECT * FROM `places` WHERE id = ?');
		$stmt -> bind_param('s', $_POST['place_id']);
		$stmt -> execute();
		
		$stmt_result = $stmt->get_result();
		$result = $stmt_result->num_rows;
		
		while($row_data = $stmt_result->fetch_assoc()) {
			$version = $row_data['version'];
		}
		
		if($_SESSION['authenticated'] && $result != 0) {
			$port = $_POST['port'];
			$name = $_SESSION['auth_user']['username'];
			$colors = str_replace(", ", ":", $_SESSION['auth_user']['character']['colors']);
			$base64 = base64_encode("ip:86.20.118.158,port:$port,name:$name,version:$version,colors:$colors");
			header("Location: lambdablox://$base64");
		} else {
			header("Location: /index.php");
			exit(0);
		}
	}
	
	else if(isset($_POST['change_pass_btn'])) {
		$password = $_POST['original_password'];
		
		$newpassword1 = $_POST['password1'];
		$newpassword2 = $_POST['password2'];
		
		$id = $_SESSION['auth_user']['id'];
		
		$stmt = $con->prepare('SELECT * FROM users WHERE id LIKE ?');
		$stmt -> bind_param('s', $id);
		$stmt -> execute();
		
		$stmt_result = $stmt->get_result();
		$result = 0;
		$result = $stmt_result->num_rows;
		
		while($row_data = $stmt_result->fetch_assoc()) {
			$pw = $row_data['password'];
		}
		
		$errormsg = "";
		
		$errorcheck = false;
		
		if(empty($newpassword1) == true) {
			$errorcheck = true;
			$errormsg .= "New Password field was empty!\n";
		}
		
		if(empty($newpassword2) == true) {
			$errorcheck = true;
			$errormsg .= "New Password field was empty!\n";
		}
		
		if(strlen($newpassword1) < 7) {
			$errorcheck = true;
			$errormsg .= "New Password was too short (7 min)!\n";
		}
		
		if(strlen($newpassword2) < 7) {
			$errorcheck = true;
			$errormsg .= "New Password was too short (7 min)!\n";
		}
		
		if($newpassword1 != $newpassword2) {
			$errorcheck = true;
			$errormsg .= "New Password was not equal!\n";
		}
		
		if(password_verify($password, $pw) == false) {
			$errorcheck = true;
			$errormsg .= "Password was not correct!\n";
		}
		
		if($errorcheck == false) {
			$newpassword = password_hash($newpassword1, PASSWORD_DEFAULT);
			
			//sanitize to prevent SQL injection (inserts user into database)
			$stmt = $con -> prepare('UPDATE users SET password = ? WHERE id = ?');
			$stmt -> bind_param('ss', $newpassword, $id);
			$stmt -> execute();
			$_SESSION['status'] = "Updated password successfully!";
		} else {
			$_SESSION['status'] = $errormsg;	
		}
		header("Location: /Account/ChangePassword.php");
	}
	
	else if(isset($_GET['username']) || isset($_GET['password'])) {
		$username = $_GET['username'];
		$password = $_GET['password'];
		
		if(isset($_GET['username']) && isset($_GET['password'])) {
			if(!empty(trim($username)) && !empty(trim($password))) {
				
				$stmt = $con->prepare('SELECT * FROM users WHERE username LIKE ?');
				$stmt -> bind_param('s', $username);
				$stmt -> execute();
				
				$stmt_result = $stmt->get_result();
				$result = 0;
				$result = $stmt_result->num_rows;
				
				$name = "";
				$pw = "";
				$email = "";
				
				while($row_data = $stmt_result->fetch_assoc()) {
					$name = $row_data['username'];
					$pw = $row_data['password'];
					$id = $row_data['id'];
					$colors = $row_data['skin_colors'];
				}
				
				if($result > 0 && password_verify($password, $pw) == true) {
					echo json_encode([
						'result' => true,
						'reason' => "Success!",
						'username' => $name,
						'skin_colors' => $colors,
					]);
					exit(0);
				} else {
					echo json_encode([
						'result' => false,
						'reason' => "Invalid username or password (or user doesn't exist)!",
						'username' => null,
					]);
					exit(0);
				}
			} else {
				echo json_encode([
					'result' => false,
					'reason' => "All fields are mandatory :P",
					'username' => null,
				]);
				exit(0);
			}
		} else {
			echo json_encode([
				'result' => false,
				'reason' => "All fields are mandatory :P",
				'username' => null,
			]);
			exit(0);
		}
	}
	
	else {
		$htmlyes = true;
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
