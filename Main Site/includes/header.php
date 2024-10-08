<?php 
	if(!isset($_loc) && session_status() !== PHP_SESSION_ACTIVE) {
		session_start();
		unset($_SESSION['status']);
	}
	$servers = true;
	$header = "LambdaBlox";
	
	if(isset($title)) {
		$header = "$title - LambdaBlox";
	}
	
	include('dbcon.php');
	include('splashes.php');
	
	if($servers == false) {
		$stmt = $con->prepare('SELECT * FROM rcc_requests');
		$stmt -> execute();
		
		$stmt_result = $stmt->get_result();
		$result = $stmt_result->num_rows;
		
		if($result != 0) {
			$stmt = $con->prepare('DELETE FROM rcc_requests WHERE 1');
			$stmt -> execute();
		}
	}
	
	if(isset($_SESSION['auth_user'])) {
		if($_SESSION['auth_user']['id'] == "0") {
			//sanitize
			$stmt = $con->prepare('SELECT * FROM users WHERE username LIKE ?');
			$stmt -> bind_param('s', $_SESSION['auth_user']['username']);
			$stmt -> execute();
			
			$stmt_result = $stmt->get_result();
			$result = $stmt_result->num_rows;
			$auth_user_id = -1;
			
			$row_data = null;
			while($row_data = $stmt_result->fetch_assoc()) {
				$auth_user_id = $row_data['id'];
			}
			
			$_SESSION['auth_user']['id'] = $auth_user_id;
			
			if($result == "0") {
				header('Location: /logout.php');
			}
		}
		
		$auth_user_id = $_SESSION['auth_user']['id'];
		
		//sanitize
		$stmt = $con->prepare('SELECT * FROM users WHERE id LIKE ?');
		$stmt -> bind_param('s', $auth_user_id);
		$stmt -> execute();
		
		$stmt_result = $stmt->get_result();
		$result = $stmt_result->num_rows;
		
		$auth_user_badges = "";
		
		if($result == 0) {
			header("Location: /error/404.php");
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
		
		$stmt = $con->prepare('SELECT * FROM friends WHERE user2 = ? AND status = 0');
		$stmt -> bind_param('s', $auth_user_id);
		$stmt -> execute();
		
		$stmt_result = $stmt->get_result();
		$friends_result = $stmt_result->num_rows;
		
		if(isset($admin) && $admin == "1") {
			$stmt = $con->prepare('SELECT * FROM places WHERE pending = 1');
			$stmt -> execute();
			
			$stmt_result = $stmt->get_result();
			$admin_result = $stmt_result->num_rows;
		}
	} else {
		session_destroy();
	}
	
	$stmt = $con->prepare("DELETE FROM rcc_status WHERE content LIKE '%kill_time:0%';");
	$stmt -> execute();
	
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" id="www-roblox-com">
	<head>
		<title><?= $header; ?></title>
		<link rel="stylesheet" type="text/css" href="/css/AllCSS.css">
		<?php if(isset($extracss)) :?>
			<link rel="stylesheet" type="text/css" href="/css/<?=$extracss?>.css">
		<?php endif ?>
		<link rel="icon" type="image/png" href="/images/favicon.png" />
		<meta http-equiv="Content-Language" content="en-gb">
		<meta name="author" content="Oikmo">
		<?php if(isset($meta_description)) :?>
			<meta name="description" content="<?= $meta_description ?>
			
			Basically kind of a big deal! Do you know who you're talkin' to?">
		<?php endif ?>
		<?php if(!isset($meta_description)): ?>
		<meta name="description" content="Basically kind of a big deal! Do you know who you're talkin' to?">
		<?php endif ?>
		<?php if(isset($meta_image)) :?>
			<meta property="og:image" content="<?= $meta_image; ?>" >
		<?php endif ?>
	</head>
	<body>
		<div name="aspnetForm" id="aspnetForm">
			<div id="MasterContainer">
				<!--<div id="AdvertisingLeaderboard">
					<iframe id="ctl00_cphBannerAd_ForumsBanner_AsyncAdIFrame" scrolling="no" frameborder="0" allowtransparency="true" src="" width="728" height="90" data-ruffle-polyfilled=""></iframe>
				</div>-->
				<div id="Container">
					<div id="Header">
					<div id="Banner">
						<div id="Options">
							<div id="Authentication">
								<?php if(!isset($_SESSION['authenticated'])) :?>
									<span><a href='/Account/Login.php'>Login</a></span>  
								<?php endif ?>
								<?php if(isset($_SESSION['authenticated'])): ?>
									<span>
									Logged in as <a href='/Account/Panel.php'><?= $_SESSION['auth_user']['username']; ?></a>
									<?php if($friends_result != 0): ?>
									<a href="/Friends.php?tab=1">(<?= $friends_result; ?>)</a>
									<?php endif ?>
									<?php if($admin_result != 0): ?>
									 <a href="/Admin/Approval.php">!!!</a>
									<?php endif ?>
									</span> | 
									<a href="/Account/Logout.php" class="loginpanel">Logout</a>
								<?php endif ?>
							</div>
							<div id="Settings"></div>
						</div>
						<div id="Logo">
							<a id="ctl00_rbxImage_Logo" title="Home" href="/" style="display:inline-block;cursor:pointer;">
								<img src="/images/logo.png" width="178px" height="59px" border="0" alt="Home" >
							</a>
						</div>
						<div id="Alerts">
							<table style="width:100%;height:100%">
								<tbody>
									<tr>
										<td valign="middle">
											<?php if(!isset($_SESSION['authenticated'])) :?>
											<a class="SignUpAndPlay" text="Sign-up and Play!" href="/Account/Login.php" style="display:inline-block;cursor:pointer;"><img src="/images/Holiday3Button.png" border="0"></a>
											<?php endif ?>
											<?php if(!isset($_SESSION['authenticated'])) :?>
											<!--<div style="background-color: white;height: 50px;border: solid 1px black;"></div>-->
											<?php endif ?>
										</td>
									</tr>	
								</tbody>
							</table>
						</div>
					</div>
					<div class="Navigation">
						
						<?php if(!isset($_SESSION['authenticated'])) :?>
							<span><a class="MenuItem" href='/Account/Login.php'>My LAMBDA</a></span>
						<?php endif ?>
						
						<?php if(isset($_SESSION['authenticated'])) : ?>
							<span><a class="MenuItem" href='/User.php?id=<?= $_SESSION['auth_user']['id']; ?>'>My LAMBDA</a></span>
						<?php endif ?>
						
						<span class="Separator">&nbsp;|&nbsp;</span>
						<span><a class="MenuItem" href="/Games.php">Games</a></span>
						<span class="Separator">&nbsp;|&nbsp;</span>
						<span><a class="MenuItem" href="/Catalog.aspx">Catalog</a></span>
						<span class="Separator">&nbsp;|&nbsp;</span>
						<span><a class="MenuItem" href="/Browse.php">People</a></span>
						<span class="Separator">&nbsp;|&nbsp;</span>
						<span><a class="MenuItem" href="/Forum/">Forum</a></span>
						<span class="Separator">&nbsp;|&nbsp;</span>
						<span><a class="MenuItem" href="/Blog.php" target="_blank">Blog</a></span>
						<span class="Separator">&nbsp;|&nbsp;</span>
						<span><a class="MenuItem" href="/Downloads.php">Downloads</a></span>
						<span class="Separator">&nbsp;|&nbsp;</span>
						<span><a class="MenuItem" href="/Help/Builderman.aspx">Help</a></span>
					</div>
					<div class="Announcement">
						<span class="Separator"><?= $splashes[array_rand($splashes)]; ?></span>
					</div>
					<?php if($servers == false): ?>
					<div class="Announcement" style="background-color: red;">
						<span class="Separator">Game Servers are down!</span>
					</div>
					<?php endif ?>
				</div>