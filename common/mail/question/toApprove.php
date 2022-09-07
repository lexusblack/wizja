<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Question */

$link = Yii::$app->urlManager->createAbsoluteUrl(['question/view', 'id' => $model->id]);
?>
<h1><?= Yii::t('app', 'Pytanie do zatwierdzenia') ?></h1>

<h3><?php echo $model->category->parentCategory->categoryName; ?></h3>
<h4><?php echo $model->category->categoryName; ?></h4>
<div>
	<?php echo $model->question; ?>
	<ul>
		<?php foreach ($model->answers as $answer): ?>
			<li>
				<strong><?php echo $answer->answer; ?></strong>
				<br />
				<?= Yii::t('app', 'Prawidłowe') ?>: <?php echo $answer->correct ? 'Tak' : 'Nie'; ?>
			</li>
		<?php endforeach; ?>
	</ul>
</div>
<?php echo Html::a('Link', $link); ?>
