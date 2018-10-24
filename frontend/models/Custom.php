<?php

namespace app\models;

use app\models\query\CustomQuery;
use Yii;

/**
 * This is the model class for table "custom".
 *
 * @property integer $id
 * @property integer $id_user
 * @property string $tovar
 * @property string $id_tovar
 * @property integer $number
 * @property string $date
 * @property integer $action
 * @property string $date_end
 *
 * @property User $idUser
 * @property Tovar $idTovar
 */
class Custom extends \yii\db\ActiveRecord
{
    const CUSTOM_NEW = 0;
    const CUSTOM_BROUGHT = 1;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'custom';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_tovar', 'number'], 'required'],
            [['id_user', 'number', 'action', 'id_tovar'], 'integer'],
            ['id_user', 'default', 'value' => Yii::$app->user->getId()],
            ['action', 'default', 'value' => 0],
            [['tovar'], 'string', 'max' => 50],
            [['date', 'date_end'], 'safe'],
            [['id_user'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['id_user' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_user' => 'Магазин',
            'tovar' => 'Товар',
            'id_tovar' => 'Товар',
            'number' => 'Кол-во',
            'date' => 'Дата',
            'action' => 'Статус',
            'date_end' => 'Date_end,',
        ];
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
    public function getIdUser()
    {
        return $this->hasOne(User::className(), ['id' => 'id_user']);
    }

    /**
     * @inheritdoc
     * @return CustomQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new CustomQuery(get_called_class());
    }
}
