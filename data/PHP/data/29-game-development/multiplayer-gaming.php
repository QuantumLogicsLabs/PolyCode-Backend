<?php
/**
 * Multiplayer Gaming in PHP
 * 
 * Network gaming, real-time synchronization, and multiplayer game architecture.
 */

// Multiplayer Game Server
class MultiplayerGameServer
{
    private array $clients;
    private array $rooms;
    private array $games;
    private int $maxClients;
    private int $port;
    private bool $running;
    private array $messageHandlers;
    
    public function __construct(int $port = 8080, int $maxClients = 100)
    {
        $this->clients = [];
        $this->rooms = [];
        $this->games = [];
        $this->maxClients = $maxClients;
        $this->port = $port;
        $this->running = false;
        $this->messageHandlers = [];
        
        $this->initializeMessageHandlers();
    }
    
    private function initializeMessageHandlers(): void
    {
        $this->messageHandlers = [
            'connect' => [$this, 'handleConnect'],
            'disconnect' => [$this, 'handleDisconnect'],
            'join_room' => [$this, 'handleJoinRoom'],
            'leave_room' => [$this, 'handleLeaveRoom'],
            'create_game' => [$this, 'handleCreateGame'],
            'join_game' => [$this, 'handleJoinGame'],
            'game_action' => [$this, 'handleGameAction'],
            'chat' => [$this, 'handleChat'],
            'heartbeat' => [$this, 'handleHeartbeat']
        ];
    }
    
    public function start(): void
    {
        if ($this->running) {
            return;
        }
        
        $this->running = true;
        echo "Multiplayer game server started on port {$this->port}\n";
        echo "Max clients: {$this->maxClients}\n";
        
        $this->serverLoop();
    }
    
    public function stop(): void
    {
        $this->running = false;
        echo "Multiplayer game server stopped\n";
    }
    
    private function serverLoop(): void
    {
        while ($this->running) {
            // Simulate server tick
            $this->update();
            
            // Process messages
            $this->processMessages();
            
            // Update games
            $this->updateGames();
            
            // Broadcast updates
            $this->broadcastUpdates();
            
            // Sleep to prevent CPU overload
            usleep(10000); // 10ms
        }
    }
    
    private function update(): void
    {
        // Update client connections
        foreach ($this->clients as $clientId => $client) {
            if ($client->isTimedOut()) {
                $this->disconnectClient($clientId);
            }
        }
        
        // Update room states
        foreach ($this->rooms as $room) {
            $room->update();
        }
    }
    
    private function processMessages(): void
    {
        // Simulate message processing
        foreach ($this->clients as $clientId => $client) {
            $messages = $client->getMessages();
            
            foreach ($messages as $message) {
                $this->processMessage($clientId, $message);
            }
            
            $client->clearMessages();
        }
    }
    
    private function processMessage(string $clientId, array $message): void
    {
        $type = $message['type'] ?? '';
        
        if (isset($this->messageHandlers[$type])) {
            $handler = $this->messageHandlers[$type];
            $handler($clientId, $message);
        } else {
            echo "Unknown message type: $type\n";
        }
    }
    
    private function updateGames(): void
    {
        foreach ($this->games as $game) {
            $game->update();
            
            if ($game->isFinished()) {
                $this->endGame($game->getId());
            }
        }
    }
    
    private function broadcastUpdates(): void
    {
        foreach ($this->rooms as $room) {
            $updates = $room->getUpdates();
            
            if (!empty($updates)) {
                foreach ($room->getClients() as $clientId) {
                    if (isset($this->clients[$clientId])) {
                        $this->sendMessage($clientId, [
                            'type' => 'room_update',
                            'room_id' => $room->getId(),
                            'updates' => $updates
                        ]);
                    }
                }
                
                $room->clearUpdates();
            }
        }
    }
    
    public function connectClient(string $clientId, string $username): bool
    {
        if (count($this->clients) >= $this->maxClients) {
            echo "Server full, rejecting client: $clientId\n";
            return false;
        }
        
        if (isset($this->clients[$clientId])) {
            echo "Client already connected: $clientId\n";
            return false;
        }
        
        $client = new Client($clientId, $username);
        $this->clients[$clientId] = $client;
        
        echo "Client connected: $clientId ($username)\n";
        
        // Send welcome message
        $this->sendMessage($clientId, [
            'type' => 'connected',
            'client_id' => $clientId,
            'server_time' => time()
        ]);
        
        return true;
    }
    
    public function disconnectClient(string $clientId): void
    {
        if (!isset($this->clients[$clientId])) {
            return;
        }
        
        $client = $this->clients[$clientId];
        
        // Remove from rooms
        foreach ($this->rooms as $room) {
            $room->removeClient($clientId);
        }
        
        // Remove from games
        foreach ($this->games as $game) {
            $game->removePlayer($clientId);
        }
        
        unset($this->clients[$clientId]);
        
        echo "Client disconnected: $clientId\n";
    }
    
    private function handleConnect(string $clientId, array $message): void
    {
        $username = $message['username'] ?? 'Anonymous';
        $this->connectClient($clientId, $username);
    }
    
    private function handleDisconnect(string $clientId, array $message): void
    {
        $this->disconnectClient($clientId);
    }
    
    private function handleJoinRoom(string $clientId, array $message): void
    {
        $roomId = $message['room_id'] ?? '';
        
        if (!isset($this->rooms[$roomId])) {
            $this->sendMessage($clientId, [
                'type' => 'error',
                'message' => 'Room not found'
            ]);
            return;
        }
        
        $room = $this->rooms[$roomId];
        $room->addClient($clientId);
        
        $this->sendMessage($clientId, [
            'type' => 'joined_room',
            'room_id' => $roomId,
            'room_info' => $room->getInfo()
        ]);
    }
    
