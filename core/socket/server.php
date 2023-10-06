<?php
    (!defined('ROOT')) ? define("ROOT", dirname(__FILE__, 3)) : "";
    require ROOT. '/core/routing/routing.php';
    require ROOT. '/vendor/autoload.php';

    use Ratchet\MessageComponentInterface;
    use Ratchet\ConnectionInterface;
    use Ratchet\Server\IoServer;
    use Ratchet\Http\HttpServer;
    use Ratchet\WebSocket\WsServer;

    class react implements MessageComponentInterface {
        
        protected $clients;
        protected $clientMap; 

        public function __construct() {
            $this->clients = new \SplObjectStorage();
            $this->clientMap = [];
        }

        public function onOpen(ConnectionInterface $conn) {
            $this->clients->attach($conn);
            //echo "Nuevo cliente conectado: {"  .json_encode($conn->resourceId) ."}\n";
        }

        public function onMessage(ConnectionInterface $from, $msg) {
            $data = json_decode($msg);
            $type = $data->type;
            $sender = $data->message->from;
            $to = $data->message->to;
            $msdata = $data->message->data;

            if($type == "setId"){
                $clientId = $msdata->clientId;
                if(isset($clientId) || !is_int($clientId)){
                    $clientId = 0;
                }
                if(!isset($this->clientMap[$clientId])){
                    $this->clientMap[$clientId] = [];
                }
                array_push($this->clientMap[$clientId], $from->resourceId);
                //echo "El cliente con ID '$clientId' se ha conectado.\n";
            }
            if($to == "none"){
                return 0;
            }
            if($to == "all"){
                foreach ($this->clients as $client) {
                    if (!$this->isClientInList($client, $this->clientMap[$sender])) {
                        $client->send(json_encode($msg));
                    }
                }
            }
            if(is_array($to)){
                foreach ($to as $dst) {
                    foreach ($this->clients as $client) {
                        if(isset($this->clientMap[$dst])){
                            foreach($this->clientMap[$dst] as $clientMapId){
                                if($client->resourceId == $clientMapId){
                                    $client->send(json_encode($msg));
                                } 
                            }
                        }
                    } 
                } 
            }
            else{
                foreach ($this->clients as $client) {
                    if(isset($this->clientMap[$to])){
                        foreach($this->clientMap[$to] as $clientMapId){
                            if($client->resourceId == $clientMapId){
                                $client->send(json_encode($msg));
                            } 
                        }
                    }
                } 
            }
        }
            
        public function onClose(ConnectionInterface $conn) {
            $this->clients->detach($conn);
            foreach($clientMap as $clientId){
                $clientMapId = array_search($conn->resourceId, $clientId);
                if($clientMapId !== false){
                    unset($clientId[$clientMapId]);
                }
            }
            //echo "Cliente desconectado: {$conn->resourceId}\n";
        }

        public function onError(ConnectionInterface $conn, \Exception $e) {
            echo "Error: {$e->getMessage()}\n";
            $conn->close();
        }
        
        private function isClientInList($client, $list) {
            foreach ($list as $id) {
                if ($client->resourceId == $id) {
                    return true;
                }
            }
            return false;
        }
        
    }

    $port = routing::config('socket', NULL)["data"]["port"];

    $server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new react()
            )
        ),
        $port
    );

    echo "Websocket server listening on $port \n";
    $server->run();
?>
