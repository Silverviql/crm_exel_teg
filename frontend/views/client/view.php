<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Client */
/* @var $searchModel app\models\ZakazSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = $model->fio;
$this->params['breadcrumbs'][] = ['label' => 'Клиент', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="client-view">

<!--    <h1>--><?//= Html::encode($this->title) ?><!--</h1>-->

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить клиента',
                'method' => 'post',
            ],
        ]) ?>
        <?= Html::a('Создать заказ', ['zakaz/create', 'phone' => $model->phone, 'id' => $model->id], ['class' => 'btn btn-success'])?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'options' => ['class' => 'table detail-view'],
        'attributes' => [
            'id',
            'fio',
            'phone',
            'email:email',
            [
                'attribute' => 'address',
                'value' => function($model){
                    if ($model->street == null){
                        return '';
                    } elseif($model->apartment == null){
                        return $model->street.' д.'.$model->home;
                    } else {
                        return $model->street.' д.'.$model->home.' кв.'.$model->apartment;
                    }
                }
            ],
        ],
    ]) ?>

    <h3>Заказы</h3>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'tableOptions' => ['class' => 'table table-bordered'],
        'columns' => [
            'id_zakaz',
            'description',
            [
                'attribute' => 'statusName',
                'label' => false,
            ],
            [
                'attribute' => 'action',
                'value' => function($zakaz){
                    return $zakaz->action == 1 ? 'Активный' : 'Закрытый';
                }
            ]
        ]
    ])
    ?>

</div>
