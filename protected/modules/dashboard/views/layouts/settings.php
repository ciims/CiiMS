<?php $this->beginContent('/layouts/main'); ?>
	<div class="settings-container">
		<div class="sidebar">
			<div class="header">
				<h3>Settings</h3>
				<p>Configure various features and behaviors for your site.</p>
			</div>
			<div id="main" class="">
				<div class="content">
					<?php 

					$theme        		= Cii::getConfig('theme', 'default');
					$displayTheme 		= file_exists(Yii::getPathOfAlias('webroot.themes.' . $theme) . DIRECTORY_SEPARATOR . 'Theme.php');
					$mobileTheme  		= Cii::getConfig('mobileTheme');
					$displayMobileTheme = file_exists(Yii::getPathOfAlias('webroot.themes.' . $mobileTheme) . DIRECTORY_SEPARATOR . 'Theme.php');
					$tabletTheme  		= Cii::getConfig('tabletTheme');
					$displayTabletTheme = file_exists(Yii::getPathOfAlias('webroot.themes.' . $tabletTheme) . DIRECTORY_SEPARATOR . 'Theme.php');

					$this->widget('zii.widgets.CMenu', array(
						'htmlOptions' => array('class' => 'menu'),
						'items' => array(
							array('url' => $this->createUrl('/dashboard/settings'), 'label' => 'General', 'itemOptions' => array('class' => 'icon-gears'), 'active' => ($this->id == 'settings' && $this->action->id == 'index' ? true : false)),
							array('url' => $this->createUrl('/dashboard/users'), 'label' => 'Users', 'itemOptions' => array('class' => 'icon-group'), 'active' => ($this->id == 'users' ? true : false)),
							array('url' => $this->createUrl('/dashboard/categories'), 'label' => 'Categories', 'itemOptions' => array('class' => 'icon-list'), 'active' => ($this->id == 'categories' ? true : false)),
							array('url' => $this->createUrl('/dashboard/settings/analytics'), 'label' => 'Analytics', 'itemOptions' => array('class' => 'icon-bar-chart'), 'active' => ($this->id == 'analytics' || ($this->id == 'settings' && $this->action->id == 'analytics') ? true : false)),
							array('url' => $this->createUrl('/dashboard/settings/appearance'), 'label' => 'Appearance', 'itemOptions' => array('class' => 'icon-eye-open'), 'active' => ($this->id == 'settings' && $this->action->id == 'appearance' ? true : false)),
							array('url' => $this->createUrl('/dashboard/settings/email'), 'label' => 'Email', 'itemOptions' => array('class' => 'icon-envelope-alt'), 'active' => ($this->id == 'settings' && $this->action->id == 'email' ? true : false)),
							array('url' => $this->createUrl('/dashboard/settings/social'), 'label' => 'Social', 'itemOptions' => array('class' => 'icon-twitter'), 'active' => ($this->id == 'settings' && $this->action->id == 'social' ? true : false)),
							array('url' => $this->createUrl('/dashboard/settings/cards'), 'label' => 'Dashboard Cards', 'itemOptions' => array('class' => 'icon-th-large'), 'active' => ($this->id == 'settings' && $this->action->id == 'cards' ? true : false)),
							//array('url' => $this->createUrl('/dashboard/settings/plugins'), 'label' => 'Plugins', 'itemOptions' => array('class' => 'icon-puzzle-piece'), 'active' => ($this->id == 'settings' && $this->action->id == 'plugins' ? true : false)),
							array('url' => $this->createUrl('/dashboard/settings/system'), 'label' => 'System', 'itemOptions' => array('class' => 'icon-cloud'), 'active' => ($this->id == 'settings' && $this->action->id == 'system' ? true : false)),
							array('url' => $this->createUrl('/dashboard/settings/theme'), 'label' => 'Theme', 'itemOptions' => array('style' => $displayTheme ?: 'display: none','class' => 'icon-desktop'), 'active' => ($this->id == 'settings' && $this->action->id == 'theme'  && $this->themeType == 'desktop' ? true : false)),
							array('url' => $this->createUrl('/dashboard/settings/theme/type/mobile'), 'label' => 'Mobile Theme', 'itemOptions' => array('style' => $displayMobileTheme ?: 'display: none','class' => 'icon-mobile-phone'), 'active' => ($this->id == 'settings' && $this->action->id == 'theme' && $this->themeType == 'mobile' ? true : false)),
							array('url' => $this->createUrl('/dashboard/settings/theme/type/tablet'), 'label' => 'Tablet Theme', 'itemOptions' => array('style' => $displayTabletTheme ?: 'display: none','class' => 'icon-tablet'), 'active' => ($this->id == 'settings' && $this->action->id == 'theme'  && $this->themeType == 'tablet' ? true : false))
						)
					)); ?>
				</div>
			</div>
		</div>
		<div class="body-content">
			<?php echo $content; ?>
		</div>
	</div>
<?php $this->endContent(); ?>