<?php
	$_loc = "Edit";
	
	session_start();
	
	if(!isset($_SESSION['authenticated'])) {
		$_SESSION['status'] = "not authenticated";
		header('Location: /index.php');
	}
	
	if(!isset($_SESSION['auth_user']['character']['colors'])) {
		$_SESSION['auth_user']['character']['colors'] = "24, 106, 26, 26, 199, 199";
	}
	
	include('../includes/dbcon.php');
	
	//sanitize
	$stmt = $con->prepare('SELECT * FROM users WHERE id LIKE ?');
	$stmt -> bind_param('s', $_SESSION['auth_user']['id']);
	$stmt -> execute();
	
	$stmt_result = $stmt->get_result();
	$result = $stmt_result->num_rows;
	
	if($result == 0) {
		header('Location: /index.php');
	}
	
	while($row_data = $stmt_result->fetch_assoc()) {
		$blurb = $row_data['blurb'];
	}
	
	include('../includes/header.php');
?>
<div id="Body">
	<div id="FrameLogin" style="margin: 10px auto 150px auto; border: black thin solid; padding: 22px; background-color: white;">
		<div id="PaneLogin">
			<a href="/Account/Panel.php" style="text-align: right;width: 100%;display: block;margin-bottom: -15px;">[ Back to panel ]</a>
			<h3 style="width: 200px;">Editing for <?= $_SESSION['auth_user']['username']?></h3>
			<?php
				if(isset($_SESSION['status'])) {
					echo "<h4 style='color:red;'>".$_SESSION['status']."</h4>";
					unset($_SESSION['status']);
				}
			?>
			<form name="input" action="/api.php" method="POST" style="text-align: left;">
				<div class="SideFlex">
					<div class="ColorPallete">
						<div class="Colors">
							<h3>Color picker!</h3>
							<p class="tip">Pick a part and choose a color for that part!</p>
						</div>
						<div class="ColorDisplay">
							<button type="button" onclick="changeColor('White');" style="background-color:#f2f3f2;" class="colorButton"></button>
							<button type="button" onclick="changeColor('LightStoneGrey');" style="background-color:#e5e4de;" class="colorButton"></button>
							<button type="button" onclick="changeColor('MediumStoneGrey');" style="background-color:#a3a2a4;" class="colorButton"></button>
							<button type="button" onclick="changeColor('DarkStoneGrey');" style="background-color:#635f61;" class="colorButton"></button>
							<button type="button" onclick="changeColor('Black');" style="background-color:#1b2a34;" class="colorButton"></button>
							<button type="button" onclick="changeColor('BrightRed');" style="background-color:#c4281b;" class="colorButton"></button>
							<button type="button" onclick="changeColor('BrightYellow');" style="background-color:#f5cd2f;" class="colorButton"></button>
							<button type="button" onclick="changeColor('CoolYellow');" style="background-color:#fdea8c;" class="colorButton"></button>
							<button type="button" onclick="changeColor('BrightBlue');" style="background-color:#0d69ab;" class="colorButton"></button>
							<button type="button" onclick="changeColor('BrightBluishGreen');" style="background-color:#008f9b;" class="colorButton"></button>
							<button type="button" onclick="changeColor('MediumBlue');" style="background-color:#6e99c9;" class="colorButton"></button>
							<button type="button" onclick="changeColor('PastelBlue');" style="background-color:#80bbdb;" class="colorButton"></button>
							<button type="button" onclick="changeColor('LightBlue');" style="background-color:#b4d2e3;" class="colorButton"></button>
							<button type="button" onclick="changeColor('SandBlue');" style="background-color:#74869c;" class="colorButton"></button>
							<button type="button" onclick="changeColor('BrightOrange');" style="background-color:#da8540;" class="colorButton"></button>
							<button type="button" onclick="changeColor('BrYellowishOrange');" style="background-color:#e29b3f;" class="colorButton"></button>
							<button type="button" onclick="changeColor('EarthGreen');" style="background-color:#27462c;" class="colorButton"></button>
							<button type="button" onclick="changeColor('DarkGreen');" style="background-color:#287f46;" class="colorButton"></button>
							<button type="button" onclick="changeColor('BrightGreen');" style="background-color:#4b974a;" class="colorButton"></button>
							<button type="button" onclick="changeColor('BrYellowishGreen');" style="background-color:#a4bd46;" class="colorButton"></button>
							<button type="button" onclick="changeColor('MediumGreen');" style="background-color:#a1c48b;" class="colorButton"></button>
							<button type="button" onclick="changeColor('SandGreen');" style="background-color:#789081;" class="colorButton"></button>
							<button type="button" onclick="changeColor('DarkOrange');" style="background-color:#a05f34;" class="colorButton"></button>
							<button type="button" onclick="changeColor('ReddishBrown');" style="background-color:#694027;" class="colorButton"></button>
							<button type="button" onclick="changeColor('BrightViolet');" style="background-color:#6b327b;" class="colorButton"></button>
							<button type="button" onclick="changeColor('LightReddishViolet');" style="background-color:#e8bac7;" class="colorButton"></button>
							<button type="button" onclick="changeColor('MediumRed');" style="background-color:#da8679;" class="colorButton"></button>
							<button type="button" onclick="changeColor('BrickYellow');" style="background-color:#d7c599;" class="colorButton"></button>
							<button type="button" onclick="changeColor('SandRed');" style="background-color:#957976;" class="colorButton"></button>
							<button type="button" onclick="changeColor('Brown');" style="background-color:#7c5c45;" class="colorButton"></button>
							<button type="button" onclick="changeColor('Nougat');" style="background-color:#cc8e68;" class="colorButton"></button>
							<button type="button" onclick="changeColor('LightOrange');" style="background-color:#eab891;" class="colorButton"></button>
						</div>
					</div>
					<div class="SelectPart">
						<h4 class="SelectedPart" id="ActualPartLabel" style="font-size: 12px; letter-spacing: 2px;">Choosen part Left Leg</h4>
						<div class="HeadDiv">
							<button type="button" id="Head" onclick="selectPart('Head');" class="Head" style="background-color: rgb(245, 205, 47);"></button>
						</div>
						
						<div class="TorsoDiv">
							<button type="button" id="RightArm" onclick="selectPart('RightArm');" class="RightArm" style="background-color: rgb(245, 205, 47);"></button>
							<button type="button" id="Torso" onclick="selectPart('Torso');" class="Torso" style="background-color: rgb(13, 105, 171);">
								<img src="/images/shirt.png" style="object-fit: fill;width: 100%;height: 100%;">
							</button>
							<button type="button" id="LeftArm" onclick="selectPart('LeftArm');" class="LeftArm" style="background-color: rgb(245, 205, 47);"></button>
						</div>
						<div class="LegDiv">
							<button type="button" id="LeftLeg" onclick="selectPart('LeftLeg');" class="LeftLeg" style="background-color: rgb(75, 151, 74);"></button>
							<button type="button" id="RightLeg" onclick="selectPart('RightLeg');" class="RightLeg" style="background-color: rgb(75, 151, 74);"></button>
						</div>
					</div>
				</div>
				<div class="AspNet-Login">
					<div class="AspNet-Login-PasswordPanel">
						<label class="Label" style="display:block;"><em>B</em>lurb:</label>
						<textarea type="text" name="blurb" class="Text" value="" tabindex="1" accesskey="b" style="min-width:180px; max-width:470px; width: 470px; min-height:16px; height: 150px;"><?= $blurb; ?></textarea>
					</div>
					<input type="hidden" id="colorPayload" name="colorPayload" value="" />
					<div class="AspNet-Login-SubmitPanel">
						<input type="submit" name="edit_btn" class="Button" style="border: solid 1px #777; color: #777;" value="Save">
					</div>
					

					<!--<div class="AspNet-Login-PasswordRecoveryPanel">
						<a href="https://web.archive.org/web/20070813034505/http://www.roblox.com/Login/ResetPasswordRequest.aspx" title="Password recovery">Forgot your password?</a>
					</div>-->
				</div>
			</div>
		</div>
	</div>
</div>
<script>
	//for the visual side of things
	const HEXColors = {
		White: "#f2f3f2",
		LightStoneGrey: "#e5e4de",
		MediumStoneGrey: "#a3a2a4",
		DarkStoneGrey:"#635f61",
		Black:"#1b2a34",
		BrightRed:"#c4281b",
		BrightYellow:"#f5cd2f",
		CoolYellow:"#fdea8c",
		BrightBlue:"#0d69ab",
		BrightBluishGreen:"#008f9b",
		MediumBlue:"#6e99c9",
		PastelBlue:"#80bbdb",
		LightBlue:"#b4d2e3",
		SandBlue:"#74869c",
		BrightOrange:"#da8540",
		BrYellowishOrange:"#e29b3f",
		EarthGreen:"#27462c",
		DarkGreen:"#287f46",
		BrightGreen:"#4b974a",
		BrYellowishGreen:"#a4bd46",
		MediumGreen:"#a1c48b",
		SandGreen:"#789081",
		DarkOrange:"#a05f34",
		ReddishBrown:"#694027",
		BrightViolet:"#6b327b",
		LightReddishViolet:"#e8bac7",
		MediumRed:"#da8679",
		BrickYellow:"#d7c599",
		SandRed:"#957976",
		Brown:"#7c5c45",
		Nougat:"#cc8e68",
		LightOrange:"#eab891"
	};
	
	//for clients
	const IDColors = {
		White:1,
		LightStoneGrey:208,
		MediumStoneGrey:194,
		DarkStoneGrey:199,
		Black:26,
		BrightRed:21,
		BrightYellow:24,
		CoolYellow:226,
		BrightBlue:23,
		BrightBluishGreen:107,
		MediumBlue:102,
		PastelBlue:11,
		LightBlue:45,
		SandBlue:135,
		BrightOrange:106,
		BrYellowishOrange:105,
		EarthGreen:141,
		DarkGreen:28,
		BrightGreen:37,
		BrYellowishGreen:119,
		MediumGreen:29,
		SandGreen:151,
		DarkOrange:38,
		ReddishBrown:192,
		BrightViolet:104,
		LightReddishViolet:9,
		MediumRed:101,
		BrickYellow:5,
		SandRed:153,
		Brown:217,
		Nougat:18,
		LightOrange:125
	};
	
	function GetNameFromID(id) {
		for(let i = 0; i < Object.keys(IDColors).length; i++) {
			if(IDColors[Object.keys(IDColors)[i]] === parseInt(id)) {
				return Object.keys(IDColors)[i];
			}
		}
	}
	
	function setStartColors(){
		var colorPayload = '<?php echo $_SESSION['auth_user']['character']['colors']; ?>';
		document.getElementById("colorPayload").value = colorPayload;
		var colors = colorPayload.split(", ");
		console.log(colorPayload);
		headColor = colors[0];
		torsoColor = colors[1];
		leftArm = colors[2];
		rightArm = colors[3];
		leftLeg = colors[4];
		rightLeg = colors[5];
		document.getElementById("Head").style.backgroundColor = HEXColors[GetNameFromID(colors[0])];
		document.getElementById("Torso").style.backgroundColor = HEXColors[GetNameFromID(colors[1])];
		document.getElementById("LeftArm").style.backgroundColor = HEXColors[GetNameFromID(colors[2])];
		document.getElementById("RightArm").style.backgroundColor = HEXColors[GetNameFromID(colors[3])];
		document.getElementById("RightLeg").style.backgroundColor = HEXColors[GetNameFromID(colors[5])];
		document.getElementById("LeftLeg").style.backgroundColor = HEXColors[GetNameFromID(colors[4])];
	}

	var headColor = IDColors["BrightYellow"];
	var torsoColor = IDColors["BrightOrange"];
	var rightArm = IDColors["Black"];
	var leftArm = IDColors["Black"];
	var rightLeg = IDColors["DarkStoneGrey"];
	var leftLeg = IDColors["DarkStoneGrey"];
	
	var selectedpart = "Head";

	function selectPart(part){
		selectedpart = part;
		document.getElementById("ActualPartLabel").innerText = "Choosen part "+selectedpart.split(/(?=[A-Z])/).join(" ");
	}

	function changeColor(color){
		
		document.getElementById(selectedpart).style.backgroundColor = HEXColors[color];
		
		var id = IDColors[color];
		
		switch(selectedpart){
			case "Head":
				headColor = id;
				break;  
			case "Torso":
				torsoColor = id;
				break; 
			case "RightArm":
				rightArm = id;
				break; 
			case "LeftArm":
				leftArm = id;
				break; 
			case "RightLeg":
				rightLeg = id;
				break; 
			case "LeftLeg":
				leftLeg = id;
				break;  
		}
		
		var colorPayload = headColor+", "+torsoColor+", "+leftArm+", "+rightArm+", "+leftLeg+", "+rightLeg;
		document.getElementById("colorPayload").value = colorPayload;
	}
	
	setStartColors();
</script>
<?php
	include('../includes/footer.php');
?>