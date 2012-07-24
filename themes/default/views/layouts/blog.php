<?php $this->beginContent('//layouts/main'); ?>
	 <div class="span3 well">
	 	<ul class="nav nav-list">
			<li class="nav-header">My Account</li>
			<li><?php echo Yii::app()->user->isGuest ? CHtml::link('Login', Yii::app()->createUrl('/login')) : Yii::app()->user->displayName . ' ' . CHtml::link('Logout', Yii::app()->createUrl('/logout')); ?></li>
			
			<li class="nav-header">Related Content</li>
			<?
					$related = Yii::app()->db->createCommand('
						SELECT id, title, slug, comment_count FROM content  WHERE category_id = ' . $this->params['data']['category_id'] .
						' AND id != ' . $this->params['data']['id'] . ' AND vid = (SELECT MAX(vid) FROM content AS content2 WHERE content2.id = content.id) AND password="" ORDER BY updated DESC LIMIT 5')->queryAll();

					
					foreach ($related as $k=>$v)
					{
						echo '<li>';
						echo CHtml::link($v['title'], Yii::app()->createUrl('/'.$v['slug']));
						echo '</li>';
					}
				?>
			
			<?php if (Configuration::model()->findByAttributes(array('key'=>'addThisExtension'))->value == 1): ?>
				<li class="nav-header">Share This</li>
				<?php $this->widget('ext.analytics.EAddThisWidget', 
					array(
						'account'=>Configuration::model()->findByAttributes(array('key'=>'addThisAccount'))->value,
					)); ?>
			<?php endif; ?>
		</ul>
	 </div>
	  <div class="span8 well">
	 	<?php echo $content; ?>
	 </div>
<?php $this->endContent(); ?>