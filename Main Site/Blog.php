<?php
	include('includes/header.php');
?>
<div id="Body">
	<div class="blog" id="blog-1" style="display: inline-block;padding: 10px;">
		<?php 
			if ($handle = opendir('./blog/images/')) {
				while (false !== ($entry = readdir($handle))) {
					if ($entry != "." && $entry != "..") {
						echo "<a href='/blog/images/$entry' target='_blank'><img class='blogimg' src='/blog/images/$entry'></a>\n";
					}
				}
				closedir($handle);
			}
		?>
	</div>
	
	<div id="ctl00_cphRoblox_ie6_peekaboo" style="clear: both"></div>
</div>
<?php
	include('includes/footer.php');
?>