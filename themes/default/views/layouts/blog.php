<?php $this->beginContent('//layouts/main'); ?>
	<div class="span8">
		<?php echo $content; ?>
	</div>
	<div class="span4 sidebar hidden-phone">
		<div class="well">
			<h4>Search</h4>
			<?php echo CHtml::beginForm($this->createUrl('/search'), 'get', array('id' => 'search')); ?>
                <div class="input-append">
                    <?php echo CHtml::textField('q', Cii::get($_GET, 'q', ''), array('type' => 'text', 'style' => 'width: 97%', 'placeholder' => 'Type to search, then press enter')); ?>
                </div>
            <?php echo CHtml::endForm(); ?>
		</div>
		<div class="well">
			<h4>Related Posts</h4>
			<?php $related = Yii::app()->db->createCommand('
				SELECT id, title, slug, comment_count FROM content  WHERE status = 1 AND category_id = ' . $this->params['data']['category_id'] .
				' AND id != ' . $this->params['data']['id'] . ' AND vid = (SELECT MAX(vid) FROM content AS content2 WHERE content2.id = content.id) AND password="" ORDER BY updated DESC LIMIT 5')->queryAll(); ?>
		</div>
		
		<?php

			
			foreach ($related as $k=>$v)
			{
				echo '<li>';
				echo CHtml::link($v['title'], Yii::app()->createUrl('/'.$v['slug']));
				echo '</li>';
			}
	    $addThisExtension = Configuration::model()->findByAttributes(array('key'=>'addThisExtension'));
			if (isset($addThisExtension->value) && $addThisExtension->value == 1): ?>
				<li class="nav-header">Share This</li>
				<?php $this->widget('ext.analytics.EAddThisWidget', 
					array(
						'account'=>Configuration::model()->findByAttributes(array('key'=>'addThisAccount'))->value,
					)); ?>
			<?php endif; ?>
	</div>
<?php $this->endContent(); ?>
