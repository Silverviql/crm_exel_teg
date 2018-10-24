<?php

use app\models\Courier;
use app\models\Helpdesk;
use app\models\Todoist;
use app\models\Zakaz;
use kartik\grid\GridView;
use yii\helpers\Html;
use dosamigos\chartjs\ChartJs;
use yii\helpers\StringHelper;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProviderTodoist yii\data\ActiveDataProvider */
/* @var $dataProviderZakaz yii\data\ActiveDataProvider */
/* @var $dataProviderHelpdesk yii\data\ActiveDataProvider */
/* @var $zakazAll app\models\Zakaz */
/* @var $zakaz app\models\Zakaz */
/* @var $zakazModel app\models\Zakaz */
$this->title = 'Акутальнык';
?>

<div class="manager-index">
    <div class="col-lg-9">
        <h2><?=Html::encode('Задачи') ?></h2>

        <div class="col-lg-12">
            <?= GridView::widget([
                'dataProvider' => $dataProviderTodoist,
                'floatHeader' => true,
                'headerRowOptions' => ['class' => 'headerTable'],
                'pjax' => true,
                'tableOptions' 	=> ['class' => 'table table-bordered tableSize'],
                'rowOptions' => ['class' => 'trTable trNormal'],
                'striped' => false,
                'columns' => [
                    [
                        'class' => 'kartik\grid\ExpandRowColumn',
                        'width' => '1px',
                        'enableRowClick' => true,
                        'expandOneOnly' => true,
                        'expandIcon' => '<span class="glyphicon glyphicon-chevron-right"></span>',
                        'collapseIcon' => '<span class="glyphicon glyphicon-chevron-down"></span>',
                        'detailUrl' => Url::to(['todoist/todoist-detail']),
                        'value' => function(){
                            return GridView::ROW_COLLAPSED;
                        },
                        'contentOptions' => ['class' => 'border-left textTr', 'style' => 'border:none'],

                    ],
                    [
                        'attribute' => 'srok',
                        'format' => ['date', 'php:d M'],
                        'hAlign' => GridView::ALIGN_RIGHT,
                        'contentOptions' => ['class' => 'textTr tr90 srok'],
                    ],
                    [
                        'attribute' => 'comment',
                        'contentOptions'=>['style'=>'white-space: normal;'],
                    ],
                    [
                        'attribute' => 'action',
                        'format' => 'raw',
                        'contentOptions'=>['class'=>'textTr tr180'],
                        'value' => function($model){
                            if ($model->activate == Todoist::REJECT){
                                return Html::tag('span', Html::encode('Отклонено'), [
                                    'class' => 'declined',
                                    'title' => $model->declined,
                                    'data-toggle' => 'tooltip',
                                ]);
                            } else {
                                return false;
                            }
                        }
                    ],
                    [
                        'attribute' => 'zakaz',
                        'format' => 'raw',
                        'value' => function($model){
                            if ($model->id_zakaz != null) {
                                return Html::a($model->idZakaz->prefics, ['zakaz/view', 'id' => $model->id_zakaz]);
                            }
                            return '';
                        },
                        'label' => 'Заказ',
                        'hAlign' => GridView::ALIGN_RIGHT,
                        'contentOptions' => ['class' => 'textTr tr70'],
                    ],
                    [
                        'attribute' => 'id_user',
                        'value' => function($model){
                            if ($model->id_user == null){
                                return '';
                            } else {
                                return $model->idUser->name;
                            }
                        },
                        'contentOptions' => ['class' => 'border-right textTr tr100'],
                    ],
                ],
            ]); ?>
        </div>

        <h2><?=Html::encode('Заказы') ?></h2>
        <div class="col-lg-12">
            <?= GridView::widget([
                'dataProvider' => $dataProviderZakaz,
                'floatHeader' => true,
                'headerRowOptions' => ['class' => 'headerTable'],
                'pjax' => true,
                'tableOptions' 	=> ['class' => 'table table-bordered tableSize'],
                'rowOptions' => ['class' => 'trTable trNormal'],
                'striped' => false,
                'columns' => [
                    [
                        'class'=>'kartik\grid\ExpandRowColumn',
                        'contentOptions' => function($model){
                            return ['id' => $model->id_zakaz, 'class' => 'border-left', 'style' => 'border:none'];
                        },
                        'width'=>'10px',
                        'value' => function () {
                            return GridView::ROW_COLLAPSED;
                        },
                        'detailUrl' => Url::to(['zakaz/order']),
                        'enableRowClick' => true,
                        'expandOneOnly' => true,
                        'expandIcon' => ' ',
                        'collapseIcon' => ' ',
                    ],
                    [
                        'attribute' => 'id_zakaz',
                        'value' => 'prefics',
                        'hAlign' => GridView::ALIGN_RIGHT,
                        'contentOptions' => function($model) {
                            if ($model->status == Zakaz::STATUS_NEW){
                                return ['class' => 'trNew tr70 '];
                            } else {
                                return ['class' => 'textTr tr70'];
                            }
                        },
                    ],
                    [
                        'attribute' => '',
                        'format' => 'raw',
                        'contentOptions' => ['class' => 'tr20'],
                        'value' => function($model){
                            if ($model->prioritet == 2) {
                                return '<i class="fa fa-circle fa-red"></i>';
                            } elseif ($model->prioritet == 1) {
                                return '<i class="fa fa-circle fa-ping"></i>';
                            } else {
                                return '';
                            }

                        }
                    ],
                    [
                        'attribute' => 'srok',
                        'format' => ['datetime', 'php:d M H:i'],
                        'hAlign' => GridView::ALIGN_RIGHT,
                        'contentOptions' => function($model) {
                            if ($model->status == Zakaz::STATUS_NEW){
                                return ['class' => 'trNew tr100 srok'];
                            } else {
                                return ['class' => 'textTr tr100 srok'];
                            }
                        },
                    ],
                    [
                        'attribute' => 'description',
                        'value' => function($model){
                            return StringHelper::truncate($model->description, 100);
                        }
                    ],
                    [
                        'attribute' => 'tag',
                        'format' => 'raw',
                        'contentOptions' => ['class' => 'tr90'],
                        'value' => function($model){
                            return $model->tags != null ? $model->getTagsAsString('gridview') : false;
                        }
                    ],
                    [
                        'attribute' => 'id_shipping',
                        'format' => 'raw',
                        'contentOptions' => ['class' => 'tr50'],
                        'value' => function($model){
                            if ($model->id_shipping == null or $model->id_shipping == null){
                                return '';
                            } else {
                                if ($model->idShipping->status == Courier::DOSTAVKA or $model->idShipping->status == Courier::RECEIVE) {
                                    return '<i class="fa fa-truck" style="font-size: 13px;color: #f0ad4e;"></i>';
                                } elseif ($model->idShipping->status == Courier::DELIVERED){
                                    return '<i class="fa fa-truck" style="font-size: 13px;color: #191412;"></i>';
                                } else {
                                    return '';
                                }
                            }
                        }
                    ],
                    [
                        'attribute' => 'oplata',
                        'value' => 'money',
                        'hAlign' => GridView::ALIGN_RIGHT,
                        'contentOptions' => function($model) {
                            if ($model->status == Zakaz::STATUS_NEW){
                                return ['class' => 'trNew tr70'];
                            } else {
                                return ['class' => 'textTr tr70'];
                            }
                        },
                    ],
                    [
                        'attribute' => '',
                        'format' => 'raw',
                        'value' => function(){
                            return '';
                        },
                        'contentOptions' => ['class' => 'textTr border-right tr90'],
                    ]
                ]
            ]); ?>
        </div>
        <h2><?= Html::encode('Поломки') ?></h2>
        <div class="col-lg-12">
            <?= GridView::widget([
                'dataProvider' => $dataProviderHelpdesk,
                'floatHeader' => true,
                'headerRowOptions' => ['class' => 'headerTable'],
                'pjax' => true,
                'tableOptions' 	=> ['class' => 'table table-bordered tableSize'],
                'striped' => false,
                'rowOptions' => ['class' => 'trTable srok trNormal'],
                'columns' => [
                    [
                        'attribute' => 'id',
                        'hAlign' => GridView::ALIGN_RIGHT,
                        'contentOptions' => ['class' => 'border-left textTr tr50', 'style' => 'border:none'],
                    ],
                    [
                        'attribute' => 'date',
                        'format' => ['date', 'php:d M H:i'],
                        'hAlign' => GridView::ALIGN_RIGHT,
                        'contentOptions' => ['class' => 'textTr tr90'],
                    ],
                    [
                        'attribute' => 'commetnt',
                        'format' => 'text',
                        'contentOptions'=>['style'=>'white-space: normal;'],
                    ],
                    [
                        'attribute' => 'id_user',
                        'value' => 'idUser.name',
                        'contentOptions' => ['class' => 'textTr tr90'],
                        'hAlign' => GridView::ALIGN_RIGHT,
                        'visible' => Yii::$app->user->can('system'),
                    ],
                    [
                        'attribute' => 'sotrud',
                        'contentOptions' => ['class' => 'textTr tr50'],
                    ],
                    [
                        'attribute' => 'statusHelpName',
                        'format' => 'raw',
                        'contentOptions' => function($model){
                            if ($model->status == Helpdesk::STATUS_CHECKING){
                                return ['class' => 'border-right successHelp textTr'];
                            } else {
                                return ['class' => 'border-right textTr'];
                            }
                        },
                        'hAlign' => GridView::ALIGN_LEFT,
                        'value' => function($model){
                            if ($model->status == Helpdesk::STATUS_DECLINED){
                                return Html::tag('span', Html::encode($model->statusHelpName), [
                                    'title' => $model->declined,
                                    'data-toggle' => 'popover',
                                    'data-placement' => 'top',
                                    'class' => 'declined',
                                ]);
                            } else {
                                return $model->statusHelpName;
                            }
                        }
                    ],
                ],
            ]); ?>
        </div>
    </div>
    <div class="col-lg-3 statics">
        <div class="col-lg-12">
            <h3><?= Html::encode('Заказы') ?></h3>
            <?= ChartJs::widget([
                'type' => 'pie',
                'data' => [
                    'labels' => ['Просроченный', 'Актуальные'],
                    'datasets' => [
                        [
                            'backgroundColor' => ['rgba(255, 10, 10, 0.5)', 'rgba(0, 128, 0, 0.55)'],
                            'data' => [$zakaz, $zakazAll-$zakaz]
                        ],
                    ]
                ]
            ]);
            ?>
            <?php echo 'Заказы: '.number_format(Zakaz::find()->where(['action' => 1])->sum('oplata'), 0, ',', ' ').' рублей<br>'?>
            <?php echo 'Количество: '.Zakaz::find()->where(['action' => 1])->count()?>
        </div>
        <div class="col-lg-12">
            <h3><?= Html::encode('Задачи') ?></h3>
            <?= ChartJs::widget([
                    'type' => 'line',
                'data' => [
                    'labels' => [
                        date('d.m', strtotime('-5 day')),
                        date('d.m', strtotime('-4 day')),
                        date('d.m', strtotime('-3 day')),
                        date('d.m', strtotime('-2 day')),
                        date('d.m', strtotime('-1 day')),
                    ],
                    'datasets' => [
                        [
                            'label' => 'Все',
                            'backgroundColor' => "rgba(179,181,198,0.2)",
                            'borderColor' => "rgba(179,181,198,1)",
                            'pointBackgroundColor' => "rgba(179,181,198,1)",
                            'pointBorderColor' => "#fff",
                            'pointHoverBackgroundColor' => "#fff",
                            'pointHoverBorderColor' => "rgba(179,181,198,1)",
                            'data' => [
                                Todoist::find()->managerCountDay(5)
                                    ->count(),
                                Todoist::find()->managerCountDay(4)
                                    ->count(),
                                Todoist::find()->managerCountDay(3)
                                    ->count(),
                                Todoist::find()->managerCountDay(2)
                                    ->count(),
                                Todoist::find()->managerCountDay(1)
                                    ->count(),
                            ]
                        ],
                        [
                            'label' => 'Выполненые',
                            'backgroundColor' => "rgba(255,99,132,0.2)",
                            'borderColor' => "rgba(255,99,132,1)",
                            'pointBackgroundColor' => "rgba(255,99,132,1)",
                            'pointBorderColor' => "#fff",
                            'pointHoverBackgroundColor' => "#fff",
                            'pointHoverBorderColor' => "rgba(255,99,132,1)",
                            'data' => [
                                Todoist::find()->managerCountExecuteDay(5)
                                    ->count(),
                                Todoist::find()->managerCountExecuteDay(4)
                                    ->count(),
                                Todoist::find()->managerCountExecuteDay(3)
                                    ->count(),
                                Todoist::find()->managerCountExecuteDay(2)
                                    ->count(),
                                Todoist::find()->managerCountExecuteDay(1)
                                    ->count(),
                            ]
                        ]
                    ]

                ]
            ]) ?>
        </div>
    </div>
</div>