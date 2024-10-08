<?php header("content-type: text/plain"); ?>
printidentity()

local twentyelevenplus = <?php echo (isset($_GET['is2011plus']))? $_GET['is2011plus'] : "false"; ?><?php echo "\n"; ?>

function urlget()
	return "http://asset.lambda.cam/api/api.php?port=<?php echo (isset($_GET['port']))? $_GET['port'] : "nil"; ?>&id=<?php echo (isset($_GET['id']))? $_GET['id'] : "nil"; ?>&msg=plr_count:".. tostring(#game.Players:GetPlayers()) .. ",max_size:<?php echo (isset($_GET['maxPlayers']))? $_GET['maxPlayers'] : "12"; ?>,kill_time:".. tostring(_G.killTime) .. "&insert=1&game_id=<?php echo (isset($_GET['placeID']))? $_GET['placeID'] : "nil"; ?>&code=chatomg"
end

game:Load("http://asset.lambda.cam/place?id=<?php echo (isset($_GET['placeID']))? $_GET['placeID'] : "nil"; ?>&type=place")
game:GetService("NetworkServer"):Start(<?php echo (isset($_GET['port']))? $_GET['port'] : "nil"; ?>)
game:GetService("RunService"):Run()

_G.maxPlayers = <?php echo (isset($_GET['maxPlayers']))? $_GET['maxPlayers'] : "nil"; ?><?php echo "\n"; ?>
_G.killTime = 120

game:HttpGet("http://asset.lambda.cam/api/api.php?game_id=<?php echo (isset($_GET['placeID']))? $_GET['placeID'] : "nil"; ?>&insert=-1&code=chatomg", true)
game:HttpGet(urlget(), true)

_G.stopServer = false
function countdown()
	coroutine.resume(coroutine.create(function()
		while wait(0.5) do
			for i, v in pairs(game.Players:GetChildren()) do
				if v.Character ~= nil then
					if v:FindFirstChild("PlayerName") then
						if v.Name ~= v:FindFirstChild("PlayerName").Value then
							v.Name = v:FindFirstChild("PlayerName").Value
							v:FindFirstChild("PlayerName"):remove()
						end
					end
					if v:FindFirstChild("ShouldBeKicked") then
						if v:FindFirstChild("ShouldBeKicked").Value then
							if game.workspace:FindFirstChild("lambda-disconnect-reason-"..tostring(v:FindFirstChild("AnonymousIdentifier").Value)) then
								KickPlayer(v, game.workspace:FindFirstChild("lambda-disconnect-reason-"..tostring(v:FindFirstChild("AnonymousIdentifier").Value)).Value)
								
							end
							local removeThatThang = true
							for i, v in pairs(game.NetworkServer:children()) do
								if v:GetPlayer() == v then
									removeThatThang = false
								end
							end
							
							if removeThatThang then
								wait(3)
								game.workspace:FindFirstChild("lambda-disconnect-reason-"..tostring(v:FindFirstChild("AnonymousIdentifier").Value)):remove()
							end
						end
					end
				end
			end
		end
	end))
	coroutine.resume(coroutine.create(function()
		while _G.killTime > 0 do
			if(_G.killTime == 0) then
				pcall(function()game:HttpGet(urlget(), true)end)
				_G.stopServer = true
				game.NetworkServer:Stop()
				break
			end
			
			if #game.Players:GetPlayers() ~= 0 then
				if _G.killTime ~= 120 then
					_G.killTime = 120
				end
			end
			_G.killTime = _G.killTime - 1
			wait(1)
		end
	end))
	
	
end

local playerCount = 0
local PlayerService = game:GetService("Players")
function KickPlayer(Player,reason)
	coroutine.resume(coroutine.create(function()
		Server = game:GetService("NetworkServer")
		local reasonValue = nil
		
		if Player ~= nil then
			if(game.workspace:FindFirstChild("lambda-disconnect-reason-"..tostring(Player:FindFirstChild("AnonymousIdentifier").Value)) == nil) then
				reasonValue = Instance.new("StringValue", game.workspace)
				reasonValue.Name = "lambda-disconnect-reason-"..tostring(Player:FindFirstChild("AnonymousIdentifier").Value)
				reasonValue.Value = reason
			else
				reasonValue = game.workspace:FindFirstChild("lambda-disconnect-reason-"..tostring(Player:FindFirstChild("AnonymousIdentifier").Value))
			end
			
			if (Player ~= nil) then
				for _,Child in pairs(Server:children()) do
					name = "ServerReplicator|"..Player.Name.."|"..Player.userId.."|"..Player.AnonymousIdentifier.Value
					if (Server:findFirstChild(name) ~= nil and Child.Name == name) then
						wait(1)
						Child:CloseConnection()
						
						print("Player '" .. Player.Name .. "' Kicked. Reason: ".. reasonValue.Value)
					else
						if Child:GetPlayer():FindFirstChild("AnonymousIdentifier") == nil or
						   Player:FindFirstChild("AnonymousIdentifier") == nil then
							wait()
						end
							
						if Child:GetPlayer():FindFirstChild("AnonymousIdentifier").Value == Player:FindFirstChild("AnonymousIdentifier").Value then
							wait(1)
							Child:CloseConnection()
							print("Player '" .. Player.Name .. "' Kicked. Reason: "..reasonValue.Value)
						else
							print("No player by the name of: " .. Player.Name .. " exists.")
						end
						
					end
				end
			end
		end
	end))
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
		print("kicking!")
		print("lambda-disconnect-reason-"..tostring(code.Value))
		if(game.workspace:FindFirstChild("lambda-disconnect-reason-"..tostring(code.Value)) == nil) then
			local reasonValue = Instance.new("StringValue", game.workspace)
			reasonValue.Name = "lambda-disconnect-reason-"..tostring(code.Value) -- we don't know name yet...
			reasonValue.Value = reason
			shouldBeKicked.Value = true
		end
		
	end
	
	if _G.stopServer then
		kick("Server has died.")
	end
	
	if (#game.Players:GetPlayers() > _G.maxPlayers) then
		--KickPlayer(Player, "Too many players on server.")
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
	
	if twentyelevenplus then
		Player.CharacterAdded:connect(function()
			Player.Character:FindFirstChild("Humanoid").Died:connect(function()
				wait(5)
				Player:LoadCharacter()
				wait(1)
				print(Player:FindFirstChild("HeadColor").Value)
				print(Player:FindFirstChild("TorsoColor").Value)
				print(Player:FindFirstChild("LeftArmColor").Value)
				print(Player:FindFirstChild("RightArmColor").Value)
				print(Player:FindFirstChild("LeftLegColor").Value)
				print(Player:FindFirstChild("RightLegColor").Value)
				
				Player.Character["Head"].BrickColor = Player:FindFirstChild("HeadColor").Value
				Player.Character["Torso"].BrickColor = Player:FindFirstChild("TorsoColor").Value
				Player.Character["Left Arm"].BrickColor = Player:FindFirstChild("LeftArmColor").Value
				Player.Character["Right Arm"].BrickColor = Player:FindFirstChild("RightArmColor").Value
				Player.Character["Left Leg"].BrickColor = Player:FindFirstChild("LeftLegColor").Value
				Player.Character["Right Leg"].BrickColor = Player:FindFirstChild("RightLegColor").Value
			end)
		end)
	else
		
		while wait() do 
			if(Player:FindFirstChild("PlayerName") ~= nil) then
				Player.Name = Player:FindFirstChild("PlayerName").Value
				for i, v in pairs(game.Players:GetChildren()) do
					if v:FindFirstChild("PlayerName") then
						if (v.Name == Player.Name or Player:FindFirstChild("PlayerName").Value == v:FindFirstChild("PlayerName").Value) and v ~= Player then
							if v:FindFirstChild("AnonymousIdentifier").Value < Player:FindFirstChild("AnonymousIdentifier").Value then
								KickPlayer(Player, "You are already on the server!")
							else
								KickPlayer(v, "You are already on the server!")
							end
						end
					end
				end
			end
			if (Player.Character ~= nil) then
				if (Player.Character:FindFirstChild("Humanoid") and (Player.Character.Humanoid.Health == 0)) then
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
					
					Player.Character["Head"].BrickColor = Player:FindFirstChild("HeadColor").Value
					Player.Character["Torso"].BrickColor = Player:FindFirstChild("TorsoColor").Value
					Player.Character["Left Arm"].BrickColor = Player:FindFirstChild("LeftArmColor").Value
					Player.Character["Right Arm"].BrickColor = Player:FindFirstChild("RightArmColor").Value
					Player.Character["Left Leg"].BrickColor = Player:FindFirstChild("LeftLegColor").Value
					Player.Character["Right Leg"].BrickColor = Player:FindFirstChild("RightLegColor").Value
				elseif (Player.Character.Parent == nil) then 
					wait(5)
					while not (Player:FindFirstChild("HeadColor") 
						and Player:FindFirstChild("TorsoColor") 
						and Player:FindFirstChild("LeftArmColor") 
						and Player:FindFirstChild("RightArmColor") 
						and Player:FindFirstChild("LeftLegColor")
						and Player:FindFirstChild("RightLegColor")) do
						
						wait()
					end
					Player:LoadCharacter() -- to make sure nobody is deleted.
					
					while not (Player:FindFirstChild("HeadColor") 
						and Player:FindFirstChild("TorsoColor") 
						and Player:FindFirstChild("LeftArmColor") 
						and Player:FindFirstChild("RightArmColor") 
						and Player:FindFirstChild("LeftLegColor")
						and Player:FindFirstChild("RightLegColor")) do
						
						wait()
					end
					print("value of name is " .. Player:FindFirstChild("PlayerName").Value)
					
					Player.Character["Head"].BrickColor = Player:FindFirstChild("HeadColor").Value
					Player.Character["Torso"].BrickColor = Player:FindFirstChild("TorsoColor").Value
					Player.Character["Left Arm"].BrickColor = Player:FindFirstChild("LeftArmColor").Value
					Player.Character["Right Arm"].BrickColor = Player:FindFirstChild("RightArmColor").Value
					Player.Character["Left Leg"].BrickColor = Player:FindFirstChild("LeftLegColor").Value
					Player.Character["Right Leg"].BrickColor = Player:FindFirstChild("RightLegColor").Value
					--Player:FindFirstChild("PlayerName"):remove()
				end
			end
		end
	end
end)
PlayerService.PlayerRemoving:connect(function(Player)
	pcall(function()game:HttpGet(urlget(), true)end)
end)

countdown()
local lastTime = _G.killTime
print("fnny asf")
coroutine.resume(coroutine.create(function()
	while wait() do
		game:HttpGet(urlget(), true)
		
		wait(5)
		
		if lastTime == _G.killTime and (_G.killTime ~= 119 or _G.killTime ~= 120) and #game.Players:GetPlayers() == 0 then
			_G.stopServer = true
			_G.killTime = 0
		else
			lastTime = _G.killTime
		end
		if _G.killTime == 0 then
			_G.stopServer = true
		end
	end
end))