<?php
use app\models\Client;
use app\models\Partners;
use app\models\Shifts;
use app\models\Tag;
use app\models\User;
use app\models\Zakaz;
use kartik\select2\Select2;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\widgets\ActiveForm;
use kartik\datecontrol\DateControl;
use kartik\file\FileInput;
use yii\widgets\MaskedInput;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $model app\models\Zakaz */
/* @var $form yii\widgets\ActiveForm */
/* @var  $client app\models\Client */
?>

    <div class="zakaz-form">
        <?php $form = ActiveForm::begin([
            'options' => ['enctype' => 'multipart/form-data'],
            'enableClientScript' => false,
            'validateOnBlur' => false,
        ]); ?>

        <div class="col-xs-4 informationZakaz">
            <h3>Информация о заказе</h3>
            <div class="col-xs-8">
                <?= $form->field($model, 'description')->textInput(['maxlength' => true, 'placeholder' => 'Описание', 'class' => 'inputForm'])->label(false) ?>
            </div>
            <div class="col-xs-4">
                <?= $form->field($model, 'number')->textInput(['type'=>'number','min' => '0', 'placeholder' => 'Кол-во', 'class' => 'inputForm'])->label(false) ?>
            </div>
            <div class="col-xs-12">
                <?= $form->field($model, 'information')->textarea(['rows' => 3, 'placeholder' => 'Дополнительная информация'])->label(false) ?>
            </div>
            <div class="col-lg-6">
                <?= $form->field($model, 'file')->widget(FileInput::className(), [
                    'language' => 'ru',
                    'options' => ['multiple' => false],
                    'pluginOptions' => [
                        'theme' => 'explorer',
                        'showCaption' => false,
                        'showRemove' => false,
                        'showUpload' => false,
                        'showPreview' => true,
                        'browseClass' => 'action fileInput',
                        'browseLabel' =>  'Загрузить файл',
                        'previewFileType' => 'any',
                        'maxFileCount' => 1,
                        'maxFileSize' => 25600,
                        'preferIconicPreview' => true,
                        'previewFileIconSettings' => ([
                            'doc' => '<i class="fa fa-file-word-o text-orange"></i>',
                            'docx' => '<i class="fa fa-file-word-o text-orange"></i>',
                            'xls' => '<i class="fa fa-file-excel-o text-orange"></i>',
                            'xlsx' => '<i class="fa fa-file-excel-o text-orange"></i>',
                            'ppt' => '<i class="fa fa-file-powerpoint-o text-orange"></i>',
                            'pptx' => '<i class="fa fa-file-powerpoint-o text-orange"></i>',
                            'pdf' => '<i class="fa fa-file-pdf-o text-orange"></i>',
                            'zip' => '<i class="fa fa-file-archive-o text-orange"></i>',
                            'rar' => '<i class="fa fa-file-archive-o text-orange"></i>',
                            'txt' => '<i class="fa fa-file-text-o text-orange"></i>',
                            'jpg' => '<i class="fa fa-file-photo-o text-orange"></i>',
                            'jpeg' => '<i class="fa fa-file-photo-o text-orange"></i>',
                            'png' => '<i class="fa fa-file-photo-o text-orange"></i>',
                            'gif' => '<i class="fa fa-file-photo-o text-orange"></i>',
                            'cdr' => '<i class="fa fa-file-photo-o text-orange"></i>',
                        ]),
                        'layoutTemplates' => [
                            'preview' => '<div class="file-preview {class}">
                               <div class="{dropClass}">
                               <div class="file-preview-thumbnails">
                                </div>
                                <div class="clearfix"></div>
                                <div class="file-preview-status text-center text-success"></div>
                                <div class="kv-fileinput-error"></div>
                                </div>
                                </div>',
                            'actionDrag' => '<span class="file-drag-handle {dragClass}" title="{dragTitle}">{dragIcon}</span>',
                        ],
                    ]
                ])->label(false) ?>
                <div class="form-group field-zakaz-file">
                    <?php if ($model->img == null) {
                        $fileImg = '';
                    } else {
                        $fileImg = 'Файл: ' . $model->img;
                    }
                    ?>
                    <?= Html::encode($fileImg) ?>
                </div>
            </div>
        </div>

        <div class="col-xs-2 clientZakaz">
            <?php Pjax::begin(['id' => 'pjax-select']) ?>
            <div class="col-xs-12">
                <?php !$model->isNewRecord ? $client->id = $model->id_client : null ?>
                <?php if (Yii::$app->request->get('phone')) {
                    echo $form->field($client, 'id')->widget(Select2::className(), [
                        'data' => [Yii::$app->request->get('phone')],
                        'disabled' => true,
                    ])->label(false);
                } else {
                    echo $form->field($client, 'id')->widget(Select2::className(), [
                        'data' => ArrayHelper::map(Client::find()->all(), 'id', 'phone', 'fioClient'),
                        'options' => ['placeholder' => 'Введите номер телефона'],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'language' => [
                                'noResults' => new JsExpression('function () { return "<button type=\"button\" class=\"btn btn-primary btn-xs createClient\" value=\"'.Url::to(['client/create']).'\">Добавить клиент</button>"; }'),
                            ],
                            'escapeMarkup' => new JsExpression('function (markup) {
        return markup;
    }')
                        ],
                    ])->label(false);
                }?>
            </div>
            <?php Pjax::end() ?>
        </div>

        <div class="col-xs-2 moneyZakaz">
            <h3>Оплата</h3>
            <div class="col-xs-10">
                <?= $form->field($model, 'oplata')->widget(MaskedInput::className(), [
                    'clientOptions' => [
                        'alias' => 'decimal',
                        'groupSeparator' => ' ',
                        'autoGroup' => true,
                    ],
                    'options' => ['placeholder' => 'Cтоимость', 'class' => 'inputWidget-form'],
                ])->label(false) ?>
            </div>
            <div class="col-xs-10">
                <?php if ($model->isNewRecord): ?>
                    <?= $form->field($model, 'fact_oplata')->widget(MaskedInput::className(), [
                        'clientOptions' => [
                            'alias' => 'decimal',
                            'groupSeparator' => ' ',
                            'autoGroup' => true,
                        ],
                        'options' => ['placeholder' => 'Предоплата', 'class' => 'inputWidget-form'],
                    ])->label(false) ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-xs-4 managmentZakaz">
            <h3>Управление</h3>
            <div class="col-xs-10">
                <?= $form->field($model, 'srok_date')->widget(DateControl::className(),
                    [
                        'convertFormat' => true,
                        'type'=>DateControl::FORMAT_DATE,
                        'displayFormat' => 'php:d M Y ',
                        'saveFormat' => 'php:Y-m-d',
                        'widgetOptions' => [
                            'pluginOptions' => [
                                'autoclose' => true,
                            ],
                            'options' => ['placeholder' => 'Дата']
                        ],
                    ])->label(false);?>
            </div>
            <div class="col-xs-10">
                <?= $form->field($model, 'srok_time')->widget(DateControl::className(),
                    [
                        'convertFormat' => true,
                        'type'=>DateControl::FORMAT_TIME,
                        'displayFormat' => 'php:H:i:s',
                        'saveFormat' => 'php:H:i:s',
                        'widgetOptions' => [
                            'pluginOptions' => [
                                'format' => 'php:H:i:s'
                            ],
                            'options' => ['placeholder' => 'Время']
                        ],
                    ])->label(false);?>
            </div>
            <?php if (Yii::$app->user->can('shop')): ?>
                <?= $form->field($model, 'prioritet')
                    ->checkbox([
                        'label' => 'Брак в заказу',
                        'labelOptions' => [
                            'style' => 'padding-left:10px;'
                        ],
                        'style' => [
                            'float' => 'right;'
                        ],

                    ]);
                ?>
            <?php endif ?>
            <?php if (Yii::$app->user->can('admin')): ?>
                <div class="col-xs-10">
                    <?= $form->field($model, 'status')->dropDownList([
                        Zakaz::STATUS_DISAIN => 'Дизайнер',
                        Zakaz::STATUS_MASTER => 'Мастер',
                        Zakaz::STATUS_AUTSORS => 'Аутсорс',
                    ],
                        ['prompt' => 'Назначить'])->label(false);?>
                    <?= $form->field($model, 'id_autsors')->dropDownList(
                        ArrayHelper::map(Partners::find()->all(), 'id', 'name'),
                        [
                            'prompt' => 'Выберите партнера',
                            'id' => 'autsors',
                        ]
                    )->label(false) ?>
                    <?= $form->field($model, 'prioritet')->dropDownList([
                        '1' => 'важно',
                        '2' => 'очень важно'],
                        ['prompt' => 'Выберите приоритет'])->label(false) ?>
                </div>
            <?php endif ?>
            <div class="col-xs-10">
                <?php if ($model->isNewRecord): ?>
                    <?php if (Shifts::find()->Shifts(Yii::$app->user->id)->all() == null) {
                        echo 'Смен не найдены';
                    } else {
                        echo $form->field($model, 'shifts_id')->dropDownList(ArrayHelper::map(Shifts::find()->Shifts(Yii::$app->user->id)->all(), 'id', 'idSotrud.nameSotrud'),
                            [
                                'prompt' => 'Выберите отрудника',
                            ]
                        )->label(false);
                    }?>
                <?php endif; ?>
            </div>
        </div>

        <div class="col-lg-2">
            <?= $form->field($model, 'tags_array')->widget(Select2::className(), [
                'data' => ArrayHelper::map(Tag::find()->all(), 'id', 'name'),
                'language' => 'ru',
                'options' => [
                    'placeholder' => 'Выберите тэг',
                    'multiple' => true
                ],
                'pluginOptions' => [
                    'allowClear' => true,
                    'tags' => true,
                    'maximumInputLength' => 10,
                ],
            ]);
            ?>
            <?php if(!$model->isNewRecord && Yii::$app->user->can('admin')): ?>
                <?= $form->field($model, 'id_shop')->dropDownList(ArrayHelper::map(User::find()
                    ->selectUser()
                    ->all(),
                    'id', 'name'))->label(false) ?>
            <?php endif; ?>
        </div>

        <!--     --><?//= $form->field($model, 'id_sotrud')->hiddenInput(['value' => Yii::$app->user->id])->label(false) ?><!-- -->
        <div class="col-lg-2 submitZakazForm">
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => $model->isNewRecord ? 'action' : 'action']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
<?php Modal::begin([
    'id' => 'modalCreateClient',
    'header' => '<h2>Создать клиента</h2>'
]);
echo '<div class="modalContentClient"></div>';
Modal::end(); ?>