    private function handleLeaveRoom(string $clientId, array $message): void
    {
        $roomId = $message['room_id'] ?? '';
        
        if (isset($this->rooms[$roomId])) {
            $this->rooms[$roomId]->removeClient($clientId);
        }
        
        $this->sendMessage($clientId, [
            'type' => 'left_room',
            'room_id' => $roomId
        ]);
    }
    
    private function handleCreateGame(string $clientId, array $message): void
    {
        $gameType = $message['game_type'] ?? 'deathmatch';
        $maxPlayers = $message['max_players'] ?? 4;
        
        $game = $this->createGame($gameType, $maxPlayers);
        $game->addPlayer($clientId);
        
        $this->sendMessage($clientId, [
            'type' => 'game_created',
            'game_id' => $game->getId(),
            'game_info' => $game->getInfo()
        ]);
    }
    
    private function handleJoinGame(string $clientId, array $message): void
    {
        $gameId = $message['game_id'] ?? '';
        
        if (!isset($this->games[$gameId])) {
            $this->sendMessage($clientId, [
                'type' => 'error',
                'message' => 'Game not found'
            ]);
            return;
        }
        
        $game = $this->games[$gameId];
        
        if (!$game->canJoin()) {
            $this->sendMessage($clientId, [
                'type' => 'error',
                'message' => 'Game is full or already started'
            ]);
            return;
        }
        
        $game->addPlayer($clientId);
        
        $this->sendMessage($clientId, [
            'type' => 'joined_game',
            'game_id' => $gameId,
            'game_info' => $game->getInfo()
        ]);
    }
    
    private function handleGameAction(string $clientId, array $message): void
    {
        $gameId = $message['game_id'] ?? '';
        $action = $message['action'] ?? '';
        $data = $message['data'] ?? [];
        
        if (!isset($this->games[$gameId])) {
            return;
        }
        
        $game = $this->games[$gameId];
        $game->handleAction($clientId, $action, $data);
    }
    
    private function handleChat(string $clientId, array $message): void
    {
        $roomId = $message['room_id'] ?? '';
        $text = $message['text'] ?? '';
        
        if (isset($this->rooms[$roomId])) {
            $room = $this->rooms[$roomId];
            
            $chatMessage = [
                'type' => 'chat',
                'room_id' => $roomId,
                'client_id' => $clientId,
                'username' => $this->clients[$clientId]->getUsername(),
                'text' => $text,
                'timestamp' => time()
            ];
            
            foreach ($room->getClients() as $clientId) {
                $this->sendMessage($clientId, $chatMessage);
            }
        }
    }
    
    private function handleHeartbeat(string $clientId, array $message): void
    {
        if (isset($this->clients[$clientId])) {
            $this->clients[$clientId]->updateHeartbeat();
        }
    }
    
    private function sendMessage(string $clientId, array $message): void
    {
        if (isset($this->clients[$clientId])) {
            $this->clients[$clientId]->sendMessage($message);
        }
    }
    
    public function createRoom(string $name, int $maxClients = 10): string
    {
        $roomId = uniqid('room_');
        $room = new Room($roomId, $name, $maxClients);
        $this->rooms[$roomId] = $room;
        
        echo "Created room: $name ($roomId)\n";
        return $roomId;
    }
    
    public function createGame(string $type, int $maxPlayers = 4): Game
    {
        $gameId = uniqid('game_');
        
        switch ($type) {
            case 'deathmatch':
                $game = new DeathmatchGame($gameId, $maxPlayers);
                break;
            case 'capture_flag':
                $game = new CaptureFlagGame($gameId, $maxPlayers);
                break;
            case 'racing':
                $game = new RacingGame($gameId, $maxPlayers);
                break;
            default:
                $game = new DeathmatchGame($gameId, $maxPlayers);
        }
        
        $this->games[$gameId] = $game;
        
        echo "Created game: $type ($gameId)\n";
        return $game;
    }
    
    private function endGame(string $gameId): void
    {
        if (!isset($this->games[$gameId])) {
            return;
        }
        
        $game = $this->games[$gameId];
        $results = $game->getResults();
        
        // Notify all players
        foreach ($game->getPlayers() as $playerId) {
            $this->sendMessage($playerId, [
                'type' => 'game_ended',
                'game_id' => $gameId,
                'results' => $results
            ]);
        }
        
        unset($this->games[$gameId]);
        echo "Game ended: $gameId\n";
    }
    
    public function getStats(): array
    {
        return [
            'connected_clients' => count($this->clients),
            'active_rooms' => count($this->rooms),
            'active_games' => count($this->games),
            'max_clients' => $this->maxClients,
            'port' => $this->port,
            'running' => $this->running
        ];
    }
    
    public function getClients(): array
    {
        return $this->clients;
    }
    
    public function getRooms(): array
    {
        return $this->rooms;
    }
    
    public function getGames(): array
    {
        return $this->games;
    }
}

// Client
class Client
{
    private string $id;
    private string $username;
    private array $messages;
    private float $lastHeartbeat;
    private float $timeoutDuration;
    
    public function __construct(string $id, string $username)
    {
        $this->id = $id;
        $this->username = $username;
        $this->messages = [];
        $this->lastHeartbeat = microtime(true);
        $this->timeoutDuration = 30; // 30 seconds
    }
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function getUsername(): string
    {
        return $this->username;
    }
    
