<?php

use app\models\Tovar;
use yii\bootstrap\ButtonDropdown;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use kartik\grid\GridView;
use yii\widgets\ActiveForm;
use unclead\multipleinput\TabularInput;
use kartik\widgets\Select2;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CustomSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $models app\models\Custom */

$this->title = 'Все запросы';?>
<div class="custom-index">
    <?php $form = ActiveForm::begin([
        'id' => 'customForm',
        'enableAjaxValidation'      => true,
        'enableClientValidation'    => false,
        'validateOnChange'          => false,
        'validateOnSubmit'          => true,
        'validateOnBlur'            => false,
    ]); ?>
    <div class="custom-formAdop">
        <?= TabularInput::widget([
            'models' => $models,
            'attributeOptions' => [
                'enableAjaxValidation'      => true,
                'enableClientValidation'    => false,
                'validateOnChange'          => false,
                'validateOnSubmit'          => true,
                'validateOnBlur'            => false,
            ],
            'columns' => [
                [
                    'name' => 'id_tovar',
                    'type' => Select2::className(),
                    'title' => 'Товар',
                    'options' => [
                        'data' => ArrayHelper::map(Tovar::find()->all(), 'id', 'name'),
                        'pluginOptions' => [
                            'allowClear' => true
                        ],
                        'options' => ['placeholder' => 'Выберите товар']
                    ]
                ],
                [
                    'name' => 'number',
                    'type' => 'textInput',
                    'title' => 'Кол-во',
                    'options' => [
                        'type' => 'number',
                        'min' => '0'
                    ]
                ],
            ],
        ]) ?>

    </div>
    <div class="form-group">
        <?= Html::submitButton('Создать', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

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
                        'url' => ['zakaz/create'],
                        'visible' => Yii::$app->user->can('seeAdop')
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
                        'url' => 'create'
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
                        'url' => ['helpdesk/create']
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
                        'url' => ['todoist/create'],
                        'visible' => Yii::$app->user->can('admin'),
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
                        'url' => ['courier/create'],
                        'visible' => Yii::$app->user->can('admin'),
                    ],
                ]
            ]
        ]); ?>
    </p>

    <?php Pjax::begin([
        'id' => 'pjax-customAdop'
    ]) ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'floatHeader' => true,
        'headerRowOptions' => ['class' => 'headerTable'],
        'pjax' => true,
        'tableOptions' 	=> ['class' => 'table table-bordered tableSize'],
        'striped' => false,
        'rowOptions' => ['class' => 'trTable srok trNormal'],
        'columns' => [
            [
				'attribute' => 'date',
				'format' => ['datetime', 'php:d M H:i'],
                'hAlign' => GridView::ALIGN_RIGHT,
                'contentOptions' => ['class' => 'border-left textTr tr90', 'style' => 'border:none'],
			],
            [
                'attribute' => 'idTovar.name',
                'contentOptions'=>['style'=>'white-space: normal;'],
            ],
            [
                'attribute' => 'number',
                'hAlign' => GridView::ALIGN_RIGHT,
                'contentOptions' => ['class' => 'textTr tr50'],
                'value' => function($model){
                    return $model->number == null ? '' : $model->number;
                }
            ],
            [
                'attribute' => 'action',
                'value' => function($model){
                    return $model->action == 0 ? 'В процессе' : 'Привезен';
                },
                'contentOptions' => ['class' => 'border-right textTr tr90'],
            ],
//            [
//                'header' => 'Действие',
//                'format' => 'raw',
//                'value' => function($model){
//                    return $model->action == 0 ? Html::a('Привезен', ['brought', 'id' => $model->id]) : '';
//                }
//            ],
        ],
    ]); ?>
    <?php Pjax::end() ?>
</div>