<?php

/** @var yii\web\View $this */
use yii\bootstrap4\Html;
use yii\grid\GridView;
use app\models\Prize;
use app\models\user\Info;

$this->title = 'Home';
?>
<div class="site-index">

    <div class="jumbotron text-center bg-transparent">
        <h1 class="display-4">Try your luck!</h1>
		
		<hr>
		
		<h4>Loyalty bonus</h4>
		<p><?= Info::getBonus() ?></p>
		
		<?= Html::beginForm('/') ?>
			<?= Html::hiddenInput('Prize[type]', $model->generateType()) ?>
			<div class="form-group">
				<p>Click the <?= Html::submitButton('button', ['class' => 'btn btn-sm btn-primary']) ?> and get one random prize :)<p>
			</div>
		<?= Html::endForm() ?>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg">
				<h2>Prize list</h2>
				
				<?= GridView::widget([
					'dataProvider' => $dataProvider,
					'pager' => [
						'class' => 'yii\bootstrap4\LinkPager',
					],
					'columns' => [
						['class' => 'yii\grid\SerialColumn'],

						'id',
						'type',
						'value',

						[
							'class' => 'yii\grid\ActionColumn',
							'template' => '{refuse} {convert} {transfer}',
							'buttons' => [
								'refuse' => function ($url, $model, $key) {
									return Html::a('refuse', $url);
								},
								'convert' => function ($url, $model, $key) {
									return $model->type == Prize::TYPE_MONEY ? Html::a('convert', $url) : null;
								},
								'transfer' => function ($url, $model, $key) {
									return $model->type != Prize::TYPE_THING ? Html::a('transfer', $url) : null;
								},
							],
						],
					],
				]) ?>
			</div>
        </div>

    </div>
</div>
