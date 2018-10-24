<?php
namespace frontend\controllers;
use app\models\Comment;
use app\models\Helpdesk;
use app\models\Notice;
use app\models\Todoist;
use app\models\User;
use app\models\Notification;
use app\models\Zakaz;
use frontend\models\Telegram;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;

class CommentController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['todoist'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@']
                    ],
                ],
            ],
        ];
    }
    /*
     * Save comment who came todoist
     * if success redirected [todoist/index]
     * @param $id
     * @return \yii\web\Response
     */
    public function actionTodoist($id)
    {
        $commentForm = new Comment();
        $telegram = new Telegram();
        $todoist = Todoist::findOne($id);
        $user = Yii::$app->user->id;
        $notification = new Notification();
        if ($commentForm->load(Yii::$app->request->post())){
            $commentForm->id_todoist = $id;
            $commentForm->id_user = Yii::$app->user->id;
            if (!$commentForm->save()){
                print_r($commentForm->getErrors());
            } else {
                if ($todoist->id_sotrud_put != $user){                 //уведомление кто назначил задачу
                    $notification->getByIdNotificationComments( '1', $commentForm, $todoist,$zakaz= null);
                    $notification->getSaveNotification();

                  /*  $telegram->message($todoist->id_sotrud_put, 'Задачу '.$commentForm->idTodoist->comment.' прокомментировали '.$commentForm->comment);*/
                } elseif($todoist->id_user != $user){                   //уведомление кому назначена задача
                    $notification->getByIdNotificationComments( '2', $commentForm, $todoist,$zakaz= null);
                    $notification->getSaveNotification();
                   /* $telegram->message($todoist->id_user, 'Задачу '.$commentForm->idTodoist->comment.' прокомментировали '.$commentForm->comment);*/
                }
                if (Yii::$app->user->can('admin')){
                    return $this->redirect(['todoist/index']);
                } else {
                    return $this->redirect(['todoist/shop']);
                }
            };
        }
    }
    public function actionHelpdesk($id)
    {
        $commentForm = new Comment();
        $telegram = new Telegram();
        $helpdesk = Helpdesk::findOne($id);
        $notification = new Notification();
        if ($commentForm->load(Yii::$app->request->post())){
            $commentForm->id_helpdesk = $id;
            $commentForm->id_user = Yii::$app->user->id;
            if (!$commentForm->save()){
                print_r($commentForm->getErrors());
            } else {
                if(Yii::$app->user->id != User::USER_SYSTEM){
                    $notification->getByIdNotificationComments( '4', $commentForm, $id_sotrud_put= null,$zakaz= null);
                    $notification->getSaveNotification();
                   /* $telegram->message($helpdesk->id_user, 'Поломку '.$commentForm->idHelpdesk->commetnt.' прокомментировали '.$commentForm->comment);*/
                } else {
                    $notification->getByIdNotificationComments( '3', $commentForm, $id_sotrud_put= null,$zakaz= null);
                    $notification->getSaveNotification();
                    /*$telegram->message(User::USER_SYSTEM, 'Поломку '.$commentForm->idHelpdesk->commetnt.' прокомментировали '.$commentForm->comment);*/
                }
                return $this->redirect(['helpdesk/index']);
            };
        }
    }
    /**
     * Save comment
     * @return bool
     */
    public function actionZakaz()
    {
        $comment = new Comment();
        $notification = new Notification();
        $idZakaz = Yii::$app->request->post('Comment')['id_zakaz'];
        $zakaz = Zakaz::find()
            ->where(['id_zakaz' => $idZakaz ])
            ->orderBy('id_zakaz DESC')
            ->one();
        $notice = Notice::find()
            ->where(['order_id' => $idZakaz])
            ->orderBy('id DESC')
            ->all();
        if($comment->load(Yii::$app->request->post()) && $comment->validate()) {
            $comment->notice_id = $notice != null ? $notice[0]->id : null;
            if (!$comment->save()){
                print_r($comment->getErrors());
            } else {
                if($zakaz->status == Zakaz::STATUS_MASTER ){
                    if(Yii::$app->user->id != User::USER_ADMIN){
                    $notification->getByIdNotificationComments( '12', $comment, $id_sotrud_put= null,$zakaz);
                    $notification->getSaveNotification();
                    } else{
                        $notification->getByIdNotificationComments( '10', $comment, $id_sotrud_put= null,$zakaz);
                        $notification->getSaveNotification();
                    }
                } else if($zakaz->status == Zakaz::STATUS_DISAIN ) {
                    if(Yii::$app->user->id != User::USER_ADMIN){
                        $notification->getByIdNotificationComments( '12', $comment, $id_sotrud_put= null,$zakaz);
                        $notification->getSaveNotification();
                    } else{
                        $notification->getByIdNotificationComments( '11', $comment, $id_sotrud_put= null,$zakaz);
                        $notification->getSaveNotification();
                    }
                } else {
                    $notification->getByIdNotificationComments( '12', $comment, $id_sotrud_put= null,$zakaz);
                    $notification->getSaveNotification();
                }
                return true;
            };
        }
    }
    /**
     * @param $id
     * @param $offset
     * @return string
     */
    public function actionPagination($id, $offset)
    {
        $offset = $offset * 6;
        $comment = Comment::find()->where(['id_zakaz' => $id])->orderBy('id DESC')->offset($offset)->limit(6)->asArray()->all();
        return json_encode($comment, JSON_UNESCAPED_UNICODE);
    }

    public function actionCreateReminder($id)
    {
        $model = $id;
        $comment = new Comment();
        $notification = new Notification();

        if ($comment->load(Yii::$app->request->post()) && $comment->validate()) {
            $comment->save();//сохранение напоминаниея
            if (!$comment->save()) {
                print_r($comment->getErrors());
            } else {
                /** @var $model \app\models\Zakaz */
                Yii::$app->session->addFlash('update', 'Напоминание успешно создано');
                $notification->getByIdNotificationComments( '13', $comment, $id_sotrud_put= null,$zakaz= null);
                $notification->getSaveNotification();
            }
            return $this->redirect(['zakaz/admin', '#' => $model->id_zakaz]);
        }

        return $this->renderAjax('create-reminder', [
            'model' => $model,
            'comment' => $comment
        ]);
    }

}