    public function sendMessage(array $message): void
    {
        $this->messages[] = $message;
    }
    
    public function getMessages(): array
    {
        return $this->messages;
    }
    
    public function clearMessages(): void
    {
        $this->messages = [];
    }
    
    public function updateHeartbeat(): void
    {
        $this->lastHeartbeat = microtime(true);
    }
    
    public function isTimedOut(): bool
    {
        return (microtime(true) - $this->lastHeartbeat) > $this->timeoutDuration;
    }
    
    public function getLastHeartbeat(): float
    {
        return $this->lastHeartbeat;
    }
}

// Room
class Room
{
    private string $id;
    private string $name;
    private array $clients;
    private int $maxClients;
    private array $updates;
    private float $createdAt;
    
    public function __construct(string $id, string $name, int $maxClients = 10)
    {
        $this->id = $id;
        $this->name = $name;
        $this->clients = [];
        $this->maxClients = $maxClients;
        $this->updates = [];
        $this->createdAt = time();
    }
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function getName(): string
    {
        return $this->name;
    }
    
    public function addClient(string $clientId): bool
    {
        if (count($this->clients) >= $this->maxClients) {
            return false;
        }
        
        if (in_array($clientId, $this->clients)) {
            return false;
        }
        
        $this->clients[] = $clientId;
        
        $this->addUpdate('client_joined', [
            'client_id' => $clientId,
            'client_count' => count($this->clients)
        ]);
        
        return true;
    }
    
    public function removeClient(string $clientId): bool
    {
        $key = array_search($clientId, $this->clients);
        
        if ($key === false) {
            return false;
        }
        
        unset($this->clients[$key]);
        $this->clients = array_values($this->clients);
        
        $this->addUpdate('client_left', [
            'client_id' => $clientId,
            'client_count' => count($this->clients)
        ]);
        
        return true;
    }
    
    public function getClients(): array
    {
        return $this->clients;
    }
    
    public function getClientCount(): int
    {
        return count($this->clients);
    }
    
    public function getMaxClients(): int
    {
        return $this->maxClients;
    }
    
    public function isFull(): bool
    {
        return count($this->clients) >= $this->maxClients;
    }
    
    public function addUpdate(string $type, array $data): void
    {
        $this->updates[] = [
            'type' => $type,
            'data' => $data,
            'timestamp' => microtime(true)
        ];
    }
    
    public function getUpdates(): array
    {
        return $this->updates;
    }
    
    public function clearUpdates(): void
    {
        $this->updates = [];
    }
    
    public function update(): void
    {
        // Room update logic
    }
    
    public function getInfo(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'client_count' => count($this->clients),
            'max_clients' => $this->maxClients,
            'created_at' => $this->createdAt
        ];
    }
}

// Game Base Class
abstract class Game
{
    protected string $id;
    protected array $players;
    protected int $maxPlayers;
    protected bool $started;
    protected bool $finished;
    protected array $gameState;
    protected float $startTime;
    
    public function __construct(string $id, int $maxPlayers = 4)
    {
        $this->id = $id;
        $this->players = [];
        $this->maxPlayers = $maxPlayers;
        $this->started = false;
        $this->finished = false;
        $this->gameState = [];
        $this->startTime = 0;
    }
    
    public function getId(): string
    {
        return $this->id;
    }
    
    public function addPlayer(string $playerId): bool
    {
        if ($this->started || $this->finished) {
            return false;
        }
        
        if (count($this->players) >= $this->maxPlayers) {
            return false;
        }
        
        if (in_array($playerId, $this->players)) {
            return false;
        }
        
        $this->players[] = $playerId;
        $this->initializePlayer($playerId);
        
        // Start game if full
        if (count($this->players) >= $this->maxPlayers) {
            $this->start();
        }
        
        return true;
    }
    
    public function removePlayer(string $playerId): bool
    {
        $key = array_search($playerId, $this->players);
        
        if ($key === false) {
            return false;
        }
        
        unset($this->players[$key]);
        $this->players = array_values($this->players);
        
        $this->cleanupPlayer($playerId);
        
        return true;
    }
    
    public function getPlayers(): array
    {
        return $this->players;
    }
    
    public function getPlayerCount(): int
    {
        return count($this->players);
    }
    
    public function getMaxPlayers(): int
    {
        return $this->maxPlayers;
    }
    
    public function canJoin(): bool
    {
        return !$this->started && !$this->finished && count($this->players) < $this->maxPlayers;
    }
    
    public function isStarted(): bool
    {
        return $this->started;
    }
    
    public function isFinished(): bool
    {
        return $this->finished;
    }
    
    public function start(): void
    {
        $this->started = true;
        $this->startTime = microtime(true);
        $this->initializeGame();
    }
    
    public function update(): void
    {
        if (!$this->started || $this->finished) {
            return;
        }
        
        $this->updateGame();
        $this->checkWinCondition();
    }
    
    public function handleAction(string $playerId, string $action, array $data): void
    {
        if (!$this->started || $this->finished) {
            return;
        }
        
        $this->processAction($playerId, $action, $data);
    }
    
    public function getGameState(): array
    {
        return $this->gameState;
    }
    
    public function getResults(): array
    {
        return [
            'winner' => $this->getWinner(),
            'scores' => $this->getScores(),
            'duration' => $this->getDuration()
        ];
    }
    
    protected function getDuration(): float
    {
        return $this->startTime > 0 ? microtime(true) - $this->startTime : 0;
    }
    
