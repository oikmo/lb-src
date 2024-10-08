local Visit = game:service("Visit")
local Players = game.Players
local NetworkClient = game:service("NetworkClient")

local HeadColor, TorsoColor, LeftArmColor, RightArmColor, LeftLegColor, RightLegColor
local HeadColorID, TorsoColorID, LeftArmColorID, RightArmColorID, LeftLegColorID, RightLegColorID
local playerName = nil
local version = nil
local function onConnectionRejected()
	game:SetMessage("This game is not available. Please try another! :33")
end

local function onConnectionFailed(_, id, reason)
	game:SetMessage("Failed to connect to the Game... :[ (ID=" .. id .. ") :[[[")
end

local function onConnectionAccepted(peer, replicator)
	
	local player = Players.LocalPlayer
	if version ~= "2011" then
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
	player.Name = playerName
	
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
		if game.workspace:FindFirstChild("lambda-disconnect-reason-"..player.Name) ~= nil then
			game:SetMessage("Disconnected: "..game.workspace["lambda-disconnect-reason-"..player.Name].Value) 
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

function SetPlayerColors(Player,HCID,TCID,LACID,RACID,LLCID,RLCID)
	HeadColorID = HCID
	TorsoColorID = TCID
	LeftArmColorID = LACID
	RightArmColorID = RACID
	LeftLegColorID = LLCID
	RightLegColorID = RLCID
	
	HeadColor = BrickColor.new(HeadColorID)
	TorsoColor = BrickColor.new(TorsoColorID)
	LeftArmColor = BrickColor.new(LeftArmColorID)
	RightArmColor = BrickColor.new(RightArmColorID)
	LeftLegColor = BrickColor.new(LeftLegColorID)
	RightLegColor = BrickColor.new(RightLegColorID)
end

function start(ver, ip, port, name, h, t, la, ra, ll, rl) 
	game:SetMessage("Connecting to Server... :3")
	version = ver
	if version ~= "2008" and version ~= "2009" then
		game:GetService("Players"):SetChatStyle(Enum.ChatStyle.ClassicAndBubble)
	end
	if version ~= "2011" then
	
		local player = game.Players.LocalPlayer
			
		if not player then
			player = Players:createLocalPlayer(0)
		end
		
		SetPlayerColors(player, h, t, la, ra, ll, rl)
		playerName = name
		local success, errorMsg = NetworkClient:Connect(ip, port)
		
		if not success then
			game:SetMessage("Failed to connect.")
		end
	else
		game:GetService("Players"):SetChatStyle(Enum.ChatStyle.ClassicAndBubble)
		local success, player = pcall(function() return NetworkClient:PlayerConnect(0, ip, port) end)
		SetPlayerColors(player, h, t, la, ra, ll, rl)
		playerName = name
	end
end