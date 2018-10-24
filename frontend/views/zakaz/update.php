<?php


/* @var $this yii\web\View */
/* @var $model app\models\Zakaz */
/* @var $client app\models\Client */

$this->title = 'Заказ: ' . $model->id_zakaz;
?>
<div class="zakaz-update">

    <?= $this->render('_form', [
        'model' => $model,
        'client' => $client,
    ]) ?>
</div>
