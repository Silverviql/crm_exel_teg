<?php

use app\models\Courier;
use yii\bootstrap\Nav;
use yii\helpers\StringHelper;
use kartik\grid\GridView;
use yii\bootstrap\Modal;
use app\models\Zakaz;
use yii\bootstrap\ButtonDropdown;
use yii\widgets\Pjax;
use kartik\widgets\Select2;


/* @var $this yii\web\View */
/* @var $searchModel app\models\ZakazSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Вce заказы';
/*$this->registerJsFile('https://api-maps.yandex.ru/2.1/?lang=ru_RU', ['type' => 'text/javascript']);*/
?>
<?php Pjax::begin(['id' => 'pjax-container']); ?>

<div class="zakaz-index">
    <p>
        <?php echo ButtonDropdown::widget([
        'label' => '+',
        'options' => [
            'class' => 'btn buttonAdd',
        ],
        'dropdown' => [
            'items' => [
                [
                    'label' => 'Заказ',
                    'url' => 'zakaz/create',
                ],
                [
                    'label' => '',
                    'options' => [
                        'role' => 'presentation',
                        'class' => 'divider'
                    ]
                ],
                [
                    'label' => 'Закупки',
                    'url' => 'custom/create'
                ],
                [
                    'label' => '',
                    'options' => [
                        'role' => 'presentation',
                        'class' => 'divider'
                    ]
                ],
                [
                    'label' => 'Поломки',
                    'url' => 'helpdesk/create'
                ],
                [
                    'label' => '',
                    'options' => [
                        'role' => 'presentation',
                        'class' => 'divider'
                    ]
                ],
                [
                    'label' => 'Задачи',
                    'url' => 'todoist/create'
                ],
                [
                    'label' => '',
                    'options' => [
                        'role' => 'presentation',
                        'class' => 'divider'
                    ]
                ],
                [
                    'label' => 'Доставка',
                    'url' => 'courier/create'
                ],
            ]
        ]
    ]); ?>
        <?php //echo $this->render('_search', ['model' => $searchModel]);?>
    </p>
    <div class="col-lg-12 divWork">
            <h3 class="titleTable">В работе</h3>
            <div class="col-lg-2 zakazSearch">
                <?php echo $this->render('_searchadmin', ['model' => $searchModel]);?>
            </div>
    </div>
    <div class="col-lg-12">
        <?=
        /** @var string $dataProviderWork */
        GridView::widget([
        'dataProvider' => $dataProviderWork,
        'floatHeader' => true,
        'headerRowOptions' => ['class' => 'headerTable'],
        'pjax' => true,
        'tableOptions' 	=> ['class' => 'table table-bordered tableSize'],
        'rowOptions' => function($model){
            if ($model->srok < date('Y-m-d H:i:s') && $model->status > Zakaz::STATUS_NEW ) {
                return ['class' => 'trTable trTablePass italic trSrok'];
            } elseif ($model->srok < date('Y-m-d H:i:s') && $model->status == Zakaz::STATUS_NEW) {
                return['class' => 'trTable trTablePass bold trSrok trNew'];
            } elseif ($model->srok > date('Y-m-d H:i:s') && $model->status == Zakaz::STATUS_NEW){
                return['class' => 'trTable bold trSrok trNew'];
            } else {
                return ['class' => 'trTable trNormal'];
            }
        },
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
                'detail' => function ($model) {
                    return Yii::$app->controller->renderPartial('_zakaz', ['model'=> $model]);
                },
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
                        return ['class' => 'tr70 '];
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
                        return ['class' => 'tr150 srok'];
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
                'contentOptions' => ['class' => 'tr110'],
                'value' => function($model){
                    return $model->tags != null ?  '<div class="markerOrder">'.$model->getTagsAsString('gridview').'</div>' : false;
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
                        return ['class' => 'tr70'];
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
        ],
    ]); ?>
    </div>
    <div class="col-lg-12">
        <h3 class="titleTable">На исполнении</h3>
    </div>
    <div class="col-lg-12">
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'floatHeader' => true,
        'headerRowOptions' => ['class' => 'headerTable'],
        'pjax' => true,
        'tableOptions'  => ['class' => 'table table-bordered tableSize'],
        'striped' => false,
        'rowOptions' => function($model){
            if ($model->srok < date('Y-m-d H:i:s')) {
                return['class' => 'trTable trTablePass trNormal'];
            } else {
                return['class' => 'trTable srok trNormal'];
            }
        },
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
                'detail'=>function ($model) {
                    return Yii::$app->controller->renderPartial('_zakaz', ['model'=>$model]);
                },
                'enableRowClick' => true,
                'expandOneOnly' => true,
                'expandIcon' => ' ',
                'collapseIcon' => ' ',
            ],
            [
                'attribute' => 'id_zakaz',
                'value' => 'prefics',
                'hAlign' => GridView::ALIGN_RIGHT,
                'contentOptions' => ['class' => 'textTr tr70'],
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
                'value' => 'srok',
                'hAlign' => GridView::ALIGN_RIGHT,
                'contentOptions' => ['class' => 'textTr tr100 srok'],
            ],
            [
                'attribute' => 'minut',
                'hAlign' => GridView::ALIGN_RIGHT,
                'contentOptions' => ['class' => 'textTr tr10'],
                'value' => function($model){
                    if ($model->minut == null){
                        return '';
                    } else {
                        return $model->minut;
                    }
                }
            ],
            [
                'attribute' => 'description',
                'value' => function($model){
                    return StringHelper::truncate($model->description, 100);
                },
            ],
            [
                'attribute' => 'tag',
                'format' => 'raw',
                'contentOptions' => ['class' => 'tr110'],
                'value' => function($model){

                   /* return $model->tags != null ?  '<div class="markerOrder">'. substr($model->getTagsAsString('gridview') , 0, 10) . '...' .'</div>' : false;*/
                    return $model->tags != null ?  '<div class="markerOrder">'.$model->getTagsAsString('gridview').'</div>' : false;
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
                'headerOptions' => ['width' => '70'],
                'value' => 'money',
                'hAlign' => GridView::ALIGN_RIGHT,
                'contentOptions' => ['class' => 'textTr tr70'],
            ],
            [
                'attribute' => 'statusName',
                'label' => 'Отв-ный',
                'contentOptions' => function($model) {
                    if ($model->id_unread == true && $model->srok < date('Y-m-d H:i:s')){
                        return ['class' => 'border-right trNew'];
                    } elseif ($model->id_unread == true && $model->srok > date('Y-m-d H:i:s')){
                        return ['class' => 'border-right success-ispol'];
                    } else {
                        return ['class' => 'border-right textTr'];
                    }
                },
            ],
        ],
    ]); ?>
    </div>
    <div class="col-lg-12">
        <h3 class="titleTable">На закрытие</h3>
    </div>
    <div class="col-lg-12">
        <?= /** @var string $dataProviderIspol */
    GridView::widget([
        'dataProvider' => $dataProviderIspol,
        'floatHeader' => true,
        'headerRowOptions' => ['class' => 'headerTable'],
        'pjax' => true,
        'striped' => false,
        'tableOptions' => ['class' => 'table table-bordered tableSize'],
        'rowOptions' => ['class' => 'trTable srok trNormal'],
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
                'detail'=>function ($model) {
                    return Yii::$app->controller->renderPartial('_zakaz', ['model'=>$model]);
                },
                'enableRowClick' => true,
                'expandOneOnly' => true,
                'expandIcon' => ' ',
                'collapseIcon' => ' ',
            ],
            [
                'attribute' => 'id_zakaz',
                'value' => 'prefics',
                'hAlign' => GridView::ALIGN_RIGHT,
                'contentOptions' => ['class' => 'textTr tr70'],
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
                'value' => 'srok',
                'hAlign' => GridView::ALIGN_RIGHT,
                'contentOptions' => ['class' => 'textTr tr100 srok'],
            ],
            [
                'attribute' => 'minut',
                'hAlign' => GridView::ALIGN_RIGHT,
                'contentOptions' => ['class' => 'textTr tr10'],
                'value' => function($model){
                    if ($model->minut == null){
                        return '';
                    } else {
                        return $model->minut;
                    }
                }
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
                'contentOptions' => ['class' => 'tr110'],
                'value' => function($model){
                    return $model->tags != null ?  '<div class="markerOrder">'.$model->getTagsAsString('gridview').'</div>' : false;
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
                'contentOptions' => ['class' => 'textTr tr70'],
            ],
            [
                'attribute' => '',
                'format' => 'raw',
                'value' => function(){
                    return '';
                },
                'contentOptions' => ['class' => 'textTr border-right tr90'],
            ]
        ],
    ]); ?>
    <?php Pjax::end(); ?>
    </div>
    <?php Modal::begin([
        'id' => 'declinedModal',
        'header' => '<h2>Укажите причину отказа:</h2>',
    ]);
    echo '<div class="modalContent"></div>';
    Modal::end();?>
    <?php Modal::begin([
        'id' => 'acceptdModal',
        'header' => '<h2>Назначить ответственного:</h2>',
    ]);
    echo '<div class="modalContent"></div>';
    Modal::end();?>
    <?php Modal::begin([
        'id' => 'draftModal',
        'header' => '<h2>Оплата услуг</h2>'
    ]);
    echo '<div class="modalContent"></div>';
    Modal::end() ?>
    <?php Modal::begin([
        'options' => [

            'tabindex' => false // important for Select2 to work properly
        ],
        'id' => 'modalShipping',
        'header' => '<h2>Назначить доставку</h2>'
    ]);
    echo '<div class="modalContent"></div>';


    Modal::end(); ?>
    <?php Modal::begin([
        'id' => 'modalReminder',
        'header' => '<h2>Поставить напоминание</h2>'
    ]);
    echo '<div class="modalContent"></div>';
    Modal::end(); ?>
</div>
<div class="footer">
    <?php echo Nav::widget([
        'options' => ['class' => 'nav nav-pills footerNav'],
        'items' => [
            ['label' => 'Архив', 'url' => ['archive'], 'visible' => Yii::$app->user->can('seeAdmin')],
        ],
    ]); ?>
</div>