    // Abstract methods to be implemented by subclasses
    abstract protected function initializePlayer(string $playerId): void;
    abstract protected function cleanupPlayer(string $playerId): void;
    abstract protected function initializeGame(): void;
    abstract protected function updateGame(): void;
    abstract protected function processAction(string $playerId, string $action, array $data): void;
    abstract protected function checkWinCondition(): void;
    abstract protected function getWinner(): ?string;
    abstract protected function getScores(): array;
    
    public function getInfo(): array
    {
        return [
            'id' => $this->id,
            'type' => static::class,
            'player_count' => count($this->players),
            'max_players' => $this->maxPlayers,
            'started' => $this->started,
            'finished' => $this->finished
        ];
    }
}

// Deathmatch Game
class DeathmatchGame extends Game
{
    private array $scores;
    private array $positions;
    private array $respawnTimes;
    private int $scoreLimit;
    private float $respawnDelay;
    
    public function __construct(string $id, int $maxPlayers = 4)
    {
        parent::__construct($id, $maxPlayers);
        $this->scoreLimit = 10;
        $this->respawnDelay = 3.0;
    }
    
    protected function initializePlayer(string $playerId): void
    {
        $this->scores[$playerId] = 0;
        $this->positions[$playerId] = $this->getRandomSpawnPosition();
        $this->respawnTimes[$playerId] = 0;
    }
    
    protected function cleanupPlayer(string $playerId): void
    {
        unset($this->scores[$playerId]);
        unset($this->positions[$playerId]);
        unset($this->respawnTimes[$playerId]);
    }
    
    protected function initializeGame(): void
    {
        $this->gameState = [
            'type' => 'deathmatch',
            'score_limit' => $this->scoreLimit,
            'players' => []
        ];
        
        foreach ($this->players as $playerId) {
            $this->gameState['players'][$playerId] = [
                'score' => 0,
                'position' => $this->positions[$playerId],
                'alive' => true,
                'kills' => 0,
                'deaths' => 0
            ];
        }
    }
    
    protected function updateGame(): void
    {
        $currentTime = microtime(true);
        
        foreach ($this->players as $playerId) {
            // Check for respawn
            if (!$this->gameState['players'][$playerId]['alive'] && 
                $currentTime >= $this->respawnTimes[$playerId]) {
                $this->respawnPlayer($playerId);
            }
        }
    }
    
    protected function processAction(string $playerId, string $action, array $data): void
    {
        switch ($action) {
            case 'move':
                $this->movePlayer($playerId, $data['position'] ?? null);
                break;
                
            case 'shoot':
                $this->shoot($playerId, $data['direction'] ?? null);
                break;
        }
    }
    
    private function movePlayer(string $playerId, ?Vector2 $position): void
    {
        if ($position && $this->gameState['players'][$playerId]['alive']) {
            $this->positions[$playerId] = $position;
            $this->gameState['players'][$playerId]['position'] = $position;
        }
    }
    
    private function shoot(string $playerId, ?Vector2 $direction): void
    {
        if (!$direction || !$this->gameState['players'][$playerId]['alive']) {
            return;
        }
        
        $shooterPos = $this->positions[$playerId];
        
        foreach ($this->players as $targetId) {
            if ($targetId === $playerId || !$this->gameState['players'][$targetId]['alive']) {
                continue;
            }
            
            $targetPos = $this->positions[$targetId];
            
            // Check if hit (simplified)
            if ($this->checkHit($shooterPos, $targetPos, $direction)) {
                $this->killPlayer($targetId);
                $this->scores[$playerId]++;
                $this->gameState['players'][$playerId]['score']++;
                $this->gameState['players'][$playerId]['kills']++;
                $this->gameState['players'][$targetId]['deaths']++;
                
                break;
            }
        }
    }
    
    private function checkHit(Vector2 $shooterPos, Vector2 $targetPos, Vector2 $direction): bool
    {
        $toTarget = $targetPos->subtract($shooterPos);
        $distance = $toTarget->magnitude();
        
        if ($distance > 200) {
            return false;
        }
        
        $toTargetNormalized = $toTarget->normalize();
        $directionNormalized = $direction->normalize();
        
        $dotProduct = $toTargetNormalized->dot($directionNormalized);
        
        return $dotProduct > 0.8; // Within 36 degrees
    }
    
    private function killPlayer(string $playerId): void
    {
        $this->gameState['players'][$playerId]['alive'] = false;
        $this->respawnTimes[$playerId] = microtime(true) + $this->respawnDelay;
    }
    
    private function respawnPlayer(string $playerId): void
    {
        $this->positions[$playerId] = $this->getRandomSpawnPosition();
        $this->gameState['players'][$playerId]['alive'] = true;
        $this->gameState['players'][$playerId]['position'] = $this->positions[$playerId];
    }
    
    private function getRandomSpawnPosition(): Vector2
    {
        $spawnPoints = [
            new Vector2(100, 100),
            new Vector2(700, 100),
            new Vector2(100, 500),
            new Vector2(700, 500)
        ];
        
        return $spawnPoints[array_rand($spawnPoints)];
    }
    
    protected function checkWinCondition(): void
    {
        foreach ($this->players as $playerId) {
            if ($this->scores[$playerId] >= $this->scoreLimit) {
                $this->finished = true;
                break;
            }
        }
    }
    
    protected function getWinner(): ?string
    {
        $maxScore = -1;
        $winner = null;
        
        foreach ($this->scores as $playerId => $score) {
            if ($score > $maxScore) {
                $maxScore = $score;
                $winner = $playerId;
            }
        }
        
        return $winner;
    }
    
    protected function getScores(): array
    {
        return $this->scores;
    }
}

