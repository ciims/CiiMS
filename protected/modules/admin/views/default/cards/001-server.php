<div class="well span6 card">
    <h4>System Information</h4>
    <br />
    <div class="left border-right span6">
        <ul class="nav nav-list">
            <li class="nav-header">Server Information</li>
                <li><strong>PHP Version:</strong> <?php echo phpversion(); ?></li>
                <li><strong>Server:</strong> <?php echo $_SERVER['SERVER_SOFTWARE']; ?></li>
                <li><strong>SQL Server:</strong> <?php echo ucwords(Yii::app()->db->driverName) . ' ' . Yii::app()->db->serverVersion; ?></li>
        </ul>
    </div>
    <div class="right span5">
        <ul class="nav nav-list">
            <li class="nav-header">App Settings</li>
                <li><strong>Yii Version:</strong> <?php echo Yii::getVersion(); ?></li>
                <li><strong>Cii Version:</strong> <?php echo substr_replace(include(dirname(__FILE__).'/../../../../../../VERSION'), '', -1); ?></li>
                <li><strong>Caching:</strong> <?php echo get_class(Yii::app()->cache); ?></li>
        </ul>
    </div>
</div>