<?php $this->beginContent('/layouts/main'); ?>
	<div class="settings-container">
		<div class="sidebar">
			<div class="header">
				<h3><?php echo Yii::t('Dashboard.views', 'Settings'); ?></h3>
			</div>
			<div id="main" class="nano">
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
							array('url' => $this->createUrl('/dashboard/settings'), 'label' => Yii::t('Dashboard.views', 'General'), 'itemOptions' => array('class' => 'fa fa-gears'), 'active' => ($this->id == 'settings' && $this->action->id == 'index' ? true : false)),
							array('url' => $this->createUrl('/dashboard/users'), 'label' => Yii::t('Dashboard.views', 'Users'), 'itemOptions' => array('class' => 'fa fa-group'), 'active' => ($this->id == 'users' ? true : false)),
							array('url' => $this->createUrl('/dashboard/categories'), 'label' => Yii::t('Dashboard.views', 'Categories'), 'itemOptions' => array('class' => 'fa fa-list'), 'active' => ($this->id == 'categories' ? true : false)),
							array('url' => $this->createUrl('/dashboard/settings/analytics'), 'label' => Yii::t('Dashboard.views', 'Analytics'), 'itemOptions' => array('class' => 'fa fa-bar-chart-o'), 'active' => ($this->id == 'analytics' || ($this->id == 'settings' && $this->action->id == 'analytics') ? true : false)),
							array('url' => $this->createUrl('/dashboard/settings/appearance'), 'label' => Yii::t('Dashboard.views', 'Appearance'), 'itemOptions' => array('class' => 'fa fa-eye'), 'active' => ($this->id == 'settings' && $this->action->id == 'appearance' ? true : false)),
							array('url' => $this->createUrl('/dashboard/settings/email'), 'label' => Yii::t('Dashboard.views', 'Email'), 'itemOptions' => array('class' => 'fa fa-envelope-o'), 'active' => ($this->id == 'settings' && $this->action->id == 'email' ? true : false)),
							array('url' => $this->createUrl('/dashboard/settings/social'), 'label' => Yii::t('Dashboard.views', 'Social'), 'itemOptions' => array('class' => 'fa fa-twitter'), 'active' => ($this->id == 'settings' && $this->action->id == 'social' ? true : false)),
							array('url' => $this->createUrl('/dashboard/settings/cards'), 'label' => Yii::t('Dashboard.views', 'Dashboard Cards'), 'itemOptions' => array('class' => 'fa fa-th-large'), 'active' => ($this->id == 'settings' && $this->action->id == 'cards' ? true : false)),
							//array('url' => $this->createUrl('/dashboard/settings/plugins'), 'label' => Yii::t('Dashboard.views', 'Plugins'), 'itemOptions' => array('class' => 'fa fa-puzzle-piece'), 'active' => ($this->id == 'settings' && $this->action->id == 'plugins' ? true : false)),
							array('url' => $this->createUrl('/dashboard/settings/system'), 'label' => Yii::t('Dashboard.views', 'System'), 'itemOptions' => array('class' => 'fa fa-cloud'), 'active' => ($this->id == 'settings' && $this->action->id == 'system' ? true : false)),
							array('url' => $this->createUrl('/dashboard/settings/theme'), 'label' => Yii::t('Dashboard.views', 'Theme'), 'itemOptions' => array('style' => $displayTheme ?: 'display: none','class' => 'fa fa-desktop'), 'active' => ($this->id == 'settings' && $this->action->id == 'theme'  && $this->themeType == 'desktop' ? true : false)),
							array('url' => $this->createUrl('/dashboard/settings/theme/type/mobile'), 'label' => Yii::t('Dashboard.views', 'Mobile Theme'), 'itemOptions' => array('style' => $displayMobileTheme ?: 'display: none','class' => 'fa fa-mobile-phone'), 'active' => ($this->id == 'settings' && $this->action->id == 'theme' && $this->themeType == 'mobile' ? true : false)),
							array('url' => $this->createUrl('/dashboard/settings/theme/type/tablet'), 'label' => Yii::t('Dashboard.views', 'Tablet Theme'), 'itemOptions' => array('style' => $displayTabletTheme ?: 'display: none','class' => 'fa fa-tablet'), 'active' => ($this->id == 'settings' && $this->action->id == 'theme'  && $this->themeType == 'tablet' ? true : false))
						)
					)); ?>
				</div>
			</div>
		</div>
		<div class="body-content">
			<?php echo $content; ?>
		</div>
		<style>
			.fa fa-twitter {
				display: block !important;
			}
		</style>
	</div>
<?php $this->endContent(); ?>