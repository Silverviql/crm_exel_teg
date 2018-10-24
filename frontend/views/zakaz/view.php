<?php

use kartik\form\ActiveForm;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use yii\helpers\Html;
use kartik\detail\DetailView;
use yii\helpers\Url;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Zakaz */
/* @var $comment app\models\Comment */
/* @var $commentField app\models\Comment */
/* @var $financy app\models\Financy */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $model->prefics;
?>
    <div class="zakaz-view">
        <div class="col-xs-6">
        <h4>Информация о заказе</h4>
        <?= DetailView::widget([
        'model' => $model,
        'striped' => false,
        'bordered' => false,
        'condensed' => true,
        'attributes' => [
            [
                'attribute' => 'srok',
                'value' => Yii::$app->formatter->asDatetime($model->srok)
            ],
            [
                'attribute' => 'id_sotrud',
                'label' => 'Магазин',
                'value' => $model->idSotrud->name,
                'visible' => Yii::$app->user->can('admin'),
            ],
            [
                'attribute' => 'shifts_id',
                'value' => $model->shifts->idSotrud->nameSotrud
            ],
            [
                'attribute' => 'prioritetName',
                'label' => 'Приоритет',
                'value' => $model->prioritet == null ? false : $model->prioritetName,
                'visible' => Yii::$app->user->can('admin') && $model->prioritet != null,
            ],
            [
                'attribute' => 'statusName',
                'label' => 'Этап',
                'visible' => Yii::$app->user->can('admin'),
            ],
            [
                'attribute' => 'oplata',
                'visible' => Yii::$app->user->can('seeAdop'),
            ],
            [
                'attribute' => 'fact_oplata',
                'visible' => Yii::$app->user->can('seeAdop'),
            ],
            'number',
            [
                'attribute' => 'data',
                'value' => Yii::$app->formatter->asDatetime($model->data),
                'visible' => Yii::$app->user->can('seeAdop'),
            ],
            [
                'attribute' => 'information',
                'format' => 'raw',
                'valueColOptions' => ['class' => 'zakaz-information'],
                'value' => $notice == null ? $model->information.'<span class="glyphicon glyphicon-plus addNotice" data-toggle="tooltip" title="Добавить примечание" data-href="'.Url::to(["notify/create", 'id' => $model->id_zakaz]).'"></span>' : $notice[0]->comment.' (Редактирован '.Yii::$app->formatter->asDatetime($notice[0]->created_at).')<span class="glyphicon glyphicon-plus addNotice" data-toggle="tooltip" title="Добавить примечание" data-href="'.Url::to(["notify/create", 'id' => $model->id_zakaz]).'"></span>',
            ],
            [
                'attribute' => 'img',
                'format' => 'raw',
                'value' => $model->img == null ? null : Html::a($model->img, '@web/'.$model->img, ['download' => true])
            ],
            [
                'attribute' => 'maket',
                'format' => 'raw',
                'value' => $model->maket == null ? null : Html::a($model->maket, '@web/'.$model->maket, ['download' => true]),
                'visible' => $model->maket != null
            ],
            [
                'attribute' => 'statusDisainName',
                'visible' => Yii::$app->user->can('seeDisain') && $model->statusDisain != null,
                'label' => 'Статус у дизайнера',
            ],
        ],
    ]) ?>
        </div>
        <div class="col-lg-4">
            <h4>Информация о клиенте</h4>
            <?= DetailView::widget([
                'model' => $model,
                'striped' => false,
                'bordered' => false,
                'condensed' => true,
                'attributes' => [
                    [
                        'attribute' => 'id_client',
                        'label' => 'ФИО клиента',
                        'value' => $model->idClient->fioClient
                    ],
                    [
                        'attribute' => 'id_client',
                        'label' => 'Телефон',
                        'value' => $model->idClient->phone
                    ],
                    [
                        'attribute' => 'id_client',
                        'label' => 'Эл. почта',
                        'value' => $model->idClient->email,
                        'visible' => $model->idClient->email != null
                    ],
                ]
            ]) ?>
        </div>
        <div class="col-lg-3">
            <h4>Информация о поступлений</h4>
            <?php if ($financy == null) {
                echo 'Поступлений пока нет';
            } else {
                foreach ($financy as $payment){
                    echo Yii::$app->formatter->asDatetime($payment->date).' '.$payment->sum.' руб.<br>';
                }
            } ?>
        </div>
            <?php if (Yii::$app->user->can('admin')) :?>
        <div class="col-lg-5">
            <h4>Информация  доставках</h4>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'headerRowOptions' => ['class' => 'headerTable'],
                'striped' => false,
                'condensed' => true,
                'containerOptions' => ['class' => 'table-responsive zakaz-view_shipping'],
                'pjax' => true,

                'columns' => [
                    [
                        'attribute' => 'date',
                        'value' => function ($model){
                            return Yii::$app->formatter->asDate($model->date);
                        },
                    ],
                    'to',
                    'from',
                    'commit',
                    [
                        'attribute' => 'status',
                        'value' => function($model){
                            return $model->dostavkaName;
                        }
                    ],
                ]
            ]) ?>
        </div>
        <?php endif; ?>
        <?php if ($notice != null): ?>
            <div class="col-lg-6">
                <?php Pjax::begin([
                    'id' => 'commentPjax'
                ]) ?>
                    <div class="notice-block"><?= '<span class="notice-sotrud">'.$notice[0]->user->name.'</span> '.$notice[0]->comment.' <span class="notice-action pull-right">'.Yii::$app->formatter->asDatetime($notice[0]->created_at).'</span>' ?></div>
                    <?php echo count($notice[0]->comments) >= 6 ? '<div class="zakaz-view_comment">' : '<div>' ?>
                    <?php foreach ($comment as $item): ?>
                        <?php if ($item['notice_id'] != null): ?>
                            <?php if ($notice[0]->id == $item['notice_id']): ?>
                            <div><span class="notice-sotrud"><?= $item['idUser']['name'] ?></span><?= $item['comment'] ?><span class="notice-action pull-right"><?= Yii::$app->formatter->asDatetime($item['date']) ?></span></div>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    </div>
                    <?php foreach ($notice as $key => $value): ?>
                        <?php if ($key != 0): ?>
                        <div class="notice-block"><?= '<span class="notice-sotrud">'.$value->user->name.'</span> '.$value->comment.' <span class="notice-action pull-right">'.Yii::$app->formatter->asDatetime($value->created_at).'</span>' ?></div>
                            <?php echo count($notice->comments) >= 6 ? '<div class="zakaz-view_comment">' : '<div>' ?>
                            <?php foreach ($comment as $item): ?>
                            <?php if ($item['notice_id'] != null): ?>
                                <?php if ($notice[0]->id != $item['notice_id']): ?>
                                <div><span class="notice-sotrud"><?= $item['idUser']['name'] ?></span><?= $item['comment'] ?><span class="notice-action pull-right"><?= Yii::$app->formatter->asDatetime($item['date']) ?></span></div>
                                <?php endif; ?>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    </div>
                    <div class="notice-block"><?= '<span class="notice-sotrud">'.$model->idSotrud->name.'</span>'.$model->information.' <span class="notice-action pull-right">Основной</span>' ?></div>
                    <?php echo count($model->comments) >= 6 ? '<div class="zakaz-view_comment">' : '<div>' ?>
                    <?php foreach ($comment as $item): ?>
                        <?php if ($item['notice_id'] == null): ?>
                            <div><span class="notice-sotrud"><?= $item['idUser']['name'] ?></span><?= $item['comment'] ?><span class="notice-action pull-right"><?= Yii::$app->formatter->asDatetime($item['date']) ?></span></div>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    </div>
                <?php Pjax::end() ?>
            </div>
        <?php endif; ?>
        <?php if ($notice == null): ?>
        <?php Pjax::begin([
            'id' => 'commentPjax'
        ]) ?>
        <div class="col-lg-6">
            <?php foreach ($comment as $item): ?>
                <div><span class="notice-sotrud"><?= $item['idUser']['name'] ?></span><?= $item['comment'] ?><span class="notice-action pull-right"><?= Yii::$app->formatter->asDatetime($item['date']) ?></span></div>
            <?php endforeach; ?>
        </div>
        <?php Pjax::end() ?>
        <?php endif; ?>
        <div class="col-lg-6">
            <h4><?= Html::encode('Комментарии') ?></h4>
