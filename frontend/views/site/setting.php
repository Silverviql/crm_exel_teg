<?php
/* @vat $this \yii\web\View */
/* @var $model \app\models\User */
/* @var $sotrud \app\models\Shifts */
/* @var $shifts \app\models\Shifts */
/* @var $personnel \app\models\Personnel */
/* @var $formSotrud \app\models\SotrudForm */
/* @var array $shifts */

use app\models\Personnel;
use kartik\detail\DetailView;
use kartik\form\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\Pjax;

?>
<a href="http://telegram.me/HollandSotrudbot?start=<?= $model->telegram_token; ?>" target="_blank" class="black-btn btn-lg">
    <i class="fa fa-paper-plane"></i> Подключить
</a>

<?= DetailView::widget([
    'model' => $model,
    'hover' => false,
    'mode'=>DetailView::MODE_VIEW,
    'striped' => false,
    'panel'=>[
        'heading'=>'Учетная запись для пользователя ' . $model->name,
        'type'=>DetailView::TYPE_INFO,
        'headingOptions' => ['template' => '{title}']
    ],
    'attributes' => [
        'email:email',
        'address:text',
        'phone',
        [
            'attribute' => 'otdel_id',
            'value' =>  ArrayHelper::getValue($model, 'idOtdel.name')
        ],
        [
            'attribute' => 'otdel_id',
            'value' =>  ArrayHelper::getValue($model, 'idOtdel.id'),
            'label' => '№ отдела',
        ],
        'personnelAsString',
    ]
]) ?>

<?= Html::submitButton('Начать смену', ['class' => 'btn action startShift']); ?>
<?php if($shifts != null): ?>
<?= Html::submitButton('Закончить смену', ['class' => 'btn action endShift']); ?>
<?php endif; ?>
<div class="form-shiftStart">
    <?php Pjax::begin() ?>
<?php $form = ActiveForm::begin([
        'id' => 'form-startShift',
        'options' => ['data' => ['pjax' =>true]],
]); ?>
    <h3>Начать смену</h3>
<?= $form->field($formSotrud, 'sotrud')->dropDownList(ArrayHelper::map(Personnel::find()->where(['not in', 'id', array_keys($shifts)])->andWhere(['action' => 0])->all(), 'id', 'nameSotrud'),
    [
        'prompt' => 'Выберите сотрудника',
    ])->label(false) ?>

<?= $form->field($formSotrud, 'password')->passwordInput()->label(false) ?>

<?= Html::submitButton('Начать', ['class' => 'btn action']) ?>

<?php ActiveForm::end(); ?>
    <?php Pjax::end() ?>
</div>
<div class="form-shiftEnd">
<?php $form = ActiveForm::begin([
    'id' => 'form-endShift',
    'action' => ['site/end-sotrud']
]); ?>

    <h3>Закончить смену</h3>
<?= $form->field($formSotrud, 'sotrud')->dropDownList(ArrayHelper::map($shifts, 'idSotrud.id', 'idSotrud.nameSotrud'),
    [
        'prompt' => 'Выберите сотрудника',
    ])->label(false) ?>
<?= $form->field($formSotrud, 'password')->passwordInput()->label(false) ?>

<?= Html::submitButton('Закончить', ['class' => 'btn action']) ?>

<?php ActiveForm::end(); ?>
</div>
