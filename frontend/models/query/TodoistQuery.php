<?php

namespace app\models\query;

use app\models\Todoist;
use app\models\User;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[\app\models\Todoist]].
 *
 * @see \app\models\Todoist
 */
class TodoistQuery extends ActiveQuery
{
    public function adminTheir()
    {
        return $this->andWhere([
            'activate' => [
                Todoist::ACTIVE,
                Todoist::COMPLETED,
                Todoist::REJECT
            ],
            'id_sotrud_put' => Yii::$app->user->id
        ]);
    }

    public function adminAlien()
    {
        return $this->andWhere(['<>', 'id_sotrud_put', Yii::$app->user->id])
            ->andWhere(['id_user' => Yii::$app->user->id])
            ->andWhere(['activate' => [
                    Todoist::ACTIVE,
                    Todoist::COMPLETED,
                    Todoist::REJECT
                ]
            ]);
    }

    public function shopTheir()
    {
        return $this->andWhere(['<>', 'id_sotrud_put', User::USER_ADMIN])
            ->andWhere(['id_sotrud_put' => Yii::$app->user->id, 'activate' => [
                Todoist::ACTIVE,
                Todoist::COMPLETED,
                Todoist::REJECT
            ]]);
    }

    public function shopAlien()
    {
        return $this->where(['id_user' => Yii::$app->user->id, 'activate' => [
            Todoist::ACTIVE,
            Todoist::COMPLETED,
            Todoist::REJECT
        ]]);
    }

    public function overdue()
    {
        return $this->andWhere(['<', 'srok', date('Y-m-d')])
            ->andWhere(['activate' => !Todoist::CLOSE]);
    }

    public function managerCountDay($day)
    {
        return $this->andWhere(['>', 'date', date('Y-m-d 00:00:00', strtotime('-'.$day.' day'))])
            ->andWhere(['<', 'date', date('Y-m-d 23:59:59', strtotime('-'.$day.' day'))]);
    }

    public function managerCountExecuteDay($day)
    {
        return $this->andWhere(['>', 'date', date('Y-m-d 00:00:00', strtotime('-'.$day.' day'))])
            ->andWhere(['<', 'date', date('Y-m-d 23:59:59', strtotime('-'.$day.' day'))])
            ->andWhere(['activate' => Todoist::CLOSE]);
    }

    public function getId($id)
    {
        return $this->indexBy('id')->with('idZakaz', 'idUser', 'idSotrudPut')->where(['id' => $id])->one();
    }

    /**
     * @inheritdoc
     * @return \app\models\Todoist[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return \app\models\Todoist|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
