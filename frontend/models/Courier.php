<?php

namespace app\models;

use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "courier".
 *
 * @property integer $id
 * @property integer $id_zakaz
 * @property string $date
 * @property string $to
 * @property string $to_name
 * @property string $data_to
 * @property string $from
 * @property string $from_name
 * @property string $data_from
 * @property string $commit
 *
 *
 * @property Zakaz $idZakaz
 * @property Zakaz[] $zakazs
 */
class Courier extends ActiveRecord
{

    public $toYandexMap;
    public $fromYandexMap;

    const DOSTAVKA = 0;
    const RECEIVE = 1;
    const DELIVERED = 2;
    const CANCEL = 3;


    const VOLGOGRADSKAYA = '4';
    const PUSHKINA = '5';
    const SIBERIAN = '6';
    const CHETAUEVA = '7';
    const MARXA = '8';

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'courier';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [[/*'toYandexMap', 'fromYandexMap',*/ 'to', 'from', 'date'], 'required'],
            [['id_zakaz', 'status'], 'integer'],
            [['data_to', 'data_from', 'date'/*, 'toYandexMap', 'fromYandexMap'*/], 'safe'],
            [['commit'], 'string'],
            ['status', 'default', 'value' => 0],
            [['to', 'from'], 'string', 'max' => 50],
            [['to_name', 'from_name'], 'string', 'max' => 86],
            [['id_zakaz'], 'exist', 'skipOnError' => true, 'targetClass' => Zakaz::className(), 'targetAttribute' => ['id_zakaz' => 'id_zakaz']],
        ];
    }
    public static function getStatusDostavka()
    {
        return [
            self::DOSTAVKA => 'Доставка',
            self::RECEIVE => 'Принято',
            self::DELIVERED => 'Доставлено',
        ];
    }
    public function getDostavkaName()
    {
        return ArrayHelper::getValue(self::getStatusDostavka(), $this->status);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_zakaz' => 'Заказ',
			'date' => 'Срок',
            'to' => 'Откуда',
            'to_name' => 'To Name',
            'data_to' => 'Data To',
            'from' => 'Куда',
            'from_name' => 'From Name',
            'data_from' => 'Data From',
            'status' => 'Доставка',
            'commit' => 'Доп. указания',
            'toYandexMap' => 'Откуда',
            'fromYandexMap' => 'Куда'
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getIdZakaz()
    {
        return $this->hasOne(Zakaz::className(), ['id_zakaz' => 'id_zakaz']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getZakazs()
    {
        return $this->hasMany(Zakaz::className(), ['id_shipping' => 'id']);
    }

    /**
     * @param bool $insert
     * @return bool
     */
    public function beforeSave($insert)
    {
        $this->date = date('Y-m-d H:i:s', strtotime($this->date));
        return parent::beforeSave($insert);
    }
}
