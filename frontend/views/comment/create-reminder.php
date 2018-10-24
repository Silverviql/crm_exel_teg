<?php
use kartik\widgets\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var $shipping app\models\Courier */
/** @var $model app\models\Zakaz */
$this->registerJsFile('@web/js/yandexMap.js');
?>
<div class="zakaz-reminderForm">
    <?php $form = ActiveForm::begin([
        'id' => 'shippingZakaz'
    ]); ?>
    <?= $form->field($comment, 'id_user')->hiddenInput(['value' => Yii::$app->user->id])->label(false) ?>
    <?= $form->field($comment, 'id_zakaz')->hiddenInput(['value' => $model])->label(false) ?>

    <?= $form-> field($comment, 'comment')->textInput(['placeholder' => 'Что', 'class' => 'inputForm', 'style' => 'float:left'])->label(false) ?>

    <?= $form->field($comment, 'date')->widget(DateTimePicker::className() ,[
        'pluginOptions' => [
            'autoclose'=>true,
            'startDate' => 'php Y-m-d H:i:s',
            'todayBtn' => true,
            'todayHighlight' => true,
        ],
        'options' => [
            'placeholder' => 'Срок',
        ],
    ])->label(false) ?>


    <div class="form-group">
        <?= Html::submitButton('Отправить', ['class' => 'action']) ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>