// Capture Flag Game
class CaptureFlagGame extends Game
{
    private array $teams;
    private array $flags;
    private array $scores;
    private int $scoreLimit;
    
    public function __construct(string $id, int $maxPlayers = 4)
    {
        parent::__construct($id, $maxPlayers);
        $this->scoreLimit = 3;
    }
    
    protected function initializePlayer(string $playerId): void
    {
        $teamIndex = count($this->teams) % 2;
        $this->teams[$playerId] = $teamIndex;
        $this->scores[$teamIndex] = 0;
    }
    
    protected function cleanupPlayer(string $playerId): void
    {
        unset($this->teams[$playerId]);
    }
    
    protected function initializeGame(): void
    {
        $this->flags = [
            0 => ['position' => new Vector2(100, 300), 'holder' => null, 'at_base' => true],
            1 => ['position' => new Vector2(700, 300), 'holder' => null, 'at_base' => true]
        ];
        
        $this->gameState = [
            'type' => 'capture_flag',
            'teams' => $this->teams,
            'flags' => $this->flags,
            'scores' => $this->scores,
            'score_limit' => $this->scoreLimit
        ];
    }
    
    protected function updateGame(): void
    {
        // Update flag positions if carried
        foreach ($this->flags as $teamId => &$flag) {
            if ($flag['holder']) {
                $holderId = $flag['holder'];
                // Update flag position to follow holder
                // This would be updated based on player movement
            }
        }
    }
    
    protected function processAction(string $playerId, string $action, array $data): void
    {
        switch ($action) {
            case 'move':
                $this->movePlayer($playerId, $data['position'] ?? null);
                break;
                
            case 'capture_flag':
                $this->captureFlag($playerId);
                break;
                
            case 'return_flag':
                $this->returnFlag($playerId);
                break;
        }
    }
    
    private function movePlayer(string $playerId, ?Vector2 $position): void
    {
        // Update player position
        // This would affect flag capture logic
    }
    
    private function captureFlag(string $playerId): void
    {
        $playerTeam = $this->teams[$playerId];
        $enemyTeam = 1 - $playerTeam;
        
        $enemyFlag = &$this->flags[$enemyTeam];
        
        if ($enemyFlag['at_base'] && !$enemyFlag['holder']) {
            $enemyFlag['holder'] = $playerId;
            $enemyFlag['at_base'] = false;
            
            echo "Player $playerId captured team $enemyTeam flag\n";
        }
    }
    
    private function returnFlag(string $playerId): void
    {
        $playerTeam = $this->teams[$playerId];
        $ownFlag = &$this->flags[$playerTeam];
        
        if ($ownFlag['holder'] === $playerId) {
            $ownFlag['holder'] = null;
            $ownFlag['at_base'] = true;
            
            $this->scores[$playerTeam]++;
            
            echo "Player $playerId returned team $playerTeam flag\n";
            echo "Team $playerTeam score: {$this->scores[$playerTeam]}\n";
        }
    }
    
    protected function checkWinCondition(): void
    {
        foreach ($this->scores as $teamId => $score) {
            if ($score >= $this->scoreLimit) {
                $this->finished = true;
                break;
            }
        }
    }
    
    protected function getWinner(): ?string
    {
        $maxScore = -1;
        $winnerTeam = null;
        
        foreach ($this->scores as $teamId => $score) {
            if ($score > $maxScore) {
                $maxScore = $score;
                $winnerTeam = $teamId;
            }
        }
        
        if ($winnerTeam !== null) {
            // Return first player from winning team
            foreach ($this->teams as $playerId => $teamId) {
                if ($teamId === $winnerTeam) {
                    return $playerId;
                }
            }
        }
        
        return null;
    }
    
    protected function getScores(): array
    {
        return $this->scores;
    }
}

// Racing Game
class RacingGame extends Game
{
    private array $positions;
    private array $laps;
    private array $checkpoints;
    private int $totalLaps;
    private array $finishTimes;
    
    public function __construct(string $id, int $maxPlayers = 4)
    {
        parent::__construct($id, $maxPlayers);
        $this->totalLaps = 3;
    }
    
    protected function initializePlayer(string $playerId): void
    {
        $this->positions[$playerId] = new Vector2(400, 500);
        $this->laps[$playerId] = 0;
        $this->checkpoints[$playerId] = [];
        $this->finishTimes[$playerId] = null;
    }
    
    protected function cleanupPlayer(string $playerId): void
    {
        unset($this->positions[$playerId]);
        unset($this->laps[$playerId]);
        unset($this->checkpoints[$playerId]);
        unset($this->finishTimes[$playerId]);
    }
    
    protected function initializeGame(): void
    {
        $this->gameState = [
            'type' => 'racing',
            'total_laps' => $this->totalLaps,
            'players' => []
        ];
        
        foreach ($this->players as $i => $playerId) {
            $startPos = new Vector2(350 + $i * 50, 500);
            $this->positions[$playerId] = $startPos;
            
            $this->gameState['players'][$playerId] = [
                'position' => $startPos,
                'lap' => 0,
                'checkpoints' => [],
                'finished' => false,
                'finish_time' => null
            ];
        }
    }
    
    protected function updateGame(): void
    {
        // Update racing logic
    }
    
    protected function processAction(string $playerId, string $action, array $data): void
    {
        switch ($action) {
            case 'move':
                $this->movePlayer($playerId, $data['position'] ?? null);
                break;
                
            case 'checkpoint':
                $this->passCheckpoint($playerId, $data['checkpoint_id'] ?? 0);
                break;
        }
    }
    
