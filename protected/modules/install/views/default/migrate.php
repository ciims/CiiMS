<h4>Install Database Tables and Other Basic Stuff</h4>
    <div class="alert in alert-block fade alert-error" id="error-alert" style="display:none;">
        <strong>Oh snap!</strong> Looks like the database istallations failed failed. Most likely this is an issue with your database connection. You can click the "View Migration Details" button below to full the full details, or you can refresh the page to attempt the migrations again. Alternatively, you could try running the migrations from the command line.
    </div>
    
    <div class="alert in alert-block fade alert-success" id="success-alert" style="display:none;">
        <strong>Horray!</strong> The database has been installed. Press the "Continue" button below to create an admin user.
    </div>
    
    <p>CiiMS is now installing the database. <strong>DO NOT LEAVE THIS PAGE UNTIL THE PROCESS HAS COMPLETED.</strong> A notification will appear when it's OK to continue.</p>
    
    <?php $this->widget('bootstrap.widgets.TbProgress', array(
                'percent'=>1, // the progress
                'striped'=>true,
                'animated'=>true,
            ));
    ?>
    
    <hr />
    <div class="clearfix">
    <?php $this->widget('bootstrap.widgets.TbButton',array(
        'label' => 'View Migration Details',
        'type' => 'inverse',
        'size' => 'small',
        'htmlOptions' => array(
            'class' => 'pull-left',
            'id' => 'migration-details',
            'style' => 'display: none;'
        )
    ));?>
    <?php $this->widget('bootstrap.widgets.TbButton',array(
        'label' => 'Continue',
        'type' => 'inverse',
        'size' => 'small',
        'url' => $this->createUrl('/createadmin'),
        'htmlOptions' => array(
            'class' => 'pull-right disabled',
            'id' => 'continue-button'
        )
    ));?>
    
    <div class="clearfix"></div>
    <div id="details-div" style="display:none; margin-top: 5px;">
        <hr />
        <h4>Output From YIIC</h4>
        <pre id="details-modal-details"></pre>
    </div>
</div>
<?php Yii::app()->clientScript->registerScript('ajaxMigrate', '
    var width = 1;
    progressInterval = setInterval(function() {
        width++;
        $(".bar").css("width", width + "%");
        if (width >= 99)
            clearInterval(progressInterval);
    }, 100);
    
    $.post("runmigrations", function(data) {
        // Set the bar to 100%
        width = 100;
        $(".bar").css("width", "100%");
        clearInterval(progressInterval);
        
        $("#migration-details").show();
        $("#details-modal-details").text(data.details);
        console.log(data);
        if (data.migrated)
        {
            $(".progress").addClass("progress-success");
            $("#success-alert").show();
            $("#continue-button").removeClass("disabled").unbind("click");
        }
        else
        {
            $(".progress").addClass("progress-danger");
            $("#error-alert").show();
        }
    });
    
    $("#migration-details").click(function(e) {
        e.preventDefault();
        $("#details-div").toggle();
    })
', CClientScript::POS_READY); ?>