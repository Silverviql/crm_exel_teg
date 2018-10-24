<?php

/* @var $this yii\web\View */
/* @var $model app\models\Todoist */

$this->title = 'Редактировать Задачу: ' . $model->id;
?>
<div class="todoist-update">
    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
