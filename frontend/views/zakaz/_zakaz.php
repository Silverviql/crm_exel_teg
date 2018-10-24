<?php

use app\models\Comment;
use app\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\bootstrap\Modal;
use app\models\Zakaz;
use app\models\Notification;
use app\models\Courier;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Tag;
use yii\widgets\Pjax;

/* @var  $comment app\models\Comment */
/* @var  $model app\models\Zakaz */
/* @var  $client app\models\Client */
/* @var  $courier app\models\Courier */
/** @var string $sotrud */
?>

<div class="view-zakaz" style="color: black">
	<div class="col-lg-2 anketaZakaz">
        <span class="anketaZakaz_from">От:</span>
        <div class="srok"><?= Yii::$app->formatter->asDatetime($model->data); ?>
        <span class="anketaZakaz_from">Автор:</span>
        <div><?= $model->idSotrud->name ?></div>

        <?php if ($model->shifts_id != null): ?>
        <span class="anketaZakaz_from">Сотрудник:</span>
        <div><?= $model->shifts->idSotrud->name ?></div>
        <?php endif; ?>

        <span class="anketaZakaz_from">Клиент:</span>
        <div><?php if ($model->name == null) {
                echo $model->idClient->name;
} else {
            echo $model->name;
            } ?></div>
        <div>
            <?php if ($model->phone == null){
                echo $model->idClient->phone;
            } else {
                echo $model->phone;
            }  ?>
        </div>
        <div><?= $model->idClient->email ?></div>
	    </div>
    </div>
	<div class="col-lg-7 zakazInfo">
        <div class="divInform">
        <?= $model->information ?>
        </div>
        <?php $comment = Comment::find()->where(['id_zakaz' => $model->id_zakaz])->orderBy('id DESC')->limit(6)->all(); ?>
        <div class="comment-zakaz">
            <?php if (count($comment) != 0  or count($comment) > 6): ?>
                <?php foreach ($comment as $com): ?>
                    <?php switch ($com->id_user){
                        case Yii::$app->user->id;
                            $user = 'Я';
                            break;
                        case (User::USER_DISAYNER);
                            $user = 'Дизайнер';
                            break;
                        case (User::USER_MASTER):
                            $user = 'Мастер';
                            break;
                    }
                    echo  '
            <div style="display: block;">
                <div class="userCommit">'.$user.':</div>
                <div class="comment">'.$com->comment.'</div>
                <div class="dateCommit">'.date('d.m H:i', strtotime($com->date)).'</div>
            </div>';
                    ?>
                <?php endforeach; ?>
                <?php if (count($comment) >= 6): ?>
                    <span class="nextComment" data-id="<?= $model->id_zakaz ?>" data-offset="1">Показать еще</span>
                <?php endif; ?>
            <?php else: ?>
                <span>Комментариев нет</span>
            <?php endif; ?>
        </div>
    </div>
	<div class="col-lg-1 zakazFile">
        <div class="zakazFile_block">
            <span class="zakazFile_block-number">Кол-во:</span>
            <div><?= $model->number ?></div>
        </div>
		<?= Detailview::widget([
			'model' => $model,
			'options' => ['class' => 'table detail-view'],
			'template' => '<tr class="trMaket"><td{contentOptions} class="zakaz-view-kartik">{value}</td></tr></tr>',
			'attributes' => [
                [
                    'attribute' => 'maket',
                    'format' =>'raw',
                    'value' => $model->maket == null ? '<div class="maket"></div>' : Html::a('<span class="glyphicon glyphicon-saved imgZakaz maketView">', '@web/maket/'.$model->maket, ['download' => true, 'data-pjax' => 0, 'title' => 'Готовый макет от дизайнера'])
                ],
				[
				    'attribute' => 'img',
                    'format' =>'raw',
                    'value' => $model->img == null ? '' : Html::a('<span class="glyphicon glyphicon-paperclip imgZakaz"></span>', '@web/attachment/'.$model->img, ['download' => true, 'data-pjax' => 0, 'title' => 'Исходный файл от клиента'])
                ],
            ],
		]) ?>
	</div>
    <div class="responsible">
        <?php if (Yii::$app->user->can('disain')): ?>
            <?php if ($model->status == Zakaz::STATUS_DISAIN && $model->statusD == Zakaz::STATUS_DISAINER_WORK): ?>
            Согласование с клиентом: <?= Html::a('Оправить', ['reconcilation', 'id' => $model->id_zakaz], ['class' => 'action']) ?>
            <?php endif ?>
            <?php if ($model->status == Zakaz::STATUS_DISAIN && $model->statusDisain == Zakaz::STATUS_DISAINER_SOGLAS): ?>
                Согласование с клиентом: <?= Html::a('Снять', ['reconcilation', 'id' => $model->id_zakaz], ['class' => 'action']) ?>
            <?php endif ?>
        <?php endif ?>
        <?php if (Yii::$app->user->can('seeIspol')): ?>
            <div class="responsible_person-status">
                <?php if ($model->status == Zakaz::STATUS_DECLINED_DISAIN or $model->status == Zakaz::STATUS_DECLINED_MASTER){
                    echo '<div class="statusZakaz declinedIspol">Отклонено</div>
<div class="declinedIspol_div">
<span class="responsible_person">По причине:</span><br>'.$model->declined.'</div>';
                }
                ?>
            </div>
        <?php endif ?>
        <?php if (Yii::$app->user->can('admin')): ?>
        <span class="responsible_person">Статус:</span>
        <div class="responsible_person-status">
            <?php if ($model->status == Zakaz::STATUS_SUC_DISAIN or $model->status == Zakaz::STATUS_SUC_MASTER){
                echo '<div class="statusZakaz">Выполнено</div>
<div>'
                    .Html::submitButton('Принять', ['class' => 'action actionApprove', 'value' => Url::to(['zakaz/accept', 'id' => $model->id_zakaz])]).' '
                    .Html::submitButton('Отклонить', ['class' => 'action actionCancel', 'value' => Url::to(['zakaz/declined', 'id' => $model->id_zakaz])]).'
</div>';
            }
            elseif($model->status == Zakaz::STATUS_DECLINED_DISAIN or $model->status == Zakaz::STATUS_DECLINED_MASTER){
                echo '<div class="statusZakaz declined">Отклонено</div>
<div class="declined_div">
<span class="responsible_person">По причине:</span><br>'.$model->declined.'</div>';
            } elseif($model->status == Zakaz::STATUS_ADOPTED){
                echo Html::submitButton('Назначить', ['class' => 'action actionApprove appoint', 'value' => Url::to(['zakaz/accept', 'id' => $model->id_zakaz])]);
            } elseif ($model->renouncement != null){
                echo '<div class="statusZakaz declined">Отказ от клиента</div>
<div class="declined_div">
<span class="responsible_person">По причине:</span><br>'.$model->renouncement.'</div>
<div>'
    .Html::a('Принять', ['refusing', 'id' => $model->id_zakaz, 'action' => 'yes'], ['class' => 'action success']).' '
    .Html::a('Отклонить', ['refusing', 'id' => $model->id_zakaz, 'action' => 'no'], ['class' => 'action cancelButton']).
'</div>';
            } elseif ($model->status == Zakaz::STATUS_AUTSORS){
                echo '<div class="statusZakaz">'.$model->idAutsors->name.'</div>
<div>'
                    .Html::submitButton('Принять', ['class' => 'action actionApprove', 'value' => Url::to(['zakaz/accept', 'id' => $model->id_zakaz])]).'
</div>';
            }
            ?>
        </div>
        <?php endif ?>
        <div class="linePrice"></div>
        <div class="oplata-zakaz">
            <span class="responsible_person namePrice">Оплачено:</span>
            <span class="responsible_person namePrice">К доплате:</span>
            <span class="responsible_person namePrice">Всего:</span>
            <div class="responsible_person price"><?= number_format($model->fact_oplata, 0, ',', ' ').'р.' ?></div>
            <div class="responsible_person price"><?php if($model->oplata != null){?>
                <?php echo number_format($model->oplata - $model->fact_oplata, 0, ',', ' ').'р.'; ?>
            <?php } ?></div>
            <div class="responsible_person price"><?= number_format($model->oplata, 0, ',', ' ').'р.'    ?></div>
        </div>
    </div>
    <div class="col-lg-12 footerView"></div>
    <div class="col-lg-12 footer-view-zakaz">
        <?php if (($model->status == Zakaz::STATUS_MASTER or $model->status == Zakaz::STATUS_DECLINED_MASTER) && Yii::$app->user->can('master')): ?>
            <?= Html::a('Готово', ['check', 'id' => $model->id_zakaz], ['class' => 'btn btn- done']) ?>
        <?php endif ?>
        <?php if (Yii::$app->user->can('seeAdop')): ?>
            <?php if ($model->status == Zakaz::STATUS_EXECUTE && $model->action == 1 && $model->renouncement == null && $model->oplata == $model->fact_oplata): ?>
                <?= Html::a('Готово', ['close', 'id' => $model->id_zakaz], ['class' => 'btn btn-xs done']) ?>
            <?php endif ?>
        <?php endif ?>
        <?php if (Yii::$app->user->can('admin')): ?>
            <?php if($model->status == Zakaz::STATUS_ADOPTED && $model->action == 1): ?>
                <?= Html::a('Выполнить', ['fulfilled','id' => $model->id_zakaz], ['class' => 'btn btn-xs done']) ?>

            <?php endif ?>
            <?php if ($model->action == 0): ?>
                <?= Html::a('Восстановить', ['restore','id' => $model->id_zakaz], [
                        'class' => 'btn btn-xs done',
                        'data' => [
                            'confirm' => 'Вы действительно хотите восстановить заказ?',
                            'method' => 'post',
                        ],
                ]) ?>
            <?php endif ?>
        <?php endif ?>
        <?php $courier = \app\models\Courier::find()->where(['id_zakaz' => $model->id_zakaz])->orderBy('id DESC')->one(); ?>
        <?php if (Yii::$app->user->can('shop')): ?>
            <?php if($model->id_zakaz == $courier->id_zakaz  && $courier->status == Courier::RECEIVE && ($courier->from == Courier::MARXA ||$courier->from ==  Courier::VOLGOGRADSKAYA|| $courier->from ==  Courier::PUSHKINA || $courier->from ==  Courier::SIBERIAN||$courier->from ==  Courier::CHETAUEVA )  ): ?>
                <?php   Pjax::begin(); ?>
                <?= Html::a('Доставил', ['courier/delivered','id' => $courier->id], ['class' => 'btn btn-xs done']) ?>
                <?php   Pjax::end(); ?>
            <?php endif ?>
        <?php endif ?>
            <?= Html::a('Задача', ['todoist/createzakaz', 'id_zakaz' => $model->id_zakaz], ['class' => 'btn btn-xs todoist']) ?>
            <?php if (Yii::$app->user->can('admin')): ?>
                <?= Html::a('Доставка', ['#'],['class' => 'btn action modalShipping-button', 'value' => Url::to(['courier/create-zakaz', 'id' => $model->id_zakaz]), 'onclick' => 'return false']) ?>
            <?php endif ?>
        <?php if (Yii::$app->user->can('seeAdop') && $model->renouncement == null): ?>
            <?php Modal::begin([
                'header' => '<h3 style="color: rgba(204, 198, 198, 0.6)">Укажите причину отказа</h3>',
                'class' => 'modal-sm',
                'toggleButton' => [
                    'tag' => 'a',
                    'class' => 'btn action',
                    'label' => 'Отказ',
                ]
            ]);
            $declinedClient = ActiveForm::begin([
                'action' => ['renouncement', 'id' => $model->id_zakaz],
                'id' => 'renouncementForm',
            ]);
            echo $declinedClient->field($model, 'renouncement')->textInput()->label(false);
            echo Html::submitButton('Отправить', ['class' => 'btn action']);
            ActiveForm::end();
            Modal::end() ?>
        <?php endif ?>
        <?php if (($model->status == Zakaz::STATUS_DISAIN or $model->status == Zakaz::STATUS_DECLINED_DISAIN) && Yii::$app->user->can('disain')): ?>
            <?= Html::submitButton('Заказ исполнен', ['class' => 'action modalDisain', 'value' => Url::to(['uploadedisain', 'id' => $model->id_zakaz])]) ?>
        <?php endif ?>
        <?php if (!Yii::$app->user->can('seeIspol')): ?>
            <?= Html::a('Редактировать', ['zakaz/update', 'id' => $model->id_zakaz], ['class' => 'btn btn-xs', 'style' => 'float: right;margin-right: 10px;'])?>
        <?php endif ?>
        <?php if (Yii::$app->user->can('seeAdop')): ?>
            <?php if ($model->oplata - $model->fact_oplata != 0): ?>
                <?= Html::a('Чек', ['#'], ['class' => 'draft btn action', 'value' => Url::to(['financy/draft', 'id' => $model->id_zakaz])]) ?>
            <?php endif ?>
        <?php endif; ?>
        <?= Html::a('Полный просмотр', ['view', 'id' => $model->id_zakaz], ['class' => 'btn action']) ?>
        <?= Html::a('Напоминание', ['#'],['class' => 'btn action modalReminder-button', 'value' => Url::to(['comment/create-reminder', 'id' => $model->id_zakaz]), 'onclick' => 'return false']) ?>
    </div>
