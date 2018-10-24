<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * TodoistSearch represents the model behind the search form about `app\models\Todoist`.
 */
class TodoistSearch extends Todoist
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'id_zakaz'], 'integer'],
            [['srok', 'comment'], 'safe'],
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
    public function search($params, $index)
    {
        $query = Todoist::find()->with(['idZakaz', 'idUser', 'idSotrudPut'])->indexBy('id');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        switch ($index) {
            case 'close':
                $query = $query->where(['activate' => Todoist::CLOSE]);
                break;
            case 'admin-their':
                $query = $query->adminTheir();
                break;
            case 'admin-alien':
                $query = $query->adminAlien();
                break;
            case 'shop-their':
                $query = $query->shopTheir();
                break;
            case 'shop-alien':
                $query = $query->shopAlien();
                break;
            case 'overdue':
                $query = $query->overdue();
        }

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'srok' => $this->srok,
            'id_zakaz' => $this->id_zakaz,
        ]);

        $query->andFilterWhere(['like', 'comment', $this->comment]);

        return $dataProvider;
    }
}
