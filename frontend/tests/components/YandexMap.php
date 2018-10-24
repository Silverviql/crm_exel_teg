<?php
/**
 * Created by PhpStorm.
 * User: Rus
 * Date: 13.10.2017
 * Time: 13:29
 */

namespace frontend\components;

use yii\base\Widget;

class YandexMap extends Widget
{
    public $data = null;
    public function init()
    {
        parent::init();
    }

    public function run()
    {
        $this->view->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU', ['type' => 'text/javascript']);
        $this->view->registerJsFile('@web/js/yandexMap.js');
    }
}