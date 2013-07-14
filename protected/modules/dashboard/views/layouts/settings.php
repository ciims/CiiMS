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
					array('url' => $this->createUrl('/dashboard/settings'), 'label' => 'General', 'itemOptions' => array('class' => 'icon-gears'), 'active' => ($this->id == 'settings' && $this->action->id == 'index' ? true : false)),
					array('url' => $this->createUrl('/dashboard/users'), 'label' => 'Users', 'itemOptions' => array('class' => 'icon-group'), 'active' => ($this->id == 'users' ? true : false)),
					array('url' => $this->createUrl('/dashboard/categories'), 'label' => 'Categories', 'itemOptions' => array('class' => 'icon-list'), 'active' => ($this->id == 'categories' ? true : false)),
					array('url' => $this->createUrl('/dashboard/settings/analytics'), 'label' => 'Analytics', 'itemOptions' => array('class' => 'icon-bar-chart'), 'active' => ($this->id == 'analytics' ? true : false)),
					array('url' => $this->createUrl('/dashboard/settings/appearance'), 'label' => 'Appearance', 'itemOptions' => array('class' => 'icon-eye-open'), 'active' => ($this->id == 'settings' && $this->action->id == 'appearance' ? true : false)),
					array('url' => $this->createUrl('/dashboard/settings/email'), 'label' => 'Email', 'itemOptions' => array('class' => 'icon-envelope-alt'), 'active' => ($this->id == 'settings' && $this->action->id == 'email' ? true : false)),
					array('url' => $this->createUrl('/dashboard/settings/social'), 'label' => 'Social', 'itemOptions' => array('class' => 'icon-twitter'), 'active' => ($this->id == 'settings' && $this->action->id == 'social' ? true : false)),
					array('url' => $this->createUrl('/dashboard/settings/cards'), 'label' => 'Cards & Plugins', 'itemOptions' => array('class' => 'icon-puzzle-piece'), 'active' => ($this->id == 'settings' && $this->action->id == 'cards' ? true : false)),
					array('url' => $this->createUrl('/dashboard/settings/system'), 'label' => 'System', 'itemOptions' => array('class' => 'icon-cloud'), 'active' => ($this->id == 'settings' && $this->action->id == 'system' ? true : false))
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