<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2019-02-07
 * Time: 22:44
 */

namespace console\controllers;

use console\models\Ratchet;
use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;
use yii\console\Controller;

class RatchetController extends Controller {
    public function actionStart() {
        $server = IoServer::factory(
            new HttpServer(
                new WsServer(
                    new Ratchet()
                )
            ),
            8080
        );

        $server -> run();
    }
}