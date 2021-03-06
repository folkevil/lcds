<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Flows');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flow-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= \Yii::$app->user->can('setFlows') ? Html::a(Yii::t('app', 'Create Flow'), ['create'], ['class' => 'btn btn-success']) : '' ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'name',
            'description',
            [
                'attribute' => 'parent',
                'value' => 'parent.name',
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'visibleButtons' => [
                    'update' => \Yii::$app->user->can('setFlows'),
                    'delete' => \Yii::$app->user->can('setFlows'),
                ],
            ],
        ],
    ]); ?>
</div>
