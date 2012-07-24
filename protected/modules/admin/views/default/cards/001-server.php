<div class="well span6 card">
	<h4 class="nav-header top">System Information</h4>
	<br />
	<div class="left border-right span6">
		<ul class="nav nav-list">
			<li class="nav-header">Server Information</li>
				<li><strong>PHP Version:</strong> <? echo phpversion(); ?></li>
				<li><strong>Server:</strong> <? echo $_SERVER['SERVER_SOFTWARE']; ?></li>
				<li><strong>SQL Server:</strong> <? echo ucwords(Yii::app()->db->driverName) . ' ' . Yii::app()->db->serverVersion; ?></li>
		</ul>
	</div>
	<div class="right span5">
		<ul class="nav nav-list">
			<li class="nav-header">App Settings</li>
				<li><strong>Yii Version:</strong> <? echo Yii::getVersion(); ?></li>
				<li><strong>Cii Version:</strong> <? echo Yii::app()->params['cii']['version']; ?></li>
				<li><strong>Caching:</strong> <? echo get_class(Yii::app()->cache); ?></li>
		</ul>
	</div>
</div>