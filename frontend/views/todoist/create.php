<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Todoist */

$this->title = 'Создать задачу';
?>
<div class="todoist-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
