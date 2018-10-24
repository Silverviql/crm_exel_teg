<?php


/* @var $this yii\web\View */
/* @var $model app\models\Zakaz */
/* @var $client app\models\Client */


$this->title = 'Добавить заказ';

?>
<div class="zakaz-create">

    <?= $this->render('_form', [
        'model' => $model,
        'client' => $client,
    ]) ?>

</div>
