<?php
/**
 * Created by PhpStorm.
 * User: holland
 * Date: 05.07.2017
 * Time: 14:15
 */
namespace frontend\components;

use yii\base\Widget;
use app\models\Notification;
use Yii;
use yii\helpers\Html;

class Notifications extends Widget
{
    public function run()
    {
        return $this->renderItems();
    }

    /**
     * Creates a window for the notification
     * @return string
     */
    public function renderItems()
    {
        $notifModel = Notification::find();
        $notifications = $notifModel->where(['id_user' => Yii::$app->user->id, 'active' => true])->all();
        $this->renderCount();
        foreach($notifications as $notification){
            $date = date('Y-m-d H:i:s', time());
            if ($notification->category == 0) {
                $notif = '<span class="glyphicon glyphicon-road"></span> '.$notification->name.'<br>';
            } elseif ($notification->category == 1) {
                $notif = '<span class="glyphicon glyphicon-ok"></span> '.$notification->name.'<br>';
            } elseif ($notification->category == 2) {
                $notif = '<span class="glyphicon glyphicon-file"></span> '.$notification->name.'<br>';
            } elseif ($notification->category == 4 && $notification->srok <= $date){
                $notif = 'Напоминание о заказе №'.$notification->id_zakaz.' '.$notification->srok;
            } elseif ($notification->category == 4 && $notification->srok >= $date){
                $notif = '';
            }
            /** @var string $notif */
            echo Html::a($notif.'<br>', ['notification/notification', 'id' => $notification->id_zakaz], ['id' => $notification->id_zakaz, 'class' => 'zakaz', 'data-key' => $notification->id_zakaz]);
        };
        $formNotif = '<div class="notification-container hidden" id="notification-container">
                    <div class="notification-content">'.$notifications.'</div>
                <div class="notification-footer">'.Html::a('Прочитать все напоминание', ['notification/index']).'</div>
            </div>';
        return $formNotif;
    }

    /**
     * Notification count row
     * @return string
     */
    public function renderCount()
    {
        $notifModel = Notification::find();
        $notification = $notifModel->where(['id_user' => Yii::$app->user->id, 'active' => true])->count();
        if ($notification > 50) {
            $notifications = '50+';
        } elseif ($notification < 1) {
            $notifications = '';
        } else {
            $notifications = $notification;
        }
        return '<span class="glyphicon glyphicon-bell" style="font-size:21px"></span><span class="badge pull-right">'.$notifications.'</span>';
    }
}