<?php
$user = Yii::$app->user->id;
$script = <<<JS
$('body').on('click', '.nextComment', function () {
           let id = $(this).data('id');
           let offset = $(this).data('offset');
           $.get(window.location.origin+'/comment/pagination?id='+id+'&offset='+offset)
               .done(res => {
                   res = JSON.parse(res);
                   if (res.length === 0){
                       $(this).parent('.comment-zakaz').append('Комментариев нет');
                       $(this).remove();
                   } else {
                        let com = res.map(comment => {
                           let user;
                           let date = new Date(comment.date);
                           let day = date.getDate();
                           let month = date.getMonth() + 1;
                           day = day < 10 ? '0'+ day : day;
                           month = month < 10 ? '0'+month : month;
                           switch (parseInt(comment.id_user)){
                               case $user:
                                   user = 'Я';
                                   break;
                               case 4:
                                   user = 'Дизайнер';
                                   break;
                               case 3:
                                   user = 'Мастер';
                                   break;
                           }
                           return '<div style="display: block;">'+
                                '<div class="userCommit">'+user+':</div>'+
                                '<div class="comment">'+comment.comment+'</div>'+
                                '<div class="dateCommit">'+day+' '+month+' '+date.getHours()+' '+date.getMinutes()+'</div>'+
                            '</div>'
                           });
                       $(this).parent('.comment-zakaz').append(com.join(' ')+
                       '<span class="nextComment" data-id="'+id+'" data-offset="'+offset + 1 +'">Показать еще</span>');
                       $(this).remove();
                   }
               })
               .fail(err => console.error(err.responseText));
       });

JS;

$this->registerJs($script)
?>