<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;


/**
 * ClientSearch represents the model behind the search form about `app\models\Client`.
 */
class ClientSearch extends Client
{
    public $fioClient;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['last_name', 'name', 'patronymic', 'fioClient','phone', 'email', 'address', 'street', 'home'], 'safe'],
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
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Client::find()->indexBy('id')->with(['zakazs']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->setSort([
           'attributes' => [
               'id' => [
                   'default' => SORT_ASC,
               ],
               'fioClient' => [
                   'asc' => ['last_name' => SORT_ASC],
                   'desc' => ['last_name' => SORT_DESC],
                   'label' => 'ФИО',
               ],
               'phone',
               'email',
               'address' => [
                   'asc' => ['street' => SORT_ASC],
                   'desc' => ['street' => SORT_DESC],
                   'label' => 'Адрес',
               ],
           ]
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
        ]);

        $query->andFilterWhere(['like', 'last_name', $this->fioClient])
            ->orFilterWhere(['like', 'name', $this->fioClient])
            ->orFilterWhere(['like', 'patronymic', $this->fioClient])
            ->andFilterWhere(['like', 'phone', $this->phone])
            ->andFilterWhere(['like', 'email', $this->email])
            ->andFilterWhere(['like', 'street', $this->address])
            ->orFilterWhere(['like', 'home', $this->address]);

        return $dataProvider;
    }
}
