<?php

/* @var $this yii\web\View */
/* @var $model app\models\Todoist */

$this->title = 'Задача к заказу '.Yii::$app->request->get('id_zakaz');
?>
<div class="todoist-create">

    <?= $this->render('_formzakaz', [
        'model' => $model,
    ]) ?>

</div>
