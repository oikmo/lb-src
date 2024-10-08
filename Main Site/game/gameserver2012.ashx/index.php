<?php header("content-type: text/plain"); ?>
local placeId = -1
local port = <?php echo (isset($_GET['port']))? $_GET['port'] : "nil"; ?><?php echo "\n"; ?>
local sleeptime = 15
local access = nil
local url = ""
local timeout = nil
local maxPlayers = <?php echo (isset($_GET['maxPlayers']))? $_GET['maxPlayers'] : "nil"; ?><?php echo "\n"; ?>
local injectScriptAssetID = nil
local servicesUrl = ""
local libraryRegistrationScriptAssetID = nil
local killTime = 120

function urlget()
	return "http://asset.lambda.cam/api/api.php?port=<?php echo (isset($_GET['port']))? $_GET['port'] : "nil"; ?>&id=<?php echo (isset($_GET['id']))? $_GET['id'] : "nil"; ?>&msg=plr_count:".. tostring(#game.Players:GetPlayers()) .. ",max_size:<?php echo (isset($_GET['maxPlayers']))? $_GET['maxPlayers'] : "12"; ?>,kill_time:".. tostring(killTime) .. "&insert=1&game_id=<?php echo (isset($_GET['placeID']))? $_GET['placeID'] : "nil"; ?>&code=chatomg"
end

-----------------------------------"CUSTOM" SHARED CODE----------------------------------

pcall(function() settings().Network.UseInstancePacketCache = true end)
pcall(function() settings().Network.UsePhysicsPacketCache = true end)
--pcall(function() settings()["Task Scheduler"].PriorityMethod = Enum.PriorityMethod.FIFO end)
pcall(function() settings()["Task Scheduler"].PriorityMethod = Enum.PriorityMethod.AccumulatedError end)

--settings().Network.PhysicsSend = 1 -- 1==RoundRobin
settings().Network.PhysicsSend = Enum.PhysicsSendMethod.ErrorComputation2
settings().Network.ExperimentalPhysicsEnabled = true
settings().Network.WaitingForCharacterLogRate = 100
pcall(function() settings().Diagnostics:LegacyScriptMode() end)

-----------------------------------START GAME SHARED SCRIPT------------------------------

local assetId = placeId -- might be able to remove this now

local scriptContext = game:GetService('ScriptContext')
pcall(function() scriptContext:AddStarterScript(libraryRegistrationScriptAssetID) end)
scriptContext.ScriptsDisabled = true

--game:SetPlaceID(assetId, true)
game:GetService("ChangeHistoryService"):SetEnabled(false)

-- establish this peer as the Server
local ns = game:GetService("NetworkServer")

if url~=nil then
	pcall(function() game:GetService("Players"):SetAbuseReportUrl(url .. "/AbuseReport/InGameChatHandler.ashx") end)
	pcall(function() game:GetService("ScriptInformationProvider"):SetAssetUrl(url .. "/Asset/") end)
	pcall(function() game:GetService("ContentProvider"):SetBaseUrl(url .. "/") end)
	--pcall(function() game:GetService("Players"):SetChatFilterUrl(url .. "/Game/ChatFilter.ashx") end)

	game:GetService("BadgeService"):SetPlaceId(placeId)
	if access~=nil then
		game:GetService("BadgeService"):SetAwardBadgeUrl(url .. "/Game/Badge/AwardBadge.ashx?UserID=%d&BadgeID=%d&PlaceID=%d&" .. access)
		game:GetService("BadgeService"):SetHasBadgeUrl(url .. "/Game/Badge/HasBadge.ashx?UserID=%d&BadgeID=%d&" .. access)
		game:GetService("BadgeService"):SetIsBadgeDisabledUrl(url .. "/Game/Badge/IsBadgeDisabled.ashx?BadgeID=%d&PlaceID=%d&" .. access)

		game:GetService("FriendService"):SetMakeFriendUrl(servicesUrl .. "/Friend/CreateFriend?firstUserId=%d&secondUserId=%d&" .. access)
		game:GetService("FriendService"):SetBreakFriendUrl(servicesUrl .. "/Friend/BreakFriend?firstUserId=%d&secondUserId=%d&" .. access)
		game:GetService("FriendService"):SetGetFriendsUrl(servicesUrl .. "/Friend/AreFriends?userId=%d&" .. access)
	end
	game:GetService("BadgeService"):SetIsBadgeLegalUrl("")
	game:GetService("InsertService"):SetBaseSetsUrl(url .. "/Game/Tools/InsertAsset.ashx?nsets=10&type=base")
	game:GetService("InsertService"):SetUserSetsUrl(url .. "/Game/Tools/InsertAsset.ashx?nsets=20&type=user&userid=%d")
	game:GetService("InsertService"):SetCollectionUrl(url .. "/Game/Tools/InsertAsset.ashx?sid=%d")
	game:GetService("InsertService"):SetAssetUrl(url .. "/Asset/?id=%d")
	game:GetService("InsertService"):SetAssetVersionUrl(url .. "/Asset/?assetversionid=%d")
	game:GetService("InsertService"):SetTrustLevel(0) -- i dont know what this does... it just works...
	
	pcall(function() loadfile(url .. "/Game/LoadPlaceInfo.ashx?PlaceId=" .. placeId)() end)
	
	pcall(function() 
		if access then
			loadfile(url .. "/Game/PlaceSpecificScript.ashx?PlaceId=" .. placeId .. "&" .. access)()
		end
	end)
end

pcall(function() game:GetService("NetworkServer"):SetIsPlayerAuthenticationRequired(false) end)
settings().Diagnostics.LuaRamLimit = 0
--settings().Network:SetThroughputSensitivity(0.08, 0.01)
--settings().Network.SendRate = 35
--settings().Network.PhysicsSend = 0  -- 1==RoundRobin

--shared["__time"] = 0
--game:GetService("RunService").Stepped:connect(function (time) shared["__time"] = time end)


--[[game:GetService("Players").PlayerAdded:connect(function(player)
	print("Player " .. player.userId .. " added")
	
	if url and access and placeId and player and player.userId then
		-- Custom --
		game:HttpGet(url .. "/Game/PlayerTracking.ashx?m=r&" .. access .. "&i=" .. player.userId .. "&n=" .. player.Name)
	end
end)]]


--[[game:GetService("Players").PlayerRemoving:connect(function(player)
	print("Player " .. player.userId .. " leaving")	

	if url and access and placeId and player and player.userId then
		-- Custom --
		game:HttpGet(url .. "/Game/PlayerTracking.ashx?m=u&" .. access .. "&i=" .. player.userId)
	end
end)]]

if placeId~=nil and url~=nil then
	-- yield so that file load happens in the heartbeat thread
	--wait()
end

-- Now start the connection
ns:Start(port, sleeptime) 

if timeout then
	scriptContext:SetTimeout(timeout)
end
scriptContext.ScriptsDisabled = false

------------------------------END START GAME SHARED SCRIPT--------------------------

-- StartGame -- 
pcall(function() game:GetService("ScriptContext"):AddStarterScript(injectScriptAssetID) end)
game:GetService("RunService"):Run()

game:Load("http://asset.lambda.cam/place?id=<?php echo (isset($_GET['placeID']))? $_GET['placeID'] : "nil"; ?>&type=place")

local maxPlayers = 120
local killTime = 120

game:HttpGet("http://192.168.0.50/api/api.php?game_id=1&insert=-1&code=chatomg", true)
game:HttpGet(urlget(), true)

local stopServer = false

function countdown()

	coroutine.resume(coroutine.create(function()
		while killTime > 0 do
			if(killTime == 0) then
				--pcall(function()game:HttpGet(urlget(), true)end)
				
				stopServer = true
				break
			end
			for i, v in pairs(game.Players:GetChildren()) do
				if v:FindFirstChild("ShouldBeKicked").Value then
					KickPlayer(Player, ".")
				end
			end
			if #game.Players:GetPlayers() ~= 0 then
				if killTime ~= 120 then
					killTime = 120
				end
			end
			killTime = killTime - 1
			wait(1)
		end
	end))
	
	
end

local playerCount = 0
local PlayerService = game:GetService("Players")
function KickPlayer(Player,reason)
	Server = game:GetService("NetworkServer")
	local reasonValue = nil
	
	if Player ~= nil then
		if(game.workspace:FindFirstChild("lambda-disconnect-reason-"..tostring(Player:FindFirstChild("AnonymousIdentifier").Value)) == nil) then
			reasonValue = Instance.new("StringValue", game.workspace)
			reasonValue.Name = "lambda-disconnect-reason-"..Player.Name
			reasonValue.Value = reason
		else
			game.workspace:FindFirstChild("lambda-disconnect-reason-"..tostring(Player:FindFirstChild("AnonymousIdentifier").Value)).Name = "lambda-disconnect-reason-"..Player.Name
			reasonValue = game.workspace:FindFirstChild("lambda-disconnect-reason-"..Player.Name)
		end
		
		if (Player ~= nil) then
			for _,Child in pairs(Server:children()) do
				name = "ServerReplicator|"..Player.Name.."|"..Player.userId.."|"..Player.AnonymousIdentifier.Value
				if (Server:findFirstChild(name) ~= nil and Child.Name == name) then
					wait(1)
					Child:CloseConnection()
					
					print("Player '" .. Player.Name .. "' Kicked. Reason: ".. reasonValue.Value)
				else
					if Child:GetPlayer():FindFirstChild("AnonymousIdentifier").Value == Player:FindFirstChild("AnonymousIdentifier").Value then
						wait(1)
						Child:CloseConnection()
						print("Player '" .. Player.Name .. "' Kicked. Reason: "..reasonValue.Valuue)
					else
						print("No player by the name of: " .. Player.Name .. " exists.")
					end
					
				end
			end
		end
		
		if(game.workspace:FindFirstChild("lambda-disconnect-reason-"..tostring(Player:FindFirstChild("AnonymousIdentifier").Value)) ~= nil) then
			game.workspace:FindFirstChild("lambda-disconnect-reason-"..tostring(Player:FindFirstChild("AnonymousIdentifier").Value)):remove()
		end
		reasonValue:remove()
	end
end

PlayerService.PlayerAdded:connect(function(Player)
	-- create anonymous player identifier. This is so we can track clients without tripcodes
	playerCount = playerCount + 1
		
	local code = Instance.new("StringValue", Player)
	code.Value = playerCount
	code.Name = "AnonymousIdentifier"
	
	Server = game:GetService("NetworkServer")
	
	Player.Chatted:connect(function(msg)
		print(Player.Name.."; "..msg)
		
		-- if player calls reset... then kill!!!
		if msg == "!!!reset" then
			Player.Character:FindFirstChild("Humanoid").Health = 0
		end
	end)
	
	local shouldBeKicked = Instance.new("BoolValue", Player)
	shouldBeKicked.Name = "ShouldBeKicked"
	shouldBeKicked.Value = false
	
	local function kick(reason)
		print("kicking! reason:"..reason)
		if(game.workspace:FindFirstChild("lambda-disconnect-reason-"..tostring(code.Value)) == nil) then
			shouldBeKicked.Value = true
			local reasonValue = Instance.new("StringValue", game.workspace)
			reasonValue.Name = "lambda-disconnect-reason-"..tostring(code.Value) -- we don't know name yet...
			reasonValue.Value = reason
		end
		
	end
	
	if stopServer then
		kick("Server has died.")
	end
	
	if (#game.Players:GetPlayers() > maxPlayers) then
		kick("Too many players on server.")
	else
		local kicked = false
		for i, v in pairs(game.Players:GetChildren()) do
			if v.Name == Player.Name and v ~= Player then
				kicked = true
				kick("You are already on the server!")
			end
		end
		if not kicked then
			print("Player '" .. Player.Name .. "' with ID '" .. Player.userId .. "' added")
			if (showServerNotifications) then
				game.Players:Chat("Player '" .. Player.Name .. "' joined")
			end
			--Player:LoadCharacter()
		end
	end
	
	pcall(function()game:HttpGet(urlget(), true)end)
	
	Player.CharacterAdded:connect(function()
		Player.Character:FindFirstChild("Humanoid").Died:connect(function()
			wait(5)
			while not (Player:FindFirstChild("HeadColor") 
				and Player:FindFirstChild("TorsoColor") 
				and Player:FindFirstChild("LeftArmColor") 
				and Player:FindFirstChild("RightArmColor") 
				and Player:FindFirstChild("LeftLegColor")
				and Player:FindFirstChild("RightLegColor")) do
				
				wait()
			end
			Player:LoadCharacter()
			
			
			if(Player:FindFirstChild("ShouldBeKicked") ~= nil) then
				if(Player:FindFirstChild("ShouldBeKicked").Value == true) then
					KickPlayer(Player, ".")
				end
			end
			Player.Character["Head"].BrickColor = Player:FindFirstChild("HeadColor").Value
			Player.Character["Torso"].BrickColor = Player:FindFirstChild("TorsoColor").Value
			Player.Character["Left Arm"].BrickColor = Player:FindFirstChild("LeftArmColor").Value
			Player.Character["Right Arm"].BrickColor = Player:FindFirstChild("RightArmColor").Value
			Player.Character["Left Leg"].BrickColor = Player:FindFirstChild("LeftLegColor").Value
			Player.Character["Right Leg"].BrickColor = Player:FindFirstChild("RightLegColor").Value
		end)
	end)
end)
PlayerService.PlayerRemoving:connect(function(Player)
	pcall(function()game:HttpGet(urlget(), true)end)
end)

countdown()
local lastTime = killTime
coroutine.resume(coroutine.create(function()
	while wait() do
		game:HttpGet(urlget(), true)
		
		wait(5)
		
		if lastTime == killTime and (killTime ~= 119 or killTime ~= 120) and #game.Players:GetPlayers() == 0 then
			stopServer = true
			killTime = 0
		else
			lastTime = killTime
		end
		if killTime == 0 then
			stopServer = true
		end
	end
end))