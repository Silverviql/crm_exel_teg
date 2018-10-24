<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\models\Notification;
use app\models\Shifts;
use frontend\components\Notifications;
use kartik\popover\PopoverX;
use kartik\widgets\Growl;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use frontend\assets\AppAsset;
use yii\bootstrap\Alert;
use frontend\components\Counter;
use yii\widgets\Pjax;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
        <?php $this->registerLinkTag([
        'rel' => 'icon',
        'type' => 'image/x-icon',
        'href' => '/frontend/web/favicon.ico',
    ]);?>
    <?php $notifModel = Notification::find();
    $notifications = $notifModel->where(['id_user' => Yii::$app->user->id, 'active' => true]);
    $this->params['notifications'] = $notifications->all();
    $this->params['counter'] = $notifications->count(); ?>
</head>
<body>
<?php $this->beginBody() ?>
<div class="wrap">
        <div class="container-fixed col-lg-12">
<?php if (!Yii::$app->user->isGuest): ?>
    <div class="logo"></div>
<?php echo '<h1 class="titleMain">'.Html::encode($this->title).'</h1>' ?>

        <?= Counter::widget() ?>

<?php endif ?>
    <?php $counts = '<span class="glyphicon glyphicon-bell" style="font-size:21px"></span><span class="badge pull-right">'.$this->params['count'].'</span>'; ?>
    <?php
    // NavBar::begin([
    //     'brandLabel' => 'Holland',
    //     'brandUrl' => ['/zakaz/index'],
    //     'options' => [
    //         'class' => 'navbar-inverse navbar-fixed-top',
    //     ],
    // ]);
    // // $menuItems = [
    // //     ['label' => 'Home', 'url' => ['/site/index']],
    // //     ['label' => 'About', 'url' => ['/site/about']],
    // //     ['label' => 'Contact', 'url' => ['/site/contact']],
    // // ];
    // if (!Yii::$app->user->isGuest) {
    //     $menuItems[] = ['encode' => false, 'label' => $counts, 'options' => ['id' => 'notification']];
    // }
    // if (Yii::$app->user->isGuest) {
    //     $menuItems[] = ['label' => 'Войти', 'url' => ['/site/login']];
    // } else {
    //     $menuItems[] = '<li>'
    //         . Html::beginForm(['/site/logout'], 'post')
    //         . Html::submitButton(
    //             'Выйти (' . Yii::$app->user->identity->username . ')',
    //             ['class' => 'btn btn-link logout']
    //         )
    //         . Html::endForm()
    //         . '</li>';
    // }
    // echo Nav::widget([
    //     'options' => ['class' => 'navbar-nav navbar-right'],
    //     'items' => $menuItems,
    // ]);
    // NavBar::end();

    if (Yii::$app->user->isGuest) {
        echo '';
    } else {
        PopoverX::begin([
                'header' => '<i class="glyphicon glyphicon-lock"></i>Учетная запись',
                'closeButton' => ['label' => false],
                'placement' => PopoverX::ALIGN_BOTTOM,
                'toggleButton' => ['label'=>'<span>'.Yii::$app->user->identity->name.'</span> <span class="glyphicon glyphicon-off exit"></span>', 'class' => 'btn btn-link logout'],
                ]);
        echo Html::a('Настройки', ['/site/setting', 'id' => Yii::$app->user->identity->getId()]).'<br>';
        echo Html::a('Контакты', ['/personnel/index']).'<br>';
        echo Html::a('Инфорстенд', ['/site/index']).'<br>';

        echo Html::beginForm(['/site/logout'], 'post');
        echo Html::submitButton('Выход '.Html::tag('span', '', ['class' => 'glyphicon glyphicon-lock']), ['class' => 'btn btn-primary']);
        echo Html::endForm();

        PopoverX::end();
            ;
    }
    ?>
    <?= Notifications::widget() ?>
    </div>

<?php if (Yii::$app->user->isGuest): ?>
    <div class="headerLogin">
        <h1>HOLLAND <span>CRM 2.2</span></h1>
        <p>Управление заказами</p>
    </div>
<?php endif ?>

    <div class="container">
        <?= Breadcrumbs::widget([
            'homeLink' => ['label' => 'Главная', 'url' => ['zakaz/index']],
            'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
        ]) ?>
        <?php if (Yii::$app->session->hasFlash('update')) {
            echo Growl::widget([
                'type' => Growl::TYPE_SUCCESS,
                'body' => Yii::$app->session->removeFlash('update'),
            ]);
        } ?>
        <?php if (Yii::$app->session->hasFlash('errors')) {
            echo Growl::widget([
                'type' => Growl::TYPE_DANGER,
                'body' => Yii::$app->session->removeFlash('errors'),
            ]);
        } ?>
        <?php if (!Shifts::find()->Shifts(Yii::$app->user->id)->all() && !Yii::$app->user->isGuest){
            echo Alert::widget(['options' => [
                'class' => 'alert-info'
            ],
                'body' => '<b>Внимание</b>, Начните смену иначе Вы останетесь без зп '.Html::a('Настройки', ['site/setting', 'id' => Yii::$app->user->id])]);
        } ?>
        <?= $content ?>
    </div>
</div>
<?php if (Yii::$app->user->isGuest): ?>
    <footer>
        <div class="footerLogin">
            <?= Html::img(\yii\helpers\Url::to('@web/img/logo.png'), [
                    'data-toggle' => 'tooltip',
                    'title' => 'Logo'
            ]) ?>
            <div>Сеть магазинов</div>
            <div>&copy Holland <?php echo date('Y') ?></div>
        </div>
    </footer>
<?php endif ?>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
