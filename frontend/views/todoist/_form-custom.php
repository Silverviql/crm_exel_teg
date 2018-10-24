<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use unclead\multipleinput\TabularInput;

/* @var $this yii\web\View */
/* @var $models app\models\Custom */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="custom-form">

    <?php $form = ActiveForm::begin([
    	'enableAjaxValidation'		=> true,
    	'enableClientValidation' 	=> false,
    	'validateOnChange' 			=> false,
    	'validateOnSubmit'			=> true,
    	'validateOnBlur' 			=> false,
    ]); ?>
    <div id="customForm">

    <?= TabularInput::widget([
    	'models' => $models,
    	'columns' => [
    		[
    			'name' => 'tovar',
    			'type' => 'textInput',
    			'title' => 'Товар',
    		],
    		[
    			'name' => 'number',
    			'type' => 'textInput',
    			'title' => 'Кол-во',
    			'options' => [
    			'type' => 'number',
    			'min' => '0'
    			]
    		],
    	],
    ]) ?>

    </div>
    <div class="form-group">
        <?= Html::submitButton('Создать', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
