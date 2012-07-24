<?php $this->beginContent('//layouts/main'); ?>
	 <div class="span3 well">
	 	<ul class="nav nav-list">
			<li class="nav-header">My Account</li>
			<li><?php echo Yii::app()->user->isGuest ? CHtml::link('Login', Yii::app()->createUrl('/login')) : Yii::app()->user->displayName . ' ' . CHtml::link('Logout', Yii::app()->createUrl('/logout')); ?></li>
			
			<li class="nav-header">Related Content</li>
				<?
					$categories = Yii::app()->cache->get('categories-listing');
					if ($categories == false)
					{
						$categories = Yii::app()->db->createCommand('SELECT categories.id AS id, categories.name AS name, categories.slug AS slug, COUNT(DISTINCT(content.id)) AS content_count FROM categories LEFT JOIN content ON categories.id = content.category_id WHERE content.type_id = 2 AND content.status = 1 GROUP BY categories.id')->queryAll();
						Yii::app()->cache->set('categories-listing', $categories);							
					}
					
					foreach ($categories as $k=>$v)
					{
						if ($v['name'] != 'Uncategorized')
						{
							echo '<li>';
							echo CHtml::link($v['name'], Yii::app()->createUrl('/'.$v['slug']));
							echo '</li>';
						}	
					}
				?>
		</ul>
	 </div>
	  <div class="span8 well">
	 	<?php echo $content; ?>
	 </div>
<?php $this->endContent(); ?>