<?php

use app\models\Partners;
use app\models\Zakaz;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
/** @var $model app\models\Zakaz */
/** @var $autsors app\models\Partners */
?>

<?php $form = ActiveForm::begin([
        'action' => ['zakaz/accept', 'id' => $model->id_zakaz],
]) ?>

<?= $form->field($model, 'status')->dropDownList([
    Zakaz::STATUS_DISAIN => 'Дизайнер',
    Zakaz::STATUS_MASTER => 'Мастер',
    Zakaz::STATUS_AUTSORS => 'Аутсорс',
])->label(false)?>

<?= $form->field($model, 'id_autsors')->dropDownList(
    ArrayHelper::map(Partners::find()->all(), 'id', 'name'),
    [
        'prompt' => 'Выберите партнера',
        'id' => 'autsors'
    ]
)->label(false) ?>

<?= Html::submitButton('Да', ['class' => 'action']) ?>

<?= Html::a('Никого не назначать', ['fulfilled', 'id' => $model->id_zakaz], ['class' => 'action fulfilled']) ?>

<?php ActiveForm::end() ?>
