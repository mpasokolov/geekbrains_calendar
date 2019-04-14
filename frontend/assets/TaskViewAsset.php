<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2019-02-08
 * Time: 17:55
 */

namespace frontend\assets;


use yii\web\AssetBundle;

class TaskViewAsset extends AssetBundle {
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/taskView.css',
    ];
    public $js = [
        'js/Message.js',
        'js/Chat.js',
        'js/taskView.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}