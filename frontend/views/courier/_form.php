<?php

use frontend\components\YandexMap;
use kartik\widgets\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Courier */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="courier-form">

    <?php YandexMap::widget(['index' => 'courier']) ?>

    <?php $form = ActiveForm::begin([
        'id' => 'CourierForm'
    ]); ?>

    <?= $form->field($model, 'date')->widget(
            DateTimePicker::className(), [
            'pluginOptions' => [
                'autoclose'=>true,
                'startDate' => 'php Y-m-d H:i:s',
                'format' => 'dd M yyyy hh:ii:ss',
                'todayBtn' => true,
                'todayHighlight' => true,
            ],
    ]) ?>

    <?= $form->field($model, 'toYandexMap')->textInput(['maxlength' => true, 'id' => 'toMap', 'value' => $model->to_name]) ?>
    <?= $form->field($model, 'to')->hiddenInput(['maxlength' => true, 'id' => 'toInput'])->label(false) ?>
    <?= $form->field($model, 'to_name')->hiddenInput(['maxlength' => true, 'id' => 'toName'])->label(false) ?>

    <?= $form->field($model, 'fromYandexMap')->textInput(['maxlength' => true, 'id' => 'fromMap', 'value' => $model->from_name]) ?>
    <?= $form->field($model, 'from')->hiddenInput(['maxlength' => true, 'id' => 'fromInput'])->label(false) ?>
    <?= $form->field($model, 'from_name')->hiddenInput(['maxlength' => true, 'id' => 'fromName'])->label(false) ?>

    <?= $form->field($model, 'commit')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Редактирвовать', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
