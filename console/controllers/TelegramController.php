<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2019-02-16
 * Time: 19:06
 */

namespace console\controllers;


use common\models\Tasks;
use common\models\Teams;
use common\models\TelegramAuth;
use common\models\TelegramOffset;
use common\models\TelegramSubscribe;
use common\models\User;
use console\models\TelegramActions;
use SonkoDmitry\Yii\TelegramBot\Component;
use TelegramBot\Api\Types\Message;
use TelegramBot\Api\Types\Update;
use yii\console\Controller;

class TelegramController extends Controller {
    /** @var Component $bot */
    private $bot;
    private $offset = 0;

    public function init() {
        parent::init();
        /** @var Component $bot */
        $bot = \Yii::$app->bot;
        $bot -> setCurlOption(CURLOPT_TIMEOUT, 20);
        $bot -> setCurlOption(CURLOPT_CONNECTTIMEOUT, 10);
        $bot -> setCurlOption(CURLOPT_HTTPHEADER, ['Expect:']);
        $this -> bot = $bot;
    }

    public function actionIndex() {
        $updates = $this -> bot -> getUpdates($this -> getOffset() + 1);
        $updCount = count($updates);

        if ($updCount > 0) {
            foreach ($updates as $update) {
                $this -> updateOffset($update);
                if ($message = $update -> getMessage()) {
                    $this -> processCommand($message);
                }
            }
            echo 'Новых сообщений: ' . $updCount . PHP_EOL;
        } else {
            echo 'Новых сообщений нет!' . PHP_EOL;
        }
    }

    private function getOffset() {
        $max = TelegramOffset::find()
            -> select('id')
            -> max('id');

        if ($max > 0) {
            $this -> offset = $max;
        }

        return $this -> offset;
    }

    private function updateOffset(Update $update) {
        $model = new TelegramOffset([
            'id' => $update -> getUpdateId(),
            'timestamp_offset' => date("Y-m-d H:i:s"),
        ]);
        $model -> save();
    }

    private function processCommand(Message $message) {
        $data = explode(' ', $message -> getText());
        $command = ltrim($data[0], '/');
        array_splice($data, 0, 1);
        $params = $data;

        $actions = new TelegramActions();
        $response = method_exists($actions, $command) ? $actions -> $command($message, $params) : 'unknown command';

        $this -> bot -> sendMessage($message -> getFrom() -> getId(), $response);
    }
}