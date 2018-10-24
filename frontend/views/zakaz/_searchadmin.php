<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use dosamigos\datepicker\DatePicker;
use yii\helpers\ArrayHelper;
use app\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\ZakazSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="zakaz-search">
    <?php $form = ActiveForm::begin(['method' => 'get', 'action' => ['']]); ?>

    <?= $form->field($model, 'search')->textInput(['class' => 'form-control'])->label(false) ?>
    
    <?= Html::submitButton('Найти') ?>

    <?php ActiveForm::end(); ?>
</div>
