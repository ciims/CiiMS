<?php 
$stage = max($ciimsConfig['params']['stage'], isset($_GET['stage']) ? $_GET['stage'] : 0);
$breadCrumbs = array(
    0 => 'Lets Get Started',
    1 => 'Where is Yii Framework At?',
    2 => 'Download Yii',
);

if (isset($_POST['_ajax']) && isset($_POST['_method']))
{
    // Retreive the installHelper 
    require_once dirname(__FILE__) . '/installHelper.php';
    $helper = new InstallHelper;
    $helper->$_POST['_method']($_POST['data']);
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>CiiMS Installer</title>
        <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.0/css/bootstrap-combined.min.css" rel="stylesheet">
        <link href="//netdna.bootstrapcdn.com/font-awesome/3.0.2/css/font-awesome.css" rel="stylesheet">
        <link href="css/install/main.css" rel="stylesheet" />
    </head>
    <body>
        <div class="well well-container">
            <div class="navbar navbar-inverse">
                <div class="navbar-inner">
                    <a class="brand" href="#">CiiMS Installer</a>
                </div>
            </div>
            <ul class="breadcrumb">
                <li class ="active"><?php echo $breadCrumbs[$stage]; ?><span class="divider">></span></li>
            </ul>
            <div class="content">
                <hr />
                <?php if ($stage == 1): ?>
                    <h4>Lets Check for Yii Framework</h4>
                    <p>CiiMS requires Yii Framework to run. In order to proceed with the installation, we need to know the patch where Yii's <strong>framework</strong> folder is located at.</p>
                    
                    <div class="path-field">
                        <input type="text" placeholder="/path/to/yii/framework" class="yii-path" />
                        <a href="#" id="checkYiiPathButton" class="btn btn-inverse pull-right" type="button">Check</a>
                    </div>
                    
                    <div class="clearfix"></div>
                    <hr />
                    <a href="?stage=1" class="btn btn-small btn-info pull-left" type="button">Install Yii For Me</a>
                    <a href="?stage=1" id="proceedButton" class="btn btn-small btn-inverse pull-right disabled" type="button">Proceed</a>
                    <div class="clearfix"></div>
                <?php else: ?>
                    <h4>Thanks for Choosing CiiMS!</h4>
                    <p>This installer will walk you through the installation of CiiMS. This process should only take around 5 minutes, but could take longer depending upon your configuration.</p>
                    
                    <a href="?stage=1" class="btn btn-small btn-inverse pull-right" type="button">Click To Begin</a>
                <?php endif; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js" async="async"></script>
        <script type="text/javascript">
            $("#checkYiiPathButton").click(function(e) {
                e.preventDefault();
                $.post('', { _ajax : true, _method : 'pathExists', data : $('.yii-path').val() }, function(data) {
                    if (data.pathExists == "true")
                    {
                        $("#proceedButton").removeClass("disabled").addClass('btn-success');
                    }
                }) 
            });
        </script>
    </body>
</html>