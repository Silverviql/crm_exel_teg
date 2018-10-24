<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ClientSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Все клиенты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-index">

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать клиента', ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <?php Pjax::begin() ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'tableOptions' => ['class' => 'table table-bordered'],
        'columns' => [
            'id',
            'fioClient',
            'phone',
            'email:email',
            [
                'attribute' => 'address',
                'format' => 'raw',
                'value' => function($model){
                    if ($model->street == null){
                        return '';
                    } elseif($model->apartment == null){
                        return $model->street.' д.'.$model->home;
                    } else {
                        return $model->street.' д.'.$model->home.' кв.'.$model->apartment;
                    }
                },
                'label' => 'Адрес',
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end() ?>
</div>
