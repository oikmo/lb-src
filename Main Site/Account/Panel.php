<?php
	include('dbcon.php');
	include('../includes/header.php');
	
	if(isset($_SESSION['auth_user'])) {
		$auth_user_id = $_SESSION['auth_user']['id'];
		
		//sanitize
		$stmt = $con->prepare('SELECT * FROM users WHERE id LIKE ?');
		$stmt -> bind_param('s', $auth_user_id);
		$stmt -> execute();
		
		$stmt_result = $stmt->get_result();
		$result = $stmt_result->num_rows;
		
		$auth_user_badges = "";
		
		if($result == 0) {
			header("Location: /logout.php");
			exit(0);
		}
		
		while($row_data = $stmt_result->fetch_assoc()) {
			$auth_user_badges = $row_data['badges'];
		}
		
		if($auth_user_badges != "0,0,0,0,0,0,0") {
			list($admin, $friendship, $homestead, $bricksmith, $veteran, $inviter, $bc) = explode(",", $auth_user_badges);
		} else {
			$auth_user_badges = "none";
		}
		
		if(isset($admin) && $admin == "1") {
			$stmt = $con->prepare('SELECT * FROM places WHERE pending = 1');
			$stmt -> execute();
			
			$stmt_result = $stmt->get_result();
			$result = $stmt_result->num_rows;
		}
	} else {
		header("Location: /index.php");
		exit(0);
	}
?>
<div id="Body" style="text-align: center; height: 300px; width: 855px; border: black thin solid; padding: 22px; background-color: white;">
	<h3>Account Panel</h3>
	<div style="text-align:center;/*! font-family: Verdana; */font-size: 4pt;">
		<h3 style="font-size: 8pt;"><a href="/Account/ChangePassword.php">Change Password</a></h3>
		<h3 style="font-size: 8pt;"><a href="/Account/Edit.php">Edit</a></h3>
		<h3 style="font-size: 8pt;"><a href="/Friends.php">Friends</a></h3>
		<?php if(isset($admin) && $admin == "1") :?>
		<br>
		<h3>Admin</h3>
		<h3 style="font-size: 8pt;"><a href="/Admin/Approval.php">Games Approval (<?= $result;?>)</a></h3>
		<?php endif ?>
	</div>
</div>
<?php
	include('../includes/footer.php');
?>