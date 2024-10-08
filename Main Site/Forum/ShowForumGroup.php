<?php 
	$extracss = "Forum"; 
	include('../includes/header.php');
	include('../includes/dbcon.php'); 
	if(!isset($_GET['ForumGroupID'])) {
		header('Location: /Forum/index.php');
		exit(0);
	} else {
		if (!is_numeric($_GET['ForumGroupID'])) {
			header('Location: /Forum/index.php');
			exit(0);
		} else {
			$ForumGroupID = $_GET['ForumGroupID'];
		}
	}
	
	//sanitize
	$stmt_groups = $con->prepare('SELECT * FROM forum_parent_groups WHERE parent_id LIKE ?');
	$stmt_groups -> bind_param('s', $ForumGroupID);
	$stmt_groups -> execute();
	
	$stmt_result_groups = $stmt_groups->get_result();
	$result_groups = $stmt_result_groups->num_rows;
	
	while($row = $stmt_result_groups->fetch_assoc()) {
		$group_name = $row['parent_name'];
	}
	
	$stmt = $con->prepare('SELECT * FROM forum_groups WHERE parent_group LIKE ?');
	$stmt -> bind_param('s', $ForumGroupID);
	$stmt -> execute();
	
	$stmt_result = $stmt->get_result();
	$result = $stmt_result->num_rows;
	
	if($result_groups == 0) {
		header('Location: /Forum/index.php');
		exit(0);
	}
?>
<div id="Body">
	<table width="100%" cellspacing="0" cellpadding="0" border="0">
		<tbody>
			<tr><td></td></tr>
			
			<tr valign="bottom">
				<td>
					<table width="100%" height="100%" cellspacing="0" cellpadding="0" border="0">
						<tbody>
							<tr valign="top">
								<!-- left column -->
								<td>&nbsp; &nbsp; &nbsp;</td>
								<!-- center column -->
								<td width="95%" class="CenterColumn">
									<br>
									<span id="NavigationMenu">
										<table width="100%" cellspacing="1" cellpadding="0">
											<tbody>
												<tr>
													<td align="right" valign="middle">
														<a id="NavigationMenu_HomeMenu" class="menuTextLink" href="/Forum/"><img src="/images/forum/icon_mini_home.gif" border="0">Home &nbsp;</a>
														<a id="NavigationMenu_SearchMenu" class="menuTextLink" href="https://web.archive.org/web/20090221141530/http://www.roblox.com/Forum/Search/default.aspx"><img src="/images/forum/icon_mini_search.gif" border="0">Search &nbsp;</a>
														<a id="NavigationMenu_RegisterMenu" class="menuTextLink" href="https://web.archive.org/web/20090221141530/http://www.roblox.com/Forum/User/CreateUser.aspx"><img src="/images/forum/icon_mini_register.gif" border="0">Register &nbsp;</a>
													</td>
												</tr>
											</tbody>
										</table>
									</span>
									<span id="WhereAmI">
										<table cellpadding="0" cellspacing="0" width="100%">
											<tbody>
												<tr>
													<td valign="top" align="left" width="1px"><nobr></nobr></td>
													<td class="popupMenuSink" valign="top" align="left" width="1px">
														<nobr>
															<a class="linkMenuSink" href="/Forum/ShowForumGroup.php?ForumGroupID=1"><?= $group_name; ?></a>
														</nobr>
													</td>
													<td class="popupMenuSink" valign="top" align="left" width="1px"><nobr></nobr></td>
													<td class="popupMenuSink" valign="top" align="left" width="1px"><nobr></nobr></td>
													<td valign="top" align="left" width="*">&nbsp;</td>
												</tr>
											</tbody>
										</table>
										<span></span>
									</span>
									<p></p>
									<table cellpadding="2" cellspacing="1" border="0" width="100%" class="tableBorder">
										<tbody>
											<tr>
												<th class="tableHeaderText" colspan="2" height="20">Forum</th>
												<th class="tableHeaderText" width="50" nowrap="nowrap">&nbsp;&nbsp;Threads&nbsp;&nbsp;</th>
												<th class="tableHeaderText" width="50" nowrap="nowrap">&nbsp;&nbsp;Posts&nbsp;&nbsp;</th>
												<th class="tableHeaderText" width="135" nowrap="nowrap">&nbsp;Last Post&nbsp;</th>
											</tr>
											<tr>
												<td class="forumHeaderBackgroundAlternate" colspan="5" height="20">
													<a class="forumTitle" href="/Forum/ShowForumGroup.php?ForumGroupID=1">LAMBDABLOX</a>
												</td>
											</tr>
											<?php
												while($row = $stmt_result->fetch_assoc()) {
													$id = $row['group_id'];
													$name = $row['group_name'];
													$desc = $row['group_description'];
													
													echo "<tr>\n";
														echo "<td class='forumRow' align='center' valign='top' width='34' nowrap='nowrap'><img src='/images/forum/forum_status.gif' width='34' border='0'></td>\n";
														echo "<td class='forumRow' width='80%'>\n";
															echo "<a class='forumTitle' href='https://web.archive.org/web/20090221141530/http://www.roblox.com/Forum/ShowForum.aspx?ForumID=13'>$name</a>\n";
															echo "<span class='normalTextSmall'><br>$desc</span>\n";
														echo "</td>\n";
														echo "<td class='forumRowHighlight' align='center'><span class='normalTextSmaller'>233,396</span></td>\n";
														echo "<td class='forumRowHighlight' align='center'><span class='normalTextSmaller'>2,272,409</span></td>\n";
														echo "<td class='forumRowHighlight' align='center'>\n";
															echo "<span class='normalTextSmaller'>\n";
																echo "<span><b>Today @ 08:14 AM</b></span>\n";
															echo "</span>\n";
															echo "<br>\n";
															echo "<span class='normalTextSmaller'>by <a href='/User.php?id=0'>nil</a>\n";
																echo "<a href='https://web.archive.org/web/20090221141530/http://www.roblox.com/Forum/ShowPost.aspx?PostID=5795070#5795108'><img border='0' src='/images/forum/icon_mini_topic.gif'></a>\n";
															echo "</span>\n";
														echo "</td>\n";
													echo "</tr>\n";
												}
											?>
											
										</tbody>
									</table>
									<table cellpadding="0" cellspacing="0" width="100%">
										<tbody>
											<tr>
												<td valign="top" align="left" width="1px">
													<nobr>
														<a id="WhereamI_LinkHome" class="linkMenuSink" href="/Forum/">LAMBDABLOX Forum</a>
													</nobr>
												</td>
												<td class="popupMenuSink" valign="top" align="left" width="1px">
													<nobr>
														<span class="normalTextSmallBold">&nbsp;&gt;</span>
														<a class="linkMenuSink" href="/Forum/ShowForumGroup.php?ForumGroupID=1"><?= $group_name?></a>
													</nobr>
												</td>
												<td id="WhereAmI_ForumMenu" class="popupMenuSink" valign="top" align="left" width="1px"><nobr></nobr></td>
												<td id="WhereAmI_PostMenu" class="popupMenuSink" valign="top" align="left" width="1px"><nobr></nobr></td>
												<td valign="top" align="left" width="*">&nbsp;</td>
											</tr>
										</tbody>
									</table>
								</td>
								<td class="CenterColumn">&nbsp;&nbsp;&nbsp;</td>
								<!-- right margin -->
								<td class="RightColumn">&nbsp;&nbsp;&nbsp;</td>
							</tr>
						</tbody>
					</table>
				</td>
			</tr>
		</tbody>
	</table>
</div>

<?php include('../includes/footer.php'); ?>