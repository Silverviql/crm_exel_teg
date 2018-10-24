<?php
namespace app\models;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "client".
 *
 * @property integer $id
 * @property string $last_name
 * @property string $name
 * @property string $patronymic
 * @property string $phone
 * @property string $email
 * @property string $street
 * @property integer $home
 * @property integer $apartment
 *
 * @property Zakaz[] $zakazs
 */
class Client extends ActiveRecord
{
    public $address;

    const SCENARIO_DEFAULT = 'default';
    const SCENARIO_CREATE = 'create';
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'client';
    }
    /**
     * @return array
     */
    public function scenarios()
    {
        return [
            self::SCENARIO_DEFAULT => ['id', 'last_name', 'name', 'patronymic', 'phone', 'email', 'home','street', 'apartment'],
            self::SCENARIO_CREATE => ['id'],
        ];
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'required', 'message' => 'Пожалуйста, заполните поле','on' => self::SCENARIO_CREATE],
            [['name', 'phone'], 'required', 'on' => self::SCENARIO_DEFAULT],
            [['home', 'apartment'], 'integer'],
            [['phone'], 'string'],
            ['phone', 'match', 'pattern' => '/^(8)[(](\d{3})[)](\d{3})[-](\d{2})[-](\d{2})/', 'message' => 'Телефона, должно быть в формате 8(XXX)XXX-XX-XX'],
            [['last_name', 'name', 'patronymic'], 'string', 'max' => 86],
            [['email'], 'string', 'max' => 50],
            [['street'], 'string', 'max' => 100],
            [['phone'], 'unique'],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'last_name' => 'Фамилия',
            'name' => 'Имя',
            'patronymic' => 'Отчество',
            'phone' => 'Телефон',
            'email' => 'Email',
            'street' => 'Улица',
            'home' => 'Дом',
            'apartment' => 'Квартира',
            'fioClient' => 'ФИО',
        ];
    }
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getZakazs()
    {
        return $this->hasMany(Zakaz::className(), ['id_client' => 'id']);
    }

    public function getFioClient()
    {
        return $this->last_name.' '.$this->name.' '.$this->patronymic;
    }
}