<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2019-02-16
 * Time: 18:40
 */

namespace frontend\controllers;


use SonkoDmitry\Yii\TelegramBot\Component as sonko;
use yii\web\Controller;

class TelegramController extends Controller {
    public function actionReceive() {

        /** @var Sonko $bot */
        $bot = \Yii::$app -> bot;
        $bot -> setCurlOption(CURLOPT_TIMEOUT, 20);
        $bot -> setCurlOption(CURLOPT_CONNECTTIMEOUT, 10);
        $bot -> setCurlOption(CURLOPT_HTTPHEADER, ['Expect:']);

        $updates = $bot -> getUpdates();
        $messages = [];

        foreach ($updates as $update) {
            $message = $update -> getMessage();
            $username = $message -> getFrom() -> getFirstName() . ' ' . $message -> getFrom() -> getLastName();
            $messages[] = [
                'message' => $message -> getText(),
                'user' => $username,
            ];
        }

        return $this -> render('index', ['messages' => $messages]);
    }

}