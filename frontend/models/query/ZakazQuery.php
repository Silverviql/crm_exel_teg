<?php

namespace app\models\query;

use app\models\Zakaz;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the ActiveQuery class for [[Zakaz]].
 *
 * @see Zakaz
 */
class ZakazQuery extends ActiveQuery
{
    public function masterGridView()
    {
        return $this->andWhere(['zakaz.status' => [
            Zakaz::STATUS_MASTER,
            Zakaz::STATUS_DECLINED_MASTER
        ], 'zakaz.action' => 1]);
    }

    public function admin()
    {
        return $this->andWhere(['zakaz.status' => [
            Zakaz::STATUS_DISAIN,
            Zakaz::STATUS_MASTER,
            Zakaz::STATUS_AUTSORS,
            Zakaz::STATUS_SUC_MASTER,
            Zakaz::STATUS_SUC_DISAIN,
            Zakaz::STATUS_DECLINED_DISAIN,
            Zakaz::STATUS_DECLINED_MASTER
        ], 'zakaz.action' => 1]);
    }

    public function masterAgreed()
    {
        return $this->andWhere(['zakaz.status' => Zakaz::STATUS_SUC_MASTER, 'zakaz.action' => 1]);
    }

    public function disainerGridView()
    {
        return $this->andWhere([
        'zakaz.status' => [
            Zakaz::STATUS_DISAIN,
            Zakaz::STATUS_DECLINED_DISAIN
        ],
        'statusDisiain' => [
            Zakaz::STATUS_DISAINER_NEW,
            Zakaz::STATUS_DISAINER_WORK,
            Zakaz::STATUS_DISAINER_DECLINED
        ],
        'zakaz.action' => 1]);
    }

    public function disainAgreed()
    {
        return $this->andWhere([
                'zakaz.status' => Zakaz::STATUS_DISAIN,
                'statusDisain' => Zakaz::STATUS_DISAINER_SOGLAS,
                'zakaz.action' => 1
            ])
            ->orWhere(['zakaz.status' => Zakaz::STATUS_SUC_DISAIN, 'zakaz.action' => 1]);
    }

    public function shopWorkGridView()
    {
        return $this->andWhere([
            'id_sotrud' => Yii::$app->user->id,
            'zakaz.action' => 1,
            'zakaz.status' => [
                Zakaz::STATUS_DISAIN,
                Zakaz::STATUS_MASTER,
                Zakaz::STATUS_AUTSORS,
                Zakaz::STATUS_SUC_MASTER,
                Zakaz::STATUS_SUC_DISAIN,
                Zakaz::STATUS_DECLINED_DISAIN,
                Zakaz::STATUS_DECLINED_MASTER,
                Zakaz::STATUS_NEW,
                Zakaz::STATUS_ADOPTED
            ]
        ]);
    }

    public function shopExecute()
    {
        return $this->andWhere(['id_shop' => Yii::$app->user->id, 'zakaz.action' => 1, 'zakaz.status' => Zakaz::STATUS_EXECUTE]);
    }

    public function adminWork()
    {
        return $this->andWhere(['zakaz.status' => [
            Zakaz::STATUS_NEW,
            Zakaz::STATUS_ADOPTED,
            Zakaz::STATUS_REJECT
        ], 'zakaz.action' => 1]);
    }

    public function adminFulfiled()
    {
        return $this->andWhere(['zakaz.status' => Zakaz::STATUS_EXECUTE, 'zakaz.action' => 1]);
    }

    public function managerGridView()
    {
        return $this->andWhere(['<', 'srok', date('Y-m-d H:i:s')])
            ->andWhere(['>', 'oplata', 1000])
            ->andWhere(['zakaz.action' => 1]);
    }

    /**
     * @inheritdoc
     * @return Zakaz[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Zakaz|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
