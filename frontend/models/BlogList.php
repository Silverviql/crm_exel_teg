<?php

namespace app\models;


class BlogList extends \yii\db\ActiveRecord{

    public static function tableName()
    {
        return 'zakaz'; // Имя таблицы в БД в которой хранятся записи блога'
    }


    public static function getAll()
    {
        $data = self::find()->all();
        return $data;

    }

}