    private function movePlayer(string $playerId, ?Vector2 $position): void
    {
        if ($position) {
            $this->positions[$playerId] = $position;
            $this->gameState['players'][$playerId]['position'] = $position;
        }
    }
    
    private function passCheckpoint(string $playerId, int $checkpointId): void
    {
        if (!in_array($checkpointId, $this->checkpoints[$playerId])) {
            $this->checkpoints[$playerId][] = $checkpointId;
            
            // Check if completed a lap
            if (count($this->checkpoints[$playerId]) >= 4) { // Assuming 4 checkpoints per lap
                $this->laps[$playerId]++;
                $this->checkpoints[$playerId] = [];
                $this->gameState['players'][$playerId]['lap'] = $this->laps[$playerId];
                
                echo "Player $playerId completed lap {$this->laps[$playerId]}\n";
                
                // Check if finished
                if ($this->laps[$playerId] >= $this->totalLaps) {
                    $this->finishPlayer($playerId);
                }
            }
        }
    }
    
    private function finishPlayer(string $playerId): void
    {
        if (!$this->finishTimes[$playerId]) {
            $this->finishTimes[$playerId] = microtime(true);
            $this->gameState['players'][$playerId]['finished'] = true;
            $this->gameState['players'][$playerId]['finish_time'] = $this->finishTimes[$playerId];
            
            echo "Player $playerId finished the race!\n";
        }
    }
    
    protected function checkWinCondition(): void
    {
        $finishedCount = 0;
        
        foreach ($this->finishTimes as $finishTime) {
            if ($finishTime !== null) {
                $finishedCount++;
            }
        }
        
        if ($finishedCount >= count($this->players)) {
            $this->finished = true;
        }
    }
    
    protected function getWinner(): ?string
    {
        $earliestTime = PHP_FLOAT_MAX;
        $winner = null;
        
        foreach ($this->finishTimes as $playerId => $finishTime) {
            if ($finishTime !== null && $finishTime < $earliestTime) {
                $earliestTime = $finishTime;
                $winner = $playerId;
            }
        }
        
        return $winner;
    }
    
    protected function getScores(): array
    {
        $scores = [];
        
        // Sort by finish time
        $sortedPlayers = $this->players;
        usort($sortedPlayers, function($a, $b) {
            $timeA = $this->finishTimes[$a] ?? PHP_FLOAT_MAX;
            $timeB = $this->finishTimes[$b] ?? PHP_FLOAT_MAX;
            return $timeA - $timeB;
        });
        
        foreach ($sortedPlayers as $rank => $playerId) {
            $scores[$playerId] = count($this->players) - $rank;
        }
        
        return $scores;
    }
}

// Vector2 Class (reused from game-basics.php)
class Vector2
{
    public float $x;
    public float $y;
    
    public function __construct(float $x = 0, float $y = 0)
    {
        $this->x = $x;
        $this->y = $y;
    }
    
    public function add(Vector2 $other): Vector2
    {
        return new Vector2($this->x + $other->x, $this->y + $other->y);
    }
    
    public function subtract(Vector2 $other): Vector2
    {
        return new Vector2($this->x - $other->x, $this->y - $other->y);
    }
    
    public function multiply(float $scalar): Vector2
    {
        return new Vector2($this->x * $scalar, $this->y * $scalar);
    }
    
    public function magnitude(): float
    {
        return sqrt($this->x * $this->x + $this->y * $this->y);
    }
    
    public function normalize(): Vector2
    {
        $mag = $this->magnitude();
        if ($mag > 0) {
            return $this->divide($mag);
        }
        return new Vector2();
    }
    
    public function divide(float $scalar): Vector2
    {
        return new Vector2($this->x / $scalar, $this->y / $scalar);
    }
    
    public function dot(Vector2 $other): float
    {
        return $this->x * $other->x + $this->y * $other->y;
    }
    
    public function __toString(): string
    {
        return "({$this->x}, {$this->y})";
    }
}

// Multiplayer Gaming Examples
class MultiplayerGamingExamples
{
    public function demonstrateBasicServer(): void
    {
        echo "Basic Multiplayer Server Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $server = new MultiplayerGameServer(8080, 50);
        
        // Create rooms
        $lobbyId = $server->createRoom('Main Lobby', 50);
        $room1Id = $server->createRoom('Game Room 1', 10);
        $room2Id = $server->createRoom('Game Room 2', 10);
        
        echo "Created rooms:\n";
        echo "  Main Lobby ($lobbyId)\n";
        echo "  Game Room 1 ($room1Id)\n";
        echo "  Game Room 2 ($room2Id)\n";
        
        // Simulate clients connecting
        $clients = [
            'client_1' => 'Alice',
            'client_2' => 'Bob',
            'client_3' => 'Charlie',
            'client_4' => 'Diana',
            'client_5' => 'Eve'
        ];
        
        foreach ($clients as $clientId => $username) {
            $server->connectClient($clientId, $username);
        }
        
        echo "\nConnected clients:\n";
        foreach ($server->getClients() as $clientId => $client) {
            echo "  $clientId: {$client->getUsername()}\n";
        }
        
        // Simulate room joining
        echo "\nJoining rooms:\n";
        $server->processMessage('client_1', ['type' => 'join_room', 'room_id' => $room1Id]);
        $server->processMessage('client_2', ['type' => 'join_room', 'room_id' => $room1Id]);
        $server->processMessage('client_3', ['type' => 'join_room', 'room_id' => $room2Id]);
        $server->processMessage('client_4', ['type' => 'join_room', 'room_id' => $room2Id]);
        
        // Show room status
        echo "\nRoom status:\n";
        foreach ($server->getRooms() as $roomId => $room) {
            echo "  {$room->getName()}: {$room->getClientCount()}/{$room->getMaxClients()} clients\n";
        }
        
        // Simulate chat
        echo "\nSimulating chat:\n";
        $server->processMessage('client_1', [
            'type' => 'chat',
            'room_id' => $room1Id,
            'text' => 'Hello everyone!'
        ]);
        
        // Show server stats
        echo "\nServer statistics:\n";
        $stats = $server->getStats();
        foreach ($stats as $key => $value) {
            if (is_bool($value)) {
                echo "  $key: " . ($value ? 'Yes' : 'No') . "\n";
            } else {
                echo "  $key: $value\n";
            }
        }
    }
    
    public function demonstrateGameTypes(): void
    {
        echo "\nGame Types Demo\n";
        echo str_repeat("-", 20) . "\n";
        
        $server = new MultiplayerGameServer();
        
        // Create different game types
        $deathmatchGame = $server->createGame('deathmatch', 4);
        $captureFlagGame = $server->createGame('capture_flag', 4);
        $racingGame = $server->createGame('racing', 4);
        
        echo "Created games:\n";
        echo "  Deathmatch: {$deathmatchGame->getId()}\n";
        echo "  Capture Flag: {$captureFlagGame->getId()}\n";
        echo "  Racing: {$racingGame->getId()}\n";
        
        // Connect players
        $players = ['player_1', 'player_2', 'player_3', 'player_4'];
        
        foreach ($players as $playerId) {
            $server->connectClient($playerId, "Player_" . substr($playerId, -1));
        }
        
        // Join games
        echo "\nJoining games:\n";
        
        // Deathmatch
        foreach ($players as $playerId) {
            $server->processMessage($playerId, [
                'type' => 'join_game',
                'game_id' => $deathmatchGame->getId()
            ]);
        }
        
        echo "Deathmatch game started with {$deathmatchGame->getPlayerCount()} players\n";
        
        // Simulate deathmatch
        echo "\nSimulating deathmatch:\n";
        
        for ($round = 0; $round < 5; $round++) {
            echo "  Round " . ($round + 1) . ":\n";
            
            // Simulate shooting
            $shooter = $players[0];
            $target = $players[1];
            
            $server->processMessage($shooter, [
                'type' => 'game_action',
                'game_id' => $deathmatchGame->getId(),
                'action' => 'shoot',
                'data' => ['direction' => new Vector2(1, 0)]
            ]);
            
            $server->processMessage($target, [
                'type' => 'game_action',
                'game_id' => $deathmatchGame->getId(),
                'action' => 'move',
                'data' => ['position' => new Vector2(200, 200)]
            ]);
            
            $deathmatchGame->update();
            
            echo "    Scores: " . json_encode($deathmatchGame->getScores()) . "\n";
            
            if ($deathmatchGame->isFinished()) {
                echo "    Game finished! Winner: {$deathmatchGame->getWinner()}\n";
                break;
            }
        }
        
        // Capture Flag
        echo "\nCapture Flag game:\n";
        $server->processMessage('player_1', [
            'type' => 'join_game',
            'game_id' => $captureFlagGame->getId()
        ]);
        $server->processMessage('player_2', [
            'type' => 'join_game',
            'game_id' => $captureFlagGame->getId()
        ]);
        
        $captureFlagGame->update();
        
        echo "  Teams: " . json_encode($captureFlagGame->getGameState()['teams']) . "\n";
        echo "  Scores: " . json_encode($captureFlagGame->getScores()) . "\n";
        
        // Racing
        echo "\nRacing game:\n";
        $server->processMessage('player_3', [
            'type' => 'join_game',
            'game_id' => $racingGame->getId()
        ]);
        $server->processMessage('player_4', [
            'type' => 'join_game',
            'game_id' => $racingGame->getId()
        ]);
        
        $racingGame->update();
        
        echo "  Total laps: {$racingGame->getGameState()['total_laps']}\n";
        echo "  Players: " . json_encode(array_keys($racingGame->getGameState()['players'])) . "\n";
    }
    
    public function demonstrateRealTimeSync(): void
    {
        echo "\nReal-time Synchronization Demo\n";
        echo str_repeat("-", 35) . "\n";
        
        $server = new MultiplayerGameServer();
        
        // Create a fast-paced game
        $game = $server->createGame('deathmatch', 2);
        
        // Connect players
        $server->connectClient('player_1', 'Alice');
        $server->connectClient('player_2', 'Bob');
        
        $server->processMessage('player_1', [
            'type' => 'join_game',
            'game_id' => $game->getId()
        ]);
        $server->processMessage('player_2', [
            'type' => 'join_game',
            'game_id' => $game->getId()
        ]);
        
        echo "Real-time synchronization test:\n";
        echo "  Game started: {$game->getId()}\n";
        echo "  Update rate: 60 FPS\n";
        echo "  Latency simulation: 50ms\n\n";
        
        // Simulate high-frequency updates
        for ($tick = 0; $tick < 120; $tick++) {
            $game->update();
            
            // Simulate player actions
            if ($tick % 5 === 0) {
                $shooter = $tick % 10 === 0 ? 'player_1' : 'player_2';
                $target = $tick % 10 === 0 ? 'player_2' : 'player_1';
                
                $server->processMessage($shooter, [
                    'type' => 'game_action',
                    'game_id' => $game->getId(),
                    'action' => 'shoot',
                    'data' => ['direction' => new Vector2(rand(-1, 1), rand(-1, 1))]
                ]);
                
                echo "  Tick $tick: $shooter shot at $target\n";
            }
            
            // Show game state periodically
            if ($tick % 20 === 0) {
                $state = $game->getGameState();
                echo "  Tick $tick: Game state - Players: " . count($state['players']) . "\n";
                
                foreach ($state['players'] as $playerId => $playerState) {
                    echo "    $playerId: Score={$playerState['score']}, Alive=" . ($playerState['alive'] ? 'Yes' : 'No') . "\n";
                }
            }
            
            if ($game->isFinished()) {
                echo "  Game finished at tick $tick\n";
                break;
            }
        }
        
        // Show synchronization metrics
        echo "\nSynchronization metrics:\n";
        echo "  Total ticks: 120\n";
        echo "  Actions processed: " . count(array_filter(range(0, 119), fn($i) => $i % 5 === 0)) . "\n";
        echo "  Game duration: " . round($game->getDuration(), 2) . "s\n";
        echo "  Winner: {$game->getWinner()}\n";
    }
    
    public function demonstrateNetworkOptimization(): void
    {
        echo "\nNetwork Optimization Demo\n";
        echo str_repeat("-", 30) . "\n";
        
        $server = new MultiplayerGameServer();
        
        // Create room with many players
        $room = $server->createRoom('Stress Test', 20);
        
        // Connect many players
        $players = [];
        for ($i = 1; $i <= 15; $i++) {
            $playerId = "player_$i";
            $server->connectClient($playerId, "Player_$i");
            $server->processMessage($playerId, ['type' => 'join_room', 'room_id' => $room->getId()]);
            $players[] = $playerId;
        }
        
        echo "Network optimization test:\n";
        echo "  Players in room: " . count($players) . "\n";
        echo "  Messages per second: 30\n";
        echo "  Compression: Enabled\n";
        echo "  Delta compression: Enabled\n\n";
        
        // Simulate high-frequency messaging
        $messageCount = 0;
        $compressedSize = 0;
        $uncompressedSize = 0;
        
        for ($second = 0; $second < 5; $second++) {
            echo "  Second $second:\n";
            
            for ($message = 0; $message < 30; $message++) {
                $playerId = $players[array_rand($players)];
                
                $testMessage = [
                    'type' => 'game_action',
                    'room_id' => $room->getId(),
                    'action' => 'move',
                    'data' => [
                        'position' => new Vector2(rand(0, 800), rand(0, 600)),
                        'velocity' => new Vector2(rand(-100, 100), rand(-100, 100)),
                        'rotation' => rand(0, 360),
                        'timestamp' => microtime(true)
                    ]
                ];
                
                // Simulate compression
                $uncompressedSize += strlen(json_encode($testMessage));
                $compressedSize += strlen(json_encode($testMessage)) * 0.3; // Simulate 70% compression
                
                $server->processMessage($playerId, $testMessage);
                $messageCount++;
            }
            
            echo "    Messages: 30\n";
            echo "    Room updates: " . count($room->getUpdates()) . "\n";
            $room->clearUpdates();
        }
        
        // Show optimization results
        echo "\nOptimization results:\n";
        echo "  Total messages: $messageCount\n";
        echo "  Uncompressed size: $uncompressedSize bytes\n";
        echo "  Compressed size: " . round($compressedSize) . " bytes\n";
        echo "  Compression ratio: " . round((1 - $compressedSize / $uncompressedSize) * 100, 1) . "%\n";
        echo "  Bandwidth saved: " . round($uncompressedSize - $compressedSize) . " bytes\n";
    }
    
    public function demonstrateBestPractices(): void
    {
        echo "\nMultiplayer Gaming Best Practices\n";
        echo str_repeat("-", 40) . "\n";
        
        echo "1. Server Architecture:\n";
        echo "   • Use scalable server architecture\n";
        echo "   • Implement proper load balancing\n";
        echo "   • Use connection pooling\n";
        echo "   • Implement graceful degradation\n";
        echo "   • Monitor server performance\n\n";
        
        echo "2. Network Communication:\n";
        echo "   • Use efficient serialization\n";
        echo "   • Implement message compression\n";
        echo "   • Use delta compression for updates\n";
        echo "   • Implement reliable UDP for games\n";
        echo "   • Handle network latency\n\n";
        
        echo "3. Synchronization:\n";
        echo "   • Use authoritative server model\n";
        echo "   • Implement client prediction\n";
        echo "   • Use interpolation for smoothing\n";
        echo "   • Handle clock synchronization\n";
        echo "   • Implement lag compensation\n\n";
        
        echo "4. Game Logic:\n";
        echo "   • Keep game state minimal\n";
        echo "   • Use deterministic game logic\n";
        echo "   • Implement proper validation\n";
        echo "   • Handle edge cases gracefully\n";
        echo "   • Use rollback for cheating detection\n\n";
        
        echo "5. Performance:\n";
        echo "   • Use spatial partitioning\n";
        echo "   • Implement interest management\n";
        echo "   • Use object pooling\n";
        echo "   • Optimize update frequency\n";
        echo "   • Profile network usage";
    }
    
    public function runAllExamples(): void
    {
        echo "Multiplayer Gaming Examples\n";
        echo str_repeat("=", 25) . "\n";
        
        $this->demonstrateBasicServer();
        $this->demonstrateGameTypes();
        $this->demonstrateRealTimeSync();
        $this->demonstrateNetworkOptimization();
        $this->demonstrateBestPractices();
    }
}

// Main execution
function runMultiplayerGamingDemo(): void
{
    $examples = new MultiplayerGamingExamples();
    $examples->runAllExamples();
}

// Run demo
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    runMultiplayerGamingDemo();
}
?>