<!--            --><?php //if ($comment == null){
//                echo 'Комментариев пока нет';
//            } else {
//                foreach ($comment as $key=>$com){
//                    echo '<b>'.Yii::$app->formatter->asDate($key, 'php:j M Y').'</b><br>';
//                    foreach ($comment as $value=>$name){
//                        echo Yii::$app->formatter->asTime(ArrayHelper::getValue($name, 'time'), 'php:H:i').' '.ArrayHelper::getValue($name, 'comment').' '.ArrayHelper::getValue($name, 'idUser.name').'<br>';
//                    }
//                }
//            } ?>
            <?php $form = ActiveForm::begin([
                    'id' => 'formComment',
                    'action' => ['comment/zakaz'],
            ]) ?>
            <?= $form->field($commentField, 'comment')->textInput()->label(false) ?>
            <?= $form->field($commentField, 'id_zakaz')->hiddenInput(['value' => $model->id_zakaz])->label(false) ?>
            <?= $form->field($commentField, 'id_user')->hiddenInput(['value' => Yii::$app->user->id])->label(false) ?>
            <?= Html::submitButton('Отправить', ['class' => 'btn action']) ?>
            <?php ActiveForm::end() ?>
        </div>
</div>
<?php Modal::begin([
    'id' => 'create-modal_notify'
]);

echo '<div class="create-notify">';
echo Html::beginForm(['notice/create', 'id' => $model->id_zakaz], 'post', ['id' => 'form-create_notify']);
echo Html::textarea('comment', null, ['class' => 'form-control', 'placeholder' => 'Введите примечание']);
echo Html::submitButton('Создать', ['class' => 'btn btn-primary']);
echo Html::endForm();
echo '</div>';
echo '</div>';

Modal::end()?>
<?php $script = <<<JS
    $('#formComment').on('beforeSubmit', function(e) {
      let form = $(this);
      $.post(
          form.attr('action'),
          form.serialize()
      )
      .done(function(result) {
        if (result == true){
            $.pjax.reload({container: '#commentPjax'});
            $('#formComment').trigger('reset');
        } else {
            return false;
        }       
      }).fail(function() {
           console.log('server error');
      });
      return false
    });
JS;
$this->registerJs($script)?>
