<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;


/**
 * ZakazSearch represents the model behind the search form about `app\models\Zakaz`.
 */
class ZakazSearch extends Zakaz
{
    public $search;
    // public $search;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_zakaz', 'id_sotrud', 'id_tovar', 'status'], 'integer'],
            [['srok', 'prioritet', 'data', 'name', 'email', 'phone', 'search', /*'sotrud_name', */'description', 'information'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     * @return ActiveDataProvider
     */
    public function search($params, $role)
    {
        $query = Zakaz::find()
            ->select([
                'zakaz.id_zakaz',
                'srok',
                'data',
                'sotrud.name',
                'id_shop',
                'id_autsors',
                'id_shipping',
                'id_unread',
                'shifts_id',
                'prioritet',
                'zakaz.status',
                'statusDisain',
                'statusMaster',
                'oplata',
                'fact_oplata',
                'number',
                'description',
                'information',
                'img',
                'maket',
                'declined',
                'renouncement',
                'client.name',
                'client.phone',
                'client.email',
                'zakaz.action'
            ])
            ->joinWith(['idClient' => function($q){
                $q->from(['client' => Client::tableName()]);
            }])
            ->joinWith(['idSotrud' => function($q){
                $q->from(['sotrud' => Personnel::tableName()]);
            }])
            ->with(['idShipping', 'idSotrud', 'tags', 'financies', 'idClient', 'shifts.idSotrud', 'idAutsors', 'zakazTag'])
            ->indexBy('id_zakaz');

        // add conditions that should always apply here

        /** @var string $sort */
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => $sort,
            ],
            'pagination' => [
                'pageSize' => 100,
            ]
        ]);

        switch ($role) {
            case 'master':
                $query->masterGridView();
                $sort = ['srok' => SORT_ASC];
                break;
            case 'masterSoglas':
                $query->masterAgreed();
                $sort = ['srok' => SORT_ASC];
                break;
            case 'disain':
                $query->disainerGridView();
                $sort = ['srok' => SORT_ASC];
                break;
            case 'disainSoglas':
                $query->disainAgreed();
                $sort = ['srok' => SORT_ASC];
                break;
            case 'shopWork':
                $query->shopWorkGridView();
                $sort = ['data' => SORT_DESC];
                break;
            case 'shopExecute':
                $query->shopExecute();
                $sort = ['data' => SORT_DESC];
                break;
            case 'admin':
                $query->admin();
                $sort = ['status' => SORT_DESC];
                break;
            case 'adminWork':
                $query->adminWork();
                $sort = ['data' => SORT_DESC];
                break;
            case 'adminIspol':
                $query->adminFulfiled();
                $sort = ['srok' => SORT_DESC];
                break;
            case 'archive':
                $query->andWhere(['action' => 0]);
                $sort = ['data' => SORT_DESC];
                break;
            case 'closeshop':
                $query->andWhere(['id_sotrud' => Yii::$app->user->id, 'zakaz.action' => 0]);
                $sort = ['data' => SORT_DESC];
                break;
            case 'manager':
                $query->managerGridView();
                $sort = ['data' => SORT_DESC];
                break;
        }

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id_zakaz' => $this->id_zakaz,
            'srok' => $this->srok,
            'id_sotrud' => $this->id_sotrud,
            'id_tovar' => $this->id_tovar,
            'oplata' => $this->oplata,
            'data' => $this->data,
            // 'name' => $this->name,
            'email' => $this->email,
        ]);

        if (isset($this->search)) {
            $query/*->andFilterWhere(['like', 'sotrud_name', $this->search])*/
                ->orFilterWhere(['like', 'description', $this->search])
                ->orFilterWhere(['like', 'information', $this->search]);
               /* ->orFilterWhere(['like', 'name', $this->search])*/
        } else {
        $query->andFilterWhere(['like', 'prioritet', $this->prioritet])
            ->andFilterWhere(['like', 'status', $this->status])
          /*  ->andFilterWhere(['like', 'name', $this->name])*/
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'email', $this->email]);
        }

        return $dataProvider;
    }
    public function attributeLabels()
    {
        return [
            'srok' => 'Срок',
            'id_sotrud' => 'Магазин',
            'name' => 'Имя клиента',
            'status' => 'Этап',
            'phone' => 'Телефон',
            'data' => 'Дата принятия заказа',
            'search' => 'Поиск',
        ];
    }
}
