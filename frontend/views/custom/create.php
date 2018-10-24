<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $models app\models\Custom */

$this->title = 'Создание запроса';
//$this->params['breadcrumbs'][] = ['label' => 'Customs', 'url' => ['index']];
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="custom-create">

    <?= $this->render('_form', [
        'models' => $models,
    ]) ?>

</div>
