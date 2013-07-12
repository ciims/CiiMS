<?php $this->beginContent('/layouts/main'); ?>
	<div class="settings-container">
		<div class="sidebar">
			<div class="header">
				<h3>Settings</h3>
				<p>Configure various features and behaviors for your site.</p>
			</div>
			<?php $this->widget('zii.widgets.CMenu', array(
				'htmlOptions' => array('class' => 'menu'),
				'items' => array(
					array('url' => $this->createUrl('/dashboard/settings'), 'label' => 'General', 'itemOptions' => array('class' => 'icon-gears')),
					array('url' => $this->createUrl('/dashboard/users'), 'label' => 'Users', 'itemOptions' => array('class' => 'icon-group')),
					array('url' => $this->createUrl('/dashboard/categories'), 'label' => 'Categories', 'itemOptions' => array('class' => 'icon-list')),
					array('url' => $this->createUrl('/dashboard/analytics/settings'), 'label' => 'Analytics', 'itemOptions' => array('class' => 'icon-bar-chart')),
					array('url' => $this->createUrl('/dashboard/settings/appearance'), 'label' => 'Appearance', 'itemOptions' => array('class' => 'icon-eye-open')),
					array('url' => $this->createUrl('/dashboard/settings/email'), 'label' => 'Email', 'itemOptions' => array('class' => 'icon-envelope-alt')),
					array('url' => $this->createUrl('/dashboard/settings/social'), 'label' => 'Social', 'itemOptions' => array('class' => 'icon-twitter')),
					array('url' => $this->createUrl('/dashboard/settings/cards'), 'label' => 'Cards & Plugins', 'itemOptions' => array('class' => 'icon-puzzle-piece')),
					array('url' => $this->createUrl('/dashboard/settings/cache'), 'label' => 'System Cache', 'itemOptions' => array('class' => 'icon-cloud')),
				)
			)); ?>
		</div>
		<div class="body-content">
			<?php echo $content; ?>
		</div>
	</div>
	<?php Yii::app()->getClientScript()->registerScript('li-click', '
		$(".menu li").click(function() { 
			window.location = $(this).find("a").attr("href");
		});
	'); ?>
<?php $this->endContent(); ?>