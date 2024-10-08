<?php 
	header("content-type: text/plain");
	if(isset($_GET['colors'])) {
		if(str_contains($str, ' ')) {
			list($head, $torso, $leftarm, $rightarm, $leftleg, $rightleg) = explode(", ", $_GET['colors']);
		} else {
			list($head, $torso, $leftarm, $rightarm, $leftleg, $rightleg) = explode(",", $_GET['colors']);
		}
		
	} else {
		$head = "nil";
		$torso = "nil";
		$leftarm = "nil";
		$rightarm = "nil";
		$leftleg = "nil";
		$rightleg = "nil";
	}
?>
local version = <?php echo "\""; echo (isset($_GET['version']))? $_GET['version'] : "nil"; ?><?php echo "\"";  echo "\n"; ?>
local playerName = <?php echo "\""; echo (isset($_GET['plrname']))? $_GET['plrname'] : "nil"; ?><?php echo "\"";  echo "\n"; ?>
local ip = <?php echo "\""; echo (isset($_GET['ip']))? $_GET['ip'] : "localhost"; ?><?php echo "\""; echo "\n"; ?>
local port = <?php echo (isset($_GET['port']))? $_GET['port'] : "25565"; ?><?php echo "\n";?>

local Visit = game:service("Visit")
local Players = game.Players
local NetworkClient = game:service("NetworkClient")

local HeadColor, TorsoColor, LeftArmColor, RightArmColor, LeftLegColor, RightLegColor

function SetPlayerColors(HeadColorID,TorsoColorID,LeftArmColorID,RightArmColorID,LeftLegColorID,RightLegColorID)	
	HeadColor = BrickColor.new(HeadColorID)
	TorsoColor = BrickColor.new(TorsoColorID)
	LeftArmColor = BrickColor.new(LeftArmColorID)
	RightArmColor = BrickColor.new(RightArmColorID)
	LeftLegColor = BrickColor.new(LeftLegColorID)
	RightLegColor = BrickColor.new(RightLegColorID)
end

SetPlayerColors(<?=$head?>, <?=$torso?>, <?=$leftarm?>, <?=$rightarm?>, <?=$leftleg?>, <?=$rightleg?>)
	

local function onConnectionRejected()
	game:SetMessage("This game is not available. Please try another! :33")
end

local function onConnectionFailed(_, id, reason)
	game:SetMessage("Failed to connect to the Game... :[ (ID=" .. id .. ") :[[[")
end

local function onConnectionAccepted(peer, replicator)
	
	local player = Players.LocalPlayer
	if version == "2010" and version == "2012" and version == "2013" then
		if player.className == "PseudoPlayer" then
			while player.className == "PseudoPlayer" do
				local success, plr = pcall(function() return Players:createLocalPlayer(0) end)
				print(success)
					
				if plr ~= nil then
					player = plr
					print("plr is " .. plr.className)
				end
				
				if not success then
					print("player is " .. player.className)
					break
				end
				wait(0.1)
			end
			
		end
	end
	
	print(playerName)
	--player.Name = playerName
	
	local worldReceiver = replicator:SendMarker()
	local received = false
	
	local function onWorldReceived()
		received = true
	end
	
	worldReceiver.Received:connect(onWorldReceived)
	game:SetMessageBrickCount()
	
	while not received do
		workspace:ZoomToExtents()
		wait(0.1)
	end
	
	replicator.Disconnection:connect(function()
		if game.workspace:FindFirstChild("lambda-disconnect-reason-"..tostring(player:FindFirstChild("AnonymousIdentifier").Value)) ~= nil then
			game:SetMessage("Disconnected: "..game.workspace["lambda-disconnect-reason-"..tostring(player:FindFirstChild("AnonymousIdentifier").Value)].Value) 
		else
			game:SetMessage("You have lost connection to the game")
		end
	end)
	
	game:SetMessage("Requesting character...")
	replicator:RequestCharacter()
	
	game:SetMessage("Waiting for character...")
	while not player.Character do
		player.Changed:wait()
	end
	
	while not (player.Character:FindFirstChild("Head") 
		and player.Character:FindFirstChild("Torso")
		and player.Character:FindFirstChild("Left Arm")
		and player.Character:FindFirstChild("Right Arm")
		and player.Character:FindFirstChild("Left Leg")
		and player.Character:FindFirstChild("Right Leg")) do
		
		wait()
	end
	
	player.Character["Head"].BrickColor = HeadColor
	player.Character["Torso"].BrickColor = TorsoColor
	player.Character["Left Arm"].BrickColor = LeftArmColor
	player.Character["Right Arm"].BrickColor = RightArmColor
	player.Character["Left Leg"].BrickColor = LeftLegColor
	player.Character["Right Leg"].BrickColor = RightLegColor
	
	nameAndBalls = Instance.new("StringValue", player)
	nameAndBalls.Value = playerName
	nameAndBalls.Name = "PlayerName"
	
	if not player:FindFirstChild("PlayerName") then
		while not player:FindFirstChild("PlayerName") do
			nameAndBalls = Instance.new("StringValue", player)
			nameAndBalls.Value = playerName
			nameAndBalls.Name = "PlayerName"
		end
	end
	
	HC = Instance.new("BrickColorValue")
	HC.Name = "HeadColor"
	HC.Parent = player
	HC.Value = HeadColor
	
	TC = Instance.new("BrickColorValue")
	TC.Name = "TorsoColor"
	TC.Parent = player
	TC.Value = TorsoColor
	
	LAC = Instance.new("BrickColorValue")
	LAC.Name = "LeftArmColor"
	LAC.Parent = player
	LAC.Value = LeftArmColor
	
	RAC = Instance.new("BrickColorValue")
	RAC.Name = "RightArmColor"
	RAC.Parent = player
	RAC.Value = RightArmColor
	
	LLC = Instance.new("BrickColorValue")
	LLC.Name = "LeftLegColor"
	LLC.Parent = player
	LLC.Value = LeftLegColor
	
	RLC = Instance.new("BrickColorValue")
	RLC.Name = "RightLegColor"
	RLC.Parent = player
	RLC.Value = RightLegColor
	
	game:ClearMessage()
end

NetworkClient.ConnectionAccepted:connect(onConnectionAccepted)
NetworkClient.ConnectionRejected:connect(onConnectionRejected)
NetworkClient.ConnectionFailed:connect(onConnectionFailed)

game:SetMessage("Connecting to Server... :3")

if version ~= "2008" and version ~= "2009" then
	game:GetService("Players"):SetChatStyle(Enum.ChatStyle.ClassicAndBubble)
end
if version ~= "2011" and version ~= "2012" and version ~= "2013" then

	local player = game.Players.LocalPlayer
		
	if not player then
		player = Players:createLocalPlayer(0)
	end
	
	local success, errorMsg = NetworkClient:Connect(ip, port)
	
	if not success then
		game:SetMessage(tostring(errorMsg))
	end
else
	NetworkClient:PlayerConnect(0, ip, port)
end