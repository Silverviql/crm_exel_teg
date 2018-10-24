<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\db\ActiveRecord;
use app\models\query\ZakazQuery;

/**
 * This is the model class for table "zakaz".
 *
 * @property integer $id_zakaz
 * @property string $srok
 * @property integer $minut
 * @property integer $id_sotrud
 * @property integer $id_shop
 * @property integer $shifts_id
 * @property integer $prioritet
 * @property integer $status
 * @property integer $action
 * @property integer $id_tovar
 * @property integer $oplata
 * @property integer $fact_oplata
 * @property integer $number
 * @property string $data
 * @property string $description
 * @property string $information
 * @property string $img
 * @property string $maket
 * @property integer $time
 * @property integer $id_autsors
 * @property integer $statusDisain
 * @property integer $statusMaster
 * @property string $name
 * @property integer $phone
 * @property string $email
 * @property integer $id_client,
 * @property integer $id_shipping
 * @property string $declined
 * @property integer $id_unread
 * @property string $renouncement
 * @property integer $date_update
 * @property string $date_close
 *
 * @property Comment[] $comments
 * @property Courier[] $couriers
 * @property Financy[] $financies
 * @property Notification[] $notifications
 * @property Todoist[] $todoists
 * @property Client $idClient
 * @property Tovar $idTovar
 * @property User $idSotrud
 * @property Courier $idShipping
 * @property Partners $idAutsors
 * @property Shifts $shifts
 * @property mixed $tags
 * @property mixed $uploadeFile
 * @property mixed $getTagsAsString
 */
class Zakaz extends ActiveRecord
{
    /** @var Zakaz */
    public $file;
    public $search;
    public $tags_array;
    public $srok_date;
    public $srok_time;

    const STATUS_NEW = 0;
    const STATUS_EXECUTE = 1;
    const STATUS_ADOPTED = 2;
    const STATUS_DISAIN = 3;
    const STATUS_SUC_DISAIN = 4;
    const STATUS_REJECT = 5;
    const STATUS_MASTER = 6;
    const STATUS_SUC_MASTER = 7;
    const STATUS_AUTSORS = 8;
    const STATUS_DECLINED_DISAIN = 9;
    const STATUS_DECLINED_MASTER = 10;

    const STATUS_DISAINER_NEW = 0;
    const STATUS_DISAINER_WORK = 1;
    const STATUS_DISAINER_SOGLAS = 2;
    const STATUS_DISAINER_PROCESS = 3;
    const STATUS_DISAINER_DECLINED = 4;

    const STATUS_MASTER_NEW = 0;
    const STATUS_MASTER_WORK = 1;
    const STATUS_MASTER_PROCESS = 2;
    const STATUS_MASTER_DECLINED = 3;

    const SCENARIO_DECLINED = 'declined';
    const SCENARIO_DEFAULT  = 'default';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%zakaz}}';
    }

    public function behaviors()
    {
        return [
            [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['date_update'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['date_update'],
                ]
            ]
        ];
    }

    /**
     * @return array
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_DECLINED => ['declined', 'required'],
            self::SCENARIO_DEFAULT => ['srok', 'number', 'description', 'phone', 'id_sotrud', 'shifts_id', 'id_shop','status', 'id_tovar', 'oplata', 'fact_oplata', 'number', 'id_autsors','statusDisain', 'statusMaster', 'img', 'id_shipping', 'id_tovar', 'id_unread', 'information', 'data', 'prioritet', 'phone', 'email', 'name', 'maket', 'time', 'renouncement', 'date_update','data_close','srok_date','srok_time', 'action'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['number', 'description', 'id_client', 'shifts_id',], 'required', 'on' => self::SCENARIO_DEFAULT],
            ['declined', 'required', 'message' => 'Введите причину отказа', 'on'=> self::SCENARIO_DECLINED],
            [['id_zakaz', 'id_tovar', 'minut', 'time', 'number', 'status', 'action', 'id_sotrud', 'id_shop','phone', 'id_client', 'shifts_id', 'id_shipping' ,'prioritet', 'id_autsors','statusDisain', 'statusMaster', 'id_unread', 'date_update'], 'integer'],
            [['srok', 'data', 'tags_array','date_close','srok_date','srok_time'], 'safe'],
            [['oplata', 'fact_oplata'], 'filter', 'filter' => function($value){
                return str_replace(' ', '', $value);
            }],
            [['oplata', 'fact_oplata'], 'number'],
            [['information', 'search', 'declined'], 'string'],
            ['prioritet', 'default', 'value' => 0],
            ['status', 'default', 'value' => self::STATUS_NEW],
            [['id_sotrud', 'id_shop'], 'default', 'value' => Yii::$app->user->getId()],
            ['data', 'default', 'value' => date('Y-m-d H:i:s')],
            ['id_tovar', 'default', 'value' => 1],
            [['description'], 'string', 'max' => 500],
            ['renouncement','string', 'max' => 250],
            [['email', 'name', 'img', 'maket'],'string', 'max' => 50],
            [['file'], 'file', 'skipOnEmpty' => true],
            [['shifts_id'], 'exist', 'skipOnError' => true, 'targetClass' => Shifts::className(), 'targetAttribute' => ['shifts_id' => 'id']],
            [['id_shop'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['id_shop' => 'id']],
            [['id_autsors'], 'exist', 'skipOnError' => true, 'targetClass' => Partners::className(), 'targetAttribute' => ['id_autsors' => 'id']],
            [['id_sotrud'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['id_sotrud' => 'id']],
            [['id_tovar'], 'exist', 'skipOnError' => true, 'targetClass' => Tovar::className(), 'targetAttribute' => ['id_tovar' => 'id']],
            [['id_shipping'], 'exist', 'skipOnError' => true, 'targetClass' => Courier::className(), 'targetAttribute' => ['id_shipping' => 'id']],
            [['id_client'], 'exist', 'skipOnError' => true, 'targetClass' => Client::className(), 'targetAttribute' => ['id_client' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_zakaz' => '№',
            'srok' => 'Срок',
            'minut' => 'Часы',
            'id_sotrud' => 'Магазин',
            'id_shop' => 'Магазин',
            'shifts_id' => 'Сотрудник',
            'prioritet' => 'Приоритет',
            'status' => 'Этап',
            'id_tovar' => 'Категория',
            'oplata' => 'Всего',
            'fact_oplata' => 'Оплачено',
            'number' => 'Количество',
            'data' => 'Дата принятия',
            'description' => 'Описание',
            'img' => 'Приложение',
            'time' => 'Рекомендуемое время',
            'maket' => 'Макет дизайнера',
            'id_autsors' => 'Id Autsors',
            'statusDisain' => 'Этап',
            'statusMaster' => 'Этап',
            'file' => 'Файл',
            'information' => 'Дополнительная информация',
            'name' => 'Клиент',
            'phone' => 'Телефон',
            'email' => 'Email',
            'id_client' => 'Клиент',
            'id_shipping' => 'Доставка',
            'declined' => 'Причина отказа',
            'id_unread' => 'Id unread',
            'renouncement' => 'Отказано',
            'date_update' => 'Date Update',
            'search' => 'Search',
            'tags_array' => 'Тэги',
            'srok_date' => 'Дата у срока',
            'srok_time' => 'Время у срока',
            'action' => 'Action',
            'date_close' => 'Дата закрытия',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getComments()
    {
        return $this->hasMany(Comment::className(), ['id_zakaz' => 'id_zakaz']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFinancies()
    {
        return $this->hasMany(Financy::className(), ['id_zakaz' => 'id_zakaz']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdSotrud()
    {
        return $this->hasOne(User::className(), ['id' => 'id_sotrud']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdTovar()
    {
        return $this->hasOne(Tovar::className(), ['id' => 'id_tovar']);
    }


    /**
     * @return \yii\db\ActiveQuery
     */
    public function getShifts()
    {
        return $this->hasOne(Shifts::className(), ['id' => 'shifts_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdShipping()
    {
        return $this->hasOne(Courier::className(), ['id' => 'id_shipping']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdClient()
    {
        return $this->hasOne(Client::className(), ['id' => 'id_client']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdAutsors()
    {
        return $this->hasOne(Partners::className(), ['id' => 'id_autsors']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdShop()
    {
        return $this->hasOne(User::className(), ['id' => 'id_shop']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getZakazTag()
    {
        return $this->hasMany(ZakazTag::className(), ['zakaz_id' => 'id_zakaz']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTags()
    {
        return $this->hasMany(Tag::className(), ['id' => 'tag_id'])->via('zakazTag');
    }

    /**
     * @inheritdoc
     * @return ZakazQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new ZakazQuery(get_called_class());
    }

    /**
     * @param $view
     * @return string
     */
    public function  getTagsAsString($view)
    {
        if ($view == 'gridview'){
            $tags = array_slice($this->tags, 0, 5);
            $arr = ArrayHelper::map($tags, 'id', 'name');
        } else {
            $arr = ArrayHelper::map($this->tags, 'id', 'name');
        }
        return implode(', ', $arr);
    }

    /**
     * @return array
     */
    public static function getStatusArray(){
        return [
            self::STATUS_NEW => 'Новый',
            self::STATUS_EXECUTE => 'Исполнен',
            self::STATUS_ADOPTED => 'Принят',
            self::STATUS_DISAIN => 'Дизайнер',
            self::STATUS_SUC_DISAIN => 'Дизайнер',
            self::STATUS_DECLINED_DISAIN => 'Дизайнер',
            self::STATUS_REJECT => 'Отклонен',
            self::STATUS_MASTER => 'Мастер',
            self::STATUS_SUC_MASTER => 'Мастер',
            self::STATUS_DECLINED_MASTER => 'Мастер',
            self::STATUS_AUTSORS => 'Аутсорс',
        ];
    }

    /**
     * @return mixed
     */
    public function getStatusName()
    {
        return ArrayHelper::getValue(self::getStatusArray(), $this->status);
    }
    public static function getPrioritetArray()
    {
        return [
            '0' => '',
            '1' => 'важно',
            '2' => 'очень важно',
        ];
    }

    /**
     * @return mixed
     */
    public function getPrioritetName()
    {
        return ArrayHelper::getValue(self::getPrioritetArray(), $this->prioritet);
    }

    /**
     * @return array
     */
    public static function getStatusDisainArray()
    {
        return [
            self::STATUS_DISAINER_NEW => 'Новый',
            self::STATUS_DISAINER_WORK => 'В работе',
            self::STATUS_DISAINER_SOGLAS => 'Согласование',
            self::STATUS_DISAINER_PROCESS => 'На проверке',
            self::STATUS_DISAINER_DECLINED => 'Отклонен',
        ];
    }

    /**
     * @return mixed
     */
    public function getStatusDisainName()
    {
        return ArrayHelper::getValue(self::getStatusDisainArray(), $this->statusDisain);
    }

    /**
     * @return array
     */
    public static function getStatusMasterArray()
    {
        return [
            self::STATUS_MASTER_NEW => 'Новый',
            self::STATUS_MASTER_WORK => 'В работе',
            self::STATUS_MASTER_PROCESS => 'На проверке',
            self::STATUS_MASTER_DECLINED => 'Отклонен',
        ];
    }

    /**
     * @return mixed
     */
    public function getStatusMasterName()
    {
        return ArrayHelper::getValue(self::getStatusMasterArray(), $this->statusMaster);
    }

    /**
     * Upload file for page 'create' and 'update'
     * @property Zakaz $file
     * @return bool
     */
    public function upload($action, $id = null)
    {
        $year = date('Y');
        $month = date('F');
        if (!is_dir('attachment/' . $year)) {
            mkdir('attachment/' . $year);
        }
        if (!is_dir('attachment/' . $year . '/' . $month)) {
            mkdir('attachment/' . $year . '/' . $month);
        }
        if (!is_dir('attachment/' . $year . '/' . $month.'/customer')) {
            mkdir('attachment/' . $year . '/' . $month.'/customer');
        }
        if ($action == 'create') {
            $this->file->saveAs('attachment/'. $year . '/' . $month . '/customer/' . time() . '.' . $this->file->extension);
            $this->img = time() .'.' . $this->file->extension;
        } else {
            $this->file->saveAs('attachment/' . $year . '/' . $month . '/customer/' . $id . '.' . $this->file->extension);
            $this->img = 'attachment/' . $year . '/' . $month . '/customer/' . $id . '.' . $this->file->extension;
        }
    }

    /**
     * Creates a prefics at the beginning id_zakaz
     * @return int|string
     */
    public function getPrefics()
    {
        $this->id_sotrud != User::USER_ADMIN && $this->id_sotrud != User::USER_DAMIR && User::USER_ALBERT  ? $prefics = $this->idSotrud->username[0] : $prefics = false;
        return $prefics != false ? strtoupper($prefics).'-'.$this->id_zakaz : $this->id_zakaz;
    }

    /**
     * @return string
     */
    public function getMoney()
    {
        return number_format($this->oplata, 0,',', ' ').' р.';
    }

    /**
     * Upload the layout from the designer
     * @return bool
     */
    public function getUploadeFile()
    {
        $year = date('Y');
        $month = date('F');
        if (!is_dir('attachment/' . $year)) {
            mkdir('attachment/' . $year);
        }
        if (!is_dir('attachment/' . $year . '/' . $month)) {
            mkdir('attachment/' . $year . '/' . $month);
        }
        if (!is_dir('attachment/' . $year . '/' . $month.'/layout')) {
            mkdir('attachment/' . $year . '/' . $month.'/layout');
        }
        //Выполнена работа дизайнером
        $this->file->saveAs('attachment/'.$year.'/'.$month.'/layout/Maket_'.$this->id_zakaz.'.'.$this->file->extension);
        $this->maket = 'attachment/'.$year.'/'.$month.'/layout/Maket_'.$this->id_zakaz.'.'.$this->file->extension;
        $this->status = self::STATUS_SUC_DISAIN;
    }

    public function afterFind()
    {
        return $this->tags_array = $this->tags;
    }

    /**
     * edit status, statusIspol and id_unread on certain conditions
     * @param null $status
     * @param null $statusIspol
     * @param null $role
     * @param $id_unread
     */
    public function unread($status = null, $statusIspol = null, $role = null, $id_unread)
    {
        switch ($status){
            case 'suc':
                if ($role == 'disain'){
                    $this->status = self::STATUS_SUC_DISAIN;
                } else {
                    $this->status = self::STATUS_SUC_MASTER;
                }
                break;
            case 'declined':
                if ($role == 'disain'){
                    $this->status = self::STATUS_DECLINED_DISAIN;
                } else {
                    $this->status = self::STATUS_DECLINED_MASTER;
                }
                break;
            case 'execute':
                $this->status = self::STATUS_EXECUTE;
                $this->id_autsors = null;
                break;
        }
        switch ($statusIspol){
            case 'new':
                if ($role == 'disain'){
                    $this->statusDisain = self::STATUS_DISAINER_NEW;
                } else {
                    $this->statusMaster = self::STATUS_MASTER_NEW;
                }
                break;
            case 'suc':
                if ($role == 'disain'){
                    $this->statusDisain = self::STATUS_DISAINER_PROCESS;
                } else {
                    $this->statusMaster = self::STATUS_MASTER_PROCESS;
                }
                break;
            case 'declined':
                if ($role == 'disain'){
                    $this->statusDisain = self::STATUS_DISAINER_DECLINED;
                } else {
                    $this->statusMaster = self::STATUS_MASTER_DECLINED;
                }
                break;
        }
        $this->id_unread = $id_unread;
    }

    public function changedUnread()
    {
        if ($this->status == self::STATUS_DISAIN or $this->status == self::STATUS_MASTER or $this->status == self::STATUS_AUTSORS) {
            if ($this->status == self::STATUS_DISAIN) {
                $this->unread(null, 'new', 'disain',0);
            } elseif ($this->status == self::STATUS_MASTER) {
                $this->unread(null, 'new', 'master',0);
            } else {
                $this->id_unread = 0;
            }
        }
    }
}
