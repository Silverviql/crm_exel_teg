<?php

namespace app\models;

use app\models\query\UserQuery;
use app\models\Shifts;
use yii\helpers\ArrayHelper;


/**
 * This is the model class for table "user".
 *
 * @property integer $id
 * @property string $username
 * @property string $auth_key
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $name
 * @property string $telegram_chat_id
 * @property string $telegram_token
 * @property string $address
 * @property integer $otdel_id
 * @property string $phone
 *
 * @property Zakaz[] $zakazs
 * @property Comment[] $comments
 * @property Comment[] $comments0
 * @property Custom[] $customs
 * @property Helpdesk[] $helpdesks
 * @property Notification[] $notifications
 * @property Shifts[] $shifts
 * @property Todoist[] $todoists
 * @property Todoist[] $todoists0
 * @property Otdel $otdel
 */
class User extends \yii\db\ActiveRecord
{
    const USER_PROGRAM = 1;
    const USER_MOSCOW = 2;
    const USER_MASTER = 3;
    const USER_DISAYNER = 4;
    const USER_ADMIN = 5;
    const USER_PUSHKIN = 6;
    const USER_COURIER = 7;
    const USER_SIBER = 9;
    const USER_ZAKUP = 10;
    const USER_SYSTEM = 11;
    const USER_CHETAEVA = 12;
    const USER_DAMIR = 13;
    const USER_ALBERT = 14;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'auth_key', 'password_hash', 'email', 'created_at', 'updated_at', 'name'], 'required'],
            [['status', 'created_at', 'updated_at'], 'integer'],
            [['phone'], 'number'],
            [['username', 'password_hash', 'password_reset_token', 'email'], 'string', 'max' => 255],
            [['auth_key'], 'string', 'max' => 32],
            [['name', 'telegram_chat_id', 'telegram_token', 'address'], 'string', 'max' => 50],
            [['username'], 'unique'],
            [['email'], 'unique'],
            [['password_reset_token'], 'unique'],
            [['otdel_id'], 'exist', 'skipOnError' => true, 'targetClass' => Otdel::className(), 'targetAttribute' => ['otdel_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'auth_key' => 'Auth Key',
            'password_hash' => 'Password Hash',
            'password_reset_token' => 'Password Reset Token',
            'email' => 'Email',
            'status' => 'Status',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'name' => 'Name',
            'telegram_chat_id' => 'Chat Id',
            'telegram_token' => 'Token',
            'address' => 'Адрес',
            'otdel_id' => 'Отдел',
            'phone' => 'Телефон',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['id_user' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments0()
    {
        return $this->hasMany(Comment::className(), ['sotrud' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCustoms()
    {
        return $this->hasMany(Custom::className(), ['id_user' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHelpdesks()
    {
        return $this->hasMany(Helpdesk::className(), ['id_user' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNotifications()
    {
        return $this->hasMany(Notification::className(), ['id_user' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTodoists()
    {
        return $this->hasMany(Todoist::className(), ['id_sotrud_put' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTodoists0()
    {
        return $this->hasMany(Todoist::className(), ['id_user' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdOtdel()
    {
        return $this->hasOne(Otdel::className(), ['id' => 'otdel_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getZakazs()
    {
        return $this->hasMany(Zakaz::className(), ['id_sotrud' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShifts()
    {
        return $this->hasMany(Shifts::className(), ['id_user' => 'id']);
    }

    /**
     * @inheritdoc
     * @return UserQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new UserQuery(get_called_class());
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShiftSotrud()
    {
        return $this->hasMany(Personnel::className(), ['id' => 'id_sotrud'])->via('shifts');
    }

    public function getPersonnelAsString()
    {
        $arr = Shifts::find()->Shifts($this->id)->all();
        $sotrud = ArrayHelper::map($arr, 'id', 'id_sotrud');
        $arr = [];
        foreach ($sotrud as $key => $id_sotrud){
            $user = Shifts::findOne(['id_sotrud' => $id_sotrud]);
            $user->idSotrud->name;
            $arr[] .= $user->idSotrud->nameSotrud;
        }
        return implode(', ', $arr);
    }
}
