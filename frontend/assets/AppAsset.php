<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'css/site.css',
        'css/style.css',
        'css/font-awesome.min.css',
    ];
    public $js = [
        'js/script.js',
        'js/yii_overrides.js',
        'js/modal.js',
        'js/component.js'
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'frontend\assets\SweetAlertAsset',
    ];
}
