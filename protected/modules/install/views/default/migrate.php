<h3><?php echo Yii::t('Install.main', '{install} and Migrate Database', array('{install}' => CHtml::tag('span', array('class' => 'highlight'), Yii::t('Isntall.main', 'Install')))); ?></h3>

<p>
    <?php echo Yii::t('Install.main', "CiiMS is now installing the database. {{donotleave}} A notification will appear when it's OK to continue.", array(
        '{{donotleave}}' => CHtml::tag('strong', array(), Yii::t('Install.main', 'DO NOT LEAVE THIS PAGE UNTIL THE PROCESS HAS COMPLETED.'))
    )); ?>    
</p>
<hr />
<h3 id="inprogress">
    <?php echo Yii::t('Install.main', 'Database Migration in Progress...'); ?>
</h3>
<div id="done" style="display:none">
    <h3>
        <?php echo Yii::t('Install.main', 'Migration Complete!'); ?>
    </h3>
    <p>
        <?php echo Yii::t('Install.main', '{{horray}} The database has been installed. Press the "Continue" button below to create an admin user.', array(
        '{{horray}}' => CHtml::tag('strong', array('class' => 'highlight'), Yii::t('Install.main', 'Horray!'))
    )); ?>
    </p>
</div>

<div id="error" style="display:none">
    <h3>
        <?php echo Yii::t('Install.main', 'Could Not Complete Migration'); ?>
    </h3>
    <p>
        <?php echo Yii::t('Install.main', '{{ohsnap}} Looks like the database istallations failed failed. Most likely this is an issue with your database connection. Alternatively, you could try running the migrations from the command line.', array(
            '{{ohsnap}}' => CHtml::tag('strong', array('class' => 'highlight'), Yii::t('Install.main', 'Oh Snap!'))
        )); ?>
    </p>
</div>

<div class="progress progress-striped active">
    <div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 1%">
    </div>
</div>

<hr />

<div class="pure-u-1 buttons">
    <?php echo CHtml::link(Yii::t('Install.main', 'Continue'), $this->createUrl('/createadmin'), array('class' => 'pure-button-disabled pure-button pure-button-primary pull-right', 'id' => 'continue-button')); ?>
</div>

<?php Yii::app()->clientScript->registerScript('ajaxMigrate', '
    var width = 1;
    progressInterval = setInterval(function() {
        width+=.5;
        $(".progress-bar").css("width", width + "%");
        if (width >= 99)
            clearInterval(progressInterval);
    }, 100);


    $.post("runmigrations", function(data) {
        // Set the bar to 100%
        width = 100;
        $(".progress-bar").css("width", "100%");
        clearInterval(progressInterval);
        $("#inprogress").hide();
        if (data.migrated)
        {
            $(".progress-bar").removeClass("progress-bar-warning").addClass("progress-bar-success");
            $("#done").show();
            $("#continue-button").removeClass("pure-button-disabled")
        }
        else
        {
            $(".progress-bar").removeClass("progress-bar-warning").addClass("progress-danger");
            $("#error").show();
        }
    }).error(function() {
        $("#inprogress").hide();
        width = 100;
        $(".progress-bar").css("width", "100%");
        clearInterval(progressInterval);
        $(".progress-bar").removeClass("progress-bar-warning").addClass("progress-bar-danger");
        $("#error").show();
    });


',CClientScript::POS_READY); ?>