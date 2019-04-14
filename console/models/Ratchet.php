<?php

namespace console\models;

use common\models\History;
use Ratchet\MessageComponentInterface;
use Ratchet\ConnectionInterface;
use yii\base\Model;
use yii\helpers\Json;

class Ratchet extends Model implements MessageComponentInterface {

    private $clients = [];

    public function onOpen(ConnectionInterface $conn) {
        echo "New connection! ({$conn -> resourceId})\n";

        $queryString = $conn -> httpRequest -> getUri() -> getQuery();
        $data = explode('=', $queryString);
        $room = $data[1];

        $this -> clients[$room][] = $conn;

        var_dump($this->clients[$room]);

    }

    public function onMessage(ConnectionInterface $from, $msg) {
        $data = Json::decode($msg);
        foreach ($this -> clients[$data['room']] as $user) {
            if ($from !== $user) {
                // The sender is not the receiver, send to each client connected
                $user -> send($msg);
            }
        }
    }

    public function onClose(ConnectionInterface $conn) {
        foreach ($this -> clients as $room) {
            $arr = array_filter($room, function ($user) use($conn) { return $user -> id == $conn -> resourceId;});
            if (!empty($arr)) {
                $key = key($arr);
                var_dump($key);
                var_dump($conn -> resourceId);
                array_splice($room, $key, 1);
                echo "Connection {$conn -> resourceId} has deleted\n";
            }
        }
        echo "Connection {$conn -> resourceId} has disconnected\n";
        $conn -> close();
    }

    public function onError(ConnectionInterface $conn, \Exception $e) {
        foreach ($this -> clients as $room) {
            $arr = array_filter($room, function ($user) use($conn) { return $user -> id == $conn -> resourceId;});
            if (!empty($arr)) {
                $key = key($arr);
                array_splice($room, $key, 1);
                echo "Connection {$conn -> resourceId} has deleted\n";
            }
        }
        echo "An error has occurred: {$e -> getMessage()}\n";
        $conn -> close();
    }
}