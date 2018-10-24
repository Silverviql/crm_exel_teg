<?php

namespace frontend\controllers;

use app\models\Client;
use app\models\Financy;
use app\models\Notice;
use app\models\User;
use app\models\ZakazTag;
use frontend\models\Telegram;
use Yii;
use app\models\Zakaz;
use app\models\Courier;
use app\models\Comment;
use app\models\Notification;
use app\models\ZakazSearch;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\data\ActiveDataProvider;
use yii\web\UploadedFile;



/**
 * ZakazController implements the CRUD actions for Zakaz model.
 */
class ZakazController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => 'yii\filters\VerbFilter',
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
                'class' => 'yii\filters\AccessControl',
                'rules' => [
                    [
                        'actions' => ['index'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['create', 'close', 'renouncement', 'update'],
                        'allow' => true,
                        'roles' => ['shop', 'admin', 'program'],
                    ],
                    [
                        'actions' => ['view'],
                        'allow' => true,
                        'roles' => ['admin', 'disain', 'master', 'program', 'shop', 'zakup', 'system'],
                    ],
                    [
                        'actions' => ['order'],
                        'allow' => true,
                        'roles' => ['manager'],
                    ],
                    [
                        'actions' => ['check', 'master', 'adopmaster'],
                        'allow' => true,
                        'roles' => ['master', 'program'],
                    ],
                    [
                        'actions' => ['uploadedisain', 'disain', 'ready','statusdisain', 'adopdisain', 'reconcilation'],
                        'allow' => true,
                        'roles' => ['disain', 'program'],
                    ],
                    [
                        'actions' => ['restore', 'admin', 'comment','zakaz', 'archive', 'adopted', 'zakazedit'],
                        'allow' => true,
                        'roles' => ['admin', 'program'],
                    ],
                    [
                        'actions' => ['shop', 'closezakaz'],
                        'allow' => true,
                        'roles' => ['shop', 'program'],
                    ],
                    [
                        'actions' => ['courier'],
                        'allow' => true,
                        'roles' => ['courier', 'program'],
                    ],
                    [
                        'actions' => ['declined', 'accept', 'fulfilled'],
                        'allow' => true,
                        'roles' => ['admin']
                    ],
                    [
                        'actions' => ['refusing'],
                        'allow' => true,
                        'roles' => ['seeAdop']
                    ]
                ],
            ],
        ];
    }

    /**
     * Lists all Zakaz models.
     * @return mixed
     */
    public function actionIndex()
    {
        $model = new Zakaz();

        return $this->render('index', [
            'model' => $model,
        ]);
    }

    /**
     * Displays a single Zakaz model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        $reminder = new Notification();
        $commentField = new Comment();
        $comment = Comment::find()->zakaz($id);
        $financy = Financy::find()->view($id);
        $notice = Notice::find()->where(['order_id' => $id])->orderBy('id DESC')->all();

        $dataProvider = new ActiveDataProvider([
            'query' => Courier::find()->select(['date', 'to', 'from', 'commit', 'status'])->where(['id_zakaz' => $id])
        ]);

        return $this->render('view', [
            'model' => $model,
            'reminder' => $reminder,
            'dataProvider' => $dataProvider,
            'comment' => $comment,
            'commentField' => $commentField,
            'notice' => $notice,
            'financy' => $financy,
        ]);
    }

    /**
     * Creates a new Zakaz model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Zakaz();
        $client = new Client();
        $client->scenario = Client::SCENARIO_CREATE;
        $telegram = new Telegram();
        $tag = new ZakazTag();
        $financy = new Financy();
        $notification = new Notification();
        $tomorrow = date("Y-m-d", time() + 86400);

        if ($model->load(Yii::$app->request->post()) && $client->load(Yii::$app->request->post())) {
            if(Yii::$app->request->post('Zakaz')['srok_time'] == null){
                $model->srok = Yii::$app->request->post('Zakaz')['srok_date'].' '.date('H-i-s');;
            }else{
                $model->srok = Yii::$app->request->post('Zakaz')['srok_date'].' '.Yii::$app->request->post('Zakaz')['srok_time'];
            }
            if(Yii::$app->request->post('Zakaz')['srok_date'] == null){
                $model->srok = $tomorrow.' '.Yii::$app->request->post('Zakaz')['srok_time'];
            }else{
                $model->srok = Yii::$app->request->post('Zakaz')['srok_date'].' '.Yii::$app->request->post('Zakaz')['srok_time'];
            }
            if(Yii::$app->request->post('Zakaz')['srok_time'] == null && Yii::$app->request->post('Zakaz')['srok_date'] == null){
                $model->srok = $tomorrow.' '.date('H-i-s');;
            }else{
                $model->srok = Yii::$app->request->post('Zakaz')['srok_date'].' '.Yii::$app->request->post('Zakaz')['srok_time'];
            }

            if (Yii::$app->request->get('id')){
                $model->id_client = ArrayHelper::getValue(Yii::$app->request->get(), 'id');
            } else {
                $model->id_client = ArrayHelper::getValue(Yii::$app->request->post('Client'), 'id');
            }
            $model->id_shop = $model->id_sotrud;
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->file) {
                $model->upload('create');
            }
            $model->changedUnread();
            if ($model->validate() && $client->validate()){
                if (!$model->save()) {
                    print_r($model->getErrors());
                } else {
                    $arr = ArrayHelper::map($model->tags, 'id', 'id');
                    $post = Yii::$app->request->post('Zakaz')['tags_array'];
                    if ($post){
                        $tag->getZakazForm($post, $arr, $model->id_zakaz);
                    }
                    $financy->saveSum($model->fact_oplata, $model->id_zakaz, $model->oplata);
                    Yii::$app->session->addFlash('update', 'Успешно создан заказ '.$model->id_zakaz);
                    if (Yii::$app->user->id != User::USER_ADMIN ){
                        $notification->getByIdNotification(2, $model->id_zakaz);
                        $notification->getSaveNotification();
                    }

                    if($model->status == Zakaz::STATUS_DISAIN){
                        $notification->getByIdNotification(3, $model->id_zakaz);
                        $notification->getSaveNotification();
                       /* $telegram->message(User::USER_DISAYNER, 'Назначен заказ '.$model->prefics.' '.$model->description);*/
                    }
                    if ($model->status == Zakaz::STATUS_MASTER ){
                        $notification->getByIdNotification(4, $model->id_zakaz);
                        $notification->getSaveNotification();
                        /* $telegram->message(User::USER_MASTER, 'Назначен заказ '.$model->prefics.' '.$model->description);*/
                    }

                    /*$telegram->message(User::USER_ADMIN, 'Создан заказ '.$model->prefics.' '.$model->description);*/
                }

                if (Yii::$app->user->can('shop')) {
                    return $this->redirect(['shop']);
                } elseif (Yii::$app->user->can('admin')) {
                    return $this->redirect(['admin']);
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
            'client' => $client,
        ]);
    }

    /**
     * Updates an existing Zakaz model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $client = new Client();
        $client->scenario = Client::SCENARIO_CREATE;
        $user = User::findOne(['id' => User::USER_DISAYNER]);
        $tag = new ZakazTag();
        $telegram = new Telegram();
        $notification = new Notification();
        if ($model->load(Yii::$app->request->post()) && $client->load(Yii::$app->request->post())) {

            $model->id_client = ArrayHelper::getValue(Yii::$app->request->post('Client'), 'id');//Пришел запрос при создание клиента из select 2
            /** Сохранение файла */
            $model->file = UploadedFile::getInstance($model, 'file');
            if (isset($model->file)) {
                $model->upload('update', $id);
            }
            $model->changedUnread();
            /** Сохранение заказ и клиента если прошли валидацию */
            if ($model->validate() && $client->validate()){
                if (!$model->save()) {
                    print_r($model->getErrors());
                } else {
                    /** Сохранение тегов */
                    $arr = ArrayHelper::map($model->tags, 'id', 'id');
                    if (Yii::$app->request->post('Zakaz')['tags_array']){
                        $tag->getZakazForm(Yii::$app->request->post('Zakaz')['tags_array'], $arr, $id);
                    }
                    if($model->status == Zakaz::STATUS_MASTER /*&& $user->telegram_chat_id*/){
                        $notification->getByIdNotification(4, $id);
                        $notification->getSaveNotification();
                        /* $telegram->message(User::USER_MASTER, 'Назначен заказ '.$model->prefics.' '.$model->description);*/
                    }


                    if($model->status == Zakaz::STATUS_DISAIN /*&& $user->telegram_chat_id*/){
                        $notification->getByIdNotification(3, $id);
                        $notification->getSaveNotification();
                      /*  $telegram->message(User::USER_DISAYNER, 'Назначен заказ '.$model->prefics.' '.$model->description);*/
                    }

                    Yii::$app->session->addFlash('update', 'Успешно отредактирован заказ');
                }

                if (Yii::$app->user->can('shop')) {
                    return $this->redirect(['shop']);
                } else {
                    return $this->redirect(['admin']);
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
            'client' => $client,
        ]);
    }

    /**
     * Master fulfilled zakaz
     * if success redirected zakaz/master
     * @param $id
     * @return \yii\web\Response
     */
    public function actionCheck($id)//Мастер выполнил свою работу
    {
        $model = $this->findModel($id);
        $telegram = new Telegram();
        $notification = new Notification();

        $model->unread('suc', 'suc', 'master',true);
        if ($model->save()) {
            $notification->getByIdNotification(8, $id);
            $notification->saveNotification;
           /* $telegram->message(User::USER_ADMIN, 'Мастер выполнил работу '.$model->prefics.' '.$model->description);*/
            Yii::$app->session->addFlash('update', 'Заказ отправлен на проверку');
            return $this->redirect(['master']);
        } else {
            $this->flashErrors($id);
        }
    }

    /**
     * Disain filfilled zakaz
     * @param $id
     * @return string
     */
    public function actionUploadedisain($id)
    {
        $model = $this->findModel($id);
        $telegram = new Telegram();
        $notification = new Notification();

        $model->unread('suc', 'suc', 'disain',true);
        if ($model->load(Yii::$app->request->post())) {
            $model->file = UploadedFile::getInstance($model, 'file');
            //Выполнение работы дизайнером
            if (isset($model->file)) {
                $model->uploadeFile;
            }
            if ($model->save()) {
                Yii::$app->session->addFlash('update', 'Заказ отправлен на проверку');
                $notification->getByIdNotification(5, $id);
                $notification->saveNotification;
                /*$telegram->message(User::USER_ADMIN, 'Дизайнер выполнил работу '.$model->prefics.' '.$model->description);*/
                return $this->redirect(['disain', 'id' => $id]);
            } else {
                $this->flashErrors($id);
            }
        }
        return $this->renderAjax('_upload', [
            'model' => $model
        ]);
    }

    /**
     * When zakaz close Shope or Admin
     * if success then redirected shop or admin
     * @param integer $id
     * @return mixed
     */
    public function actionClose($id)
    {
        $model = $this->findModel($id);
        $model->action = 0;
        $model->date_close = date('Y-m-d H:i:s');
        $notification = new Notification();
        if (!$model->save()) {
            $this->flashErrors($id);
            var_dump($model->getErrors());
        } else {
            $model->save();
            if (Yii::$app->user->id != User::USER_ADMIN ){
                $notification->getByIdNotification(10, $id);
                $notification->saveNotification;
            }
            Yii::$app->session->addFlash('update', 'Заказ успешно закрылся');
        }

        if (Yii::$app->user->can('shop')) {
            return $this->redirect(['shop']);
        } elseif (Yii::$app->user->can('admin')) {
            return $this->redirect(['admin']);
        }
    }

    /**
     * @param $id
     * @return \yii\web\Response
     */
    public function actionRestore($id)
    {
        $model = $this->findModel($id);
        $model->action = 1;
        $model->save();
        Yii::$app->session->addFlash('update', 'Заказ успешно активирован');

        return $this->redirect(['archive']);
    }

    /**
     * New zakaz become in status adopted
     * @param $id
     * @return \yii\web\Response
     */
    public function actionAdopted($id)
    {
        $model = $this->findModel($id);
        $model->status = Zakaz::STATUS_ADOPTED;
        $model->save();
    }

    /**
     * New zakaz become in status wokr for disain
     * @param $id
     * @return \yii\web\Response
     */
    public function actionAdopdisain($id)
    {
        $model = $this->findModel($id);
        $model->statusDisain = Zakaz::STATUS_DISAINER_WORK;
        $model->save();
    }

    /**
     * New zakaz become in status wokr for master
     * @param $id
     * @return \yii\web\Response
     */
    public function actionAdopmaster($id)
    {
        $model = $this->findModel($id);
        $model->statusMaster = Zakaz::STATUS_MASTER_WORK;
        $model->save();
    }

    /**
     * Zakaz fulfilled
     * if success then redirected zakaz/admin
     * @param $id
     * @return \yii\web\Response
     */
    public function actionFulfilled($id)
    {
        $model = $this->findModel($id);
        $model->unread('execute', null, null,0);
        if ($model->save()) {
            Yii::$app->session->addFlash('update', 'Выполнен заказ №'.$model->prefics);
            return $this->redirect(['admin']);
        } else {
            print_r($model->getErrors());
        }
    }

    /**
     * Zakaz the disainer
     * if success then redirected zakaz/disain
     * @param $id
     * @return \yii\web\Response
     */
    public function actionReconcilation($id)
    {
        $model = $this->findModel($id);

        if ($model->statusDisain == Zakaz::STATUS_DISAINER_SOGLAS) {
            $model->statusDisain = Zakaz::STATUS_DISAINER_WORK;
        } else {
            $model->statusDisain = Zakaz::STATUS_DISAINER_SOGLAS;
        }
        if ($model->save()) {
            return $this->redirect(['disain']);
        } else {
            $this->flashErrors($id);
        }
    }

    /**
     * All existing close zakaz in Admin
     * @return string
     */
    public function actionArchive()
    {
        $searchModel = new ZakazSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'archive');

        return $this->render('archive', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /** All close zakaz
     * in shop */
    public function actionClosezakaz()
    {
        $searchModel = new ZakazSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'closeshop');

        return $this->render('closezakaz', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /** All fulfilled disain */
    public function actionReady()
    {
        $searchModel = new ZakazSearch();
        $dataProvider = new ActiveDataProvider([
            'query' => Zakaz::find()->andWhere(['status' => Zakaz::STATUS_SUC_DISAIN, 'action' => 1]),
            'sort' => ['defaultOrder' => ['srok' => SORT_DESC]]
        ]);

        return $this->render('ready', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /** START view role */
    /**
     * All zakaz existing in Shop
     * @return string
     */
    public function actionShop()
    {
        $searchModel = new ZakazSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'shopWork');
        $dataProviderExecute = $searchModel->search(Yii::$app->request->queryParams, 'shopExecute');

        return $this->render('shop', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dataProviderExecute' => $dataProviderExecute,
        ]);
    }

    /**
     * All zakaz existing in Disain
     * @return string
     */
    public function actionDisain()
    {
        $searchModel = new ZakazSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'disain');
        $dataProviderSoglas = $searchModel->search(Yii::$app->request->queryParams, 'disainSoglas');

        return $this->render('disain', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dataProviderSoglas' => $dataProviderSoglas,
        ]);
    }

    /**
     * All zakaz existing in Master
     * @return string
     */
    public function actionMaster()
    {
        $comment = new Comment();
        $searchModel = new ZakazSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'master');
        $dataProviderSoglas = $searchModel->search(Yii::$app->request->queryParams, 'masterSoglas');

        return $this->render('master', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dataProviderSoglas' => $dataProviderSoglas,
            'comment' => $comment,
        ]);
    }

    /**
     * All zakaz existing in Admin
     * @return string|\yii\web\Response
     * windows Admin
     */
    public function actionAdmin()
    {
        $model = new Zakaz();

        $image = $model->img;
        $searchModel = new ZakazSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, 'admin');
        $dataProviderNew = $searchModel->search(Yii::$app->request->queryParams, 'adminNew');
        $dataProviderWork = $searchModel->search(Yii::$app->request->queryParams, 'adminWork');
        $dataProviderIspol = $searchModel->search(Yii::$app->request->queryParams, 'adminIspol');
        $dataProvider  ->sort->defaultOrder['srok']=SORT_ASC;
        $dataProviderNew  ->sort->defaultOrder['srok']=SORT_DESC;
        $dataProviderWork  ->sort->defaultOrder['srok']=SORT_ASC;

        $dataProviderIspol  ->sort->defaultOrder['srok']=SORT_ASC;
        $dataProvider->pagination = false;

        return $this->render('admin', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'dataProviderNew' => $dataProviderNew,
            'dataProviderWork' => $dataProviderWork,
            'dataProviderIspol' => $dataProviderIspol,
            'image' => $image,
        ]);
    }

    public function actionOrder()
    {
        $id = Yii::$app->request->post('expandRowKey');
        $model = $this->findModel($id);

        return $this->renderPartial('order', [
            'model' => $model
        ]);
    }
    /** END view role */
    /** START Block admin in gridview */

    /**
     * Disain internal status zakaz
     * @param $id
     * @return \yii\web\Response
     */
    public function actionStatusdisain($id)
    {
        $model = $this->findModel($id);
        $model->statusDisain = Zakaz::STATUS_DISAINER_WORK;
        $model->save();

        return $this->redirect(['view', 'id' => $model->id_zakaz]);
    }

    /**
     * Indicates the reason refused the order
     * if success redirected for admin on admib view and shop on shop view.
     * And the reason for rejection is send by mail
     */
    public function actionRenouncement($id)
    {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->validate()){
            if (!$model->save()){
                print_r($model->getErrors());
            }
            Yii::$app->mailer->compose()
                ->setFrom('holland.itkzn@gmail.com')
                ->setTo('holland.itkzn@gmail.com')
                ->setSubject('Отказ от клиента')
                ->setTextBody($model->prefics.' '.$model->renouncement)
                ->send();
            if (Yii::$app->user->can('shop')){
                return $this->redirect(['shop']);
            } else {
                return $this->redirect(['admin']);
            }

        }
    }
    /**
     * Zakaz deckined admin and in db setup STATUS_DECLINED_DISAIN or STATUS_DECLINED_MASTER
     * if success then redirected view admin
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionDeclined($id)
    {
        $model = $this->findModel($id);
        $model->scenario = Zakaz::SCENARIO_DECLINED;
        $telegram = new Telegram();
        $notification = new Notification();
        if ($model->status == Zakaz::STATUS_SUC_DISAIN) {
            $user_id = User::USER_DISAYNER;
        } else {
            $user_id = User::USER_MASTER;
        }

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if ($model->status == Zakaz::STATUS_SUC_DISAIN) {
                    $model->unread('declined', 'declined', 'disain', 0);
                } else {
                    $model->unread('declined', 'declined', 'master', 0);
                }
                if (!$model->save()) {
                    $this->flashErrors($id);
                } else {
                    Yii::$app->session->addFlash('update', 'Работа была отклонена!');
                    $notification->getByIdNotification(12, $id);
                    $notification->getSaveNotification();
                   /* $telegram->message($user_id, 'Отклонен заказ ' . $model->prefics . ' По причине: ' . $model->declined);*/
                }
                return $this->redirect(['admin', '#' => $model->id_zakaz]);
            } else {
                return $this->renderAjax('_declined', ['model' => $model]);
            }
        } else {
            return $this->renderAjax('_declined', ['model' => $model]);
        }
    }

    /**
     * * Zakaz accept admin and in appoint
     * if success then redirected view admin
     * @param $id
     * @return string|\yii\web\Response
     */
    public function actionAccept($id)
    {
        $model = $this->findModel($id);
        $telegram = new Telegram();
        $notification = new Notification();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->validate()) {
                if ($model->status == Zakaz::STATUS_DISAIN or $model->status == Zakaz::STATUS_MASTER or $model->status == Zakaz::STATUS_AUTSORS) {
                    if ($model->status == Zakaz::STATUS_DISAIN) {
                        $model->unread(null, 'new', 'disain', 0);
                        $user_id = User::USER_DISAYNER;
                    } elseif ($model->status == Zakaz::STATUS_MASTER) {
                        $model->unread(null, 'new', 'master', 0);
                        $user_id = User::USER_MASTER;
                    } else {
                        $model->id_autsors = null;
                        $model->id_unread = 0;
                    }
                }
                if ($model->save()) {
                    /** @var $user_id \app\models\User */
                    $user = User::findOne(['id' => $user_id]);
                    if($model->status == Zakaz::STATUS_DISAIN && $user->telegram_chat_id){
                        $notification->getByIdNotification(4, $model->id_zakaz);
                        $notification->getSaveNotification();
                        /*$telegram->message($user_id, 'Назначен заказ '.$model->prefics.' '.$model->description);*/
                    }
                    Yii::$app->session->addFlash('update', 'Работа была принята');
                    return $this->redirect(['admin', 'id' => $id]);
                } else {
                    $this->flashErrors($id);
                }
            } else {
                return $this->renderAjax('accept', ['model' => $model]);
            }
        }
        return $this->renderAjax('accept', ['model' => $model]);
    }

    public function actionRefusing($id, $action)
    {
        $model = $this->findModel($id);
        if ($action == 'yes'){
            $model->action = 0;
            $model->save();
            return $this->redirect('admin');
        } else {
            $model->renouncement = null;
            $model->save();
            return $this->redirect(['update', 'id' => $id]);
        }
    }

    /** END Block admin in gridview*/
    /**
     * Finds the Zakaz model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Zakaz the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Zakaz::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    protected function findShipping($id)
    {
        if (($shipping = Courier::findOne($id)) !== null) {
            return $shipping;
        } else {
            throw new NotFoundHttpException("The requested page does not exist.");

        }
    }

    /**
     * @param null $id
     */
    private function flashErrors($id = null)
    {
        /** @var $model \app\models\Zakaz */
        $id == null ? $model = new Zakaz() : $this->findModel($id);
        Yii::$app->session->addFlash('errors', 'Произошла ошибка!');
    }
}
