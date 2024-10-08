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

settings()["Game Options"].CollisionSoundEnabled = true
pcall(function() settings().Rendering.EnableFRM = true end)
pcall(function() settings().Physics.Is30FpsThrottleEnabled = true end)
pcall(function() settings()["Task Scheduler"].PriorityMethod = Enum.PriorityMethod.AccumulatedError end)

-- arguments ---------------------------------------
local threadSleepTime = 15

if threadSleepTime==nil then
	threadSleepTime = 15
end

local test = false

print("! Joining game '' place -1 at localhost")

game:GetService("ChangeHistoryService"):SetEnabled(false)
game:GetService("ContentProvider"):SetThreadPool(16)
game:GetService("InsertService"):SetBaseSetsUrl("http://www.roblox.com/Game/Tools/InsertAsset.ashx?nsets=10&type=base")
game:GetService("InsertService"):SetUserSetsUrl("http://www.roblox.com/Game/Tools/InsertAsset.ashx?nsets=20&type=user&userid=%d")
game:GetService("InsertService"):SetCollectionUrl("http://www.roblox.com/Game/Tools/InsertAsset.ashx?sid=%d")
game:GetService("InsertService"):SetAssetUrl("http://www.roblox.com/Asset/?id=%d")
game:GetService("InsertService"):SetAssetVersionUrl("http://www.roblox.com/Asset/?assetversionid=%d")

pcall(function() game:GetService("SocialService"):SetFriendUrl("http://www.roblox.com/Game/LuaWebService/HandleSocialRequest.ashx?method=IsFriendsWith&playerid=%d&userid=%d") end)
pcall(function() game:GetService("SocialService"):SetBestFriendUrl("http://www.roblox.com/Game/LuaWebService/HandleSocialRequest.ashx?method=IsBestFriendsWith&playerid=%d&userid=%d") end)
pcall(function() game:GetService("SocialService"):SetGroupUrl("http://www.roblox.com/Game/LuaWebService/HandleSocialRequest.ashx?method=IsInGroup&playerid=%d&groupid=%d") end)
pcall(function() game:GetService("SocialService"):SetGroupRankUrl("http://www.roblox.com/Game/LuaWebService/HandleSocialRequest.ashx?method=GetGroupRank&playerid=%d&groupid=%d") end)
pcall(function() game:GetService("SocialService"):SetGroupRoleUrl("http://www.roblox.com/Game/LuaWebService/HandleSocialRequest.ashx?method=GetGroupRole&playerid=%d&groupid=%d") end)
pcall(function() game:SetCreatorID(0, Enum.CreatorType.User) end)

-- Bubble chat.  This is all-encapsulated to allow us to turn it off with a config setting
pcall(function() game:GetService("Players"):SetChatStyle(Enum.ChatStyle.ClassicAndBubble) end)

pcall( function()
	if settings().Network.MtuOverride == 0 then
	  settings().Network.MtuOverride = 1400
	end
end)

local player = nil
local playerName = <?php echo "\""; echo (isset($_GET['plrname']))? $_GET['plrname'] : "nil"; ?><?php echo "\"";  echo "\n"; ?>
local ip = <?php echo "\""; echo (isset($_GET['ip']))? $_GET['ip'] : "localhost"; ?><?php echo "\""; echo "\n"; ?>
local port = <?php echo (isset($_GET['port']))? $_GET['port'] : "25565"; ?><?php echo "\n";?>

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

-- globals -----------------------------------------

client = game:GetService("NetworkClient")
visit = game:GetService("Visit")

-- functions ---------------------------------------
function setMessage(message)
	-- todo: animated "..."
	if not false then
		game:SetMessage(message)
	else
		-- hack, good enought for now
		game:SetMessage("Teleporting ...")
	end
end

function showErrorWindow(message)
	game:SetMessage(message)
end

function reportError(err)
	print("***ERROR*** " .. err)
	if not test then visit:SetUploadUrl("") end
	client:Disconnect()
	wait(4)
	showErrorWindow("Error: " .. err)
end

-- called when the client connection closes
function onDisconnection(peer, lostConnection)
	if lostConnection then
		showErrorWindow("You have lost the connection to the game")
	else
		showErrorWindow("This game has shut down")
	end
end

function requestCharacter(replicator)
	
	-- prepare code for when the Character appears
	local connection
	connection = player.Changed:connect(function (property)
		if property=="Character" then
			game:ClearMessage()
			
			connection:disconnect()
		end
	end)
	
	setMessage("Requesting character")

	local success, err = pcall(function()	
		replicator:RequestCharacter()
		setMessage("Waiting for character")
	end)
	if not success then
		reportError(err)
		return
	end
	
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
end

-- called when the client connection is established
function onConnectionAccepted(url, replicator)

	local waitingForMarker = true
	
	local success, err = pcall(function()	
		if not test then 
		    --visit:SetPing("", 300) 
		end
		
		if not false then
			game:SetMessageBrickCount()
		else
			setMessage("Teleporting ...")
		end

		replicator.Disconnection:connect(onDisconnection)
		
		-- Wait for a marker to return before creating the Player
		local marker = replicator:SendMarker()
		
		marker.Received:connect(function()
			waitingForMarker = false
			requestCharacter(replicator)
		end)
	end)
	
	if not success then
		reportError(err)
		return
	end
	
	-- TODO: report marker progress
	
	while waitingForMarker do
		workspace:ZoomToExtents()
		wait(0.5)
	end
end

-- called when the client connection fails
function onConnectionFailed(_, error)
	showErrorWindow("Failed to connect to the Game. (ID=" .. error .. ")")
end

-- called when the client connection is rejected
function onConnectionRejected()
	connectionFailed:disconnect()
	showErrorWindow("This game is not available. Please try another")
end

idled = false
function onPlayerIdled(time)
	if time > 20*60 then
		showErrorWindow(string.format("You were disconnected for being idle %d minutes", time/60))
		client:Disconnect()	
		if not idled then
			idled = true
		end
	end
end


-- main ------------------------------------------------------------

pcall(function() settings().Diagnostics:LegacyScriptMode() end)
game:SetRemoteBuildMode(true)

setMessage("Connecting to Server")
client.ConnectionAccepted:connect(onConnectionAccepted)
client.ConnectionRejected:connect(onConnectionRejected)
connectionFailed = client.ConnectionFailed:connect(onConnectionFailed)
client.Ticket = ""

print("ip is: " .. ip)
print("port is: " .. tostring(port))

pcall(function() client:PlayerConnect(0, ip, port) end)

player = game.Players.LocalPlayer
print(player.className)
pcall(function() player:SetMembershipType(Enum.MembershipType.None) end)
pcall(function() player:SetAccountAge(365) end)
player.Idled:connect(onPlayerIdled)

pcall(function() player.Name = playerName end)

pcall(function() game:SetScreenshotInfo("") end)
pcall(function() game:SetVideoInfo('<?xml version="1.0"?><entry xmlns="http://www.w3.org/2005/Atom" xmlns:media="http://search.yahoo.com/mrss/" xmlns:yt="http://gdata.youtube.com/schemas/2007"><media:group><media:title type="plain"><![CDATA[ROBLOX Place]]></media:title><media:description type="plain"><![CDATA[ For more games visit http://www.roblox.com]]></media:description><media:category scheme="http://gdata.youtube.com/schemas/2007/categories.cat">Games</media:category><media:keywords>ROBLOX, video, free game, online virtual world</media:keywords></media:group></entry>') end)
-- use single quotes here because the video info string may have unescaped double quotes
