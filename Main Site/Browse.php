<?php
	include('includes/dbcon.php');
	include('includes/header.php');
	
	$getpage = $_GET["page"];
	
	if(isset($_POST['search-btn'])) {
		$user = $_POST['search'];
	} else {
		$user = "*";
	}
	
	if(!isset($_GET["page"])) {
		$getpage = 1;
	}
	
	$page = $getpage - 1;
	
	if($page < 0) {
		header('Location:/Browse.php?page=1');
	}
	
	$start = 10 + 10 * ($page - 1);
	$rows = 10;
	
	$user = str_replace("*","%",$user);
	
	if(isset($user) && strlen($user) != 0) {
		$stmt = $con->prepare('SELECT * FROM users WHERE username LIKE ? LIMIT ?, ?');
		$stmt->bind_param('sss', $user, $start, $rows);
		$stmt->execute();
	} else {
		$stmt = $con->prepare('SELECT * FROM users LIMIT ?, ?');
		$stmt->bind_param('ss', $start, $rows);
		$stmt->execute();
	}
	
	$result = $stmt->get_result();
	$num_rows = $result->num_rows;
	
	if($num_rows == "0") {
		$prev_page = ($page-1)+1;
		if($prev_page >= 1) {
			$page_url = "/Browse.php?page=$prev_page";
			header('Location:'.$page_url);
		}
	}
	
	$total_query = mysqli_query($con, "SELECT * FROM users");
	$total_rows = mysqli_num_rows($total_query);
	$total_pages = (int)($total_rows / 10)+1;
	
	$start_checklast = 10 + 10 * ($total_pages - 2);
	$rows_checklast = 10;
	
	$stmt_checklast = $con->prepare('SELECT * FROM users LIMIT ?, ?');
	$stmt_checklast->bind_param('ss', $start_checklast, $rows_checklast);
	$stmt_checklast->execute();
	
	$result_checklast = $stmt_checklast->get_result();
	$num_rows_checklast = $result_checklast->num_rows;
	
	if($num_rows_checklast == 0) {
		$total_pages = $total_pages - 1;
	}
?>
<div id="Body">
	<div id="ContainerPanel">
		<div id="BrowseContainer" style="font-family: Verdana, Sans-Serif; text-align: center;">
			<input name="ctl00$cphRoblox$FormSubmitWithoutOnClickEventWorkaround" type="text" value="http://aspnet.4guysfromrolla.com/articles/060805-1.aspx" id="ctl00_cphRoblox_FormSubmitWithoutOnClickEventWorkaround" style="visibility:hidden;display:none;">
			<form name="input" action="/Browse.php" method="POST">
				<div id="SearchBar" class="SearchBar">
					<span class="SearchBox"><input name="search" type="text" maxlength="100" class="TextBox"></span>
					<span class="SearchButton"><input type="submit" name="search-btn" value="Search" id="ctl00_cphRoblox_SearchButton"></span>
					</span> 
				</div>
			</form>
			<div style="display: inline-block;">
				<table class="Grid" style="width:900px" cellspacing="0" cellpadding="4" border="0" id="ctl00_cphRoblox_gvUsersBrowsed">
					<tbody>
						<tr class="GridHeader">
							<th scope="col" style="width:100px;">Avatar</th>
							<th scope="col">Name</th>
							<th scope="col">Status</th>
							<th scope="col">Last Online</th>
						</tr>
						<?php
							while($row = $result->fetch_assoc()) {
								echo '<tr class="GridItem">';
								$id = $row['id'];
								$username = $row['username'];
								$last_online = $row['last_online'];
								$blurb = $row['blurb'];
								$blurb = str_replace("<","&lt", $blurb); 
								$blurb = str_replace(">","&gt", $blurb); 
								$username = str_replace("<","&lt", $username); 
								$username = str_replace(">","&gt", $username); 
								
								$status = "Offline";
								if($row['status'] == 1) {
									$status = "Online";
								}
								echo "<td>";
								echo "<a title='$username' href='/User.php?id=$id' style='display:inline-block;cursor:pointer;'><img src='https://asset.lambda.cam/user?id=$id' width='36px' height='48px' border='0' alt='$username'></a>";
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
								echo "</tr>";
							}
						?>
						<tr class="GridPager">
							<td colspan="4">
								<table border="0">
									<tbody>
										<tr>
										<?php 
										for ($i = 1; $i <= $total_pages; $i++) {
											
											if($i == ($page+1)) {
												echo "<td><span>$i</span></td>";
											} else {
												echo "<td><a href='/Browse.php?page=$i'>$i</a></td>";
											}
											
										}
										?>
										</tr>
									</tbody>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
				
			</div>
		</div>
	</div>
</div>
<?php
	include('includes/footer.php');
?>