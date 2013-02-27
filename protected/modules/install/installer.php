<?php

$stage = max((isset($ciimsConfig['params']['stage']) ? $ciimsConfig['params']['stage'] : 0), isset($_GET['stage']) ? $_GET['stage'] : 0);
$stage = isset($e) && !empty($e) ? 10 : $stage;
if ($stage == 10)
    header("HTTP/1.0 409 Conflic");

$breadCrumbs = array(
    0 => 'Lets Get Started',
    1 => 'Where is Yii Framework At?',
    2 => 'Install Yii',
    3 => 'Downloading Yii...',
    10 => 'Things to Fix Before Proceeding',
);

if (isset($_POST['_ajax']) && isset($_POST['_method']))
{
    // Retreive the installHelper 
    require_once(dirname(__FILE__) . '/installHelper.php');
    $helper = new InstallHelper;
    if (method_exists($helper, $_POST['_method']))
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
                <?php if ($stage == 1):  // Checks for Yii Path?>
                    <h4><?php echo $breadCrumbs[$stage]; ?></h4>
                    <p>CiiMS requires Yii Framework to run. In order to proceed with the installation, we need to know the patch where Yii's <strong>framework</strong> folder is located at.</p>
                    
                    <div class="path-field">
                        <div class="control-group" style="width: 100%;">
                            <input type="text" placeholder="/path/to/yii/framework" required class="yii-path" />
                            <a href="#" id="checkYiiPathButton" class="btn btn-inverse pull-right" type="button">Check</a>
                        </div>
                    </div>
                    
                    <div class="clearfix"></div>
                    <hr />
                    <a href="?stage=2" class="btn btn-small btn-info pull-left" type="button">Install Yii For Me</a>
                    <a href="?stage=4" id="proceedButton" class="btn btn-small btn-inverse pull-right disabled" type="button">Proceed</a>
                    <div class="clearfix"></div>
                <?php elseif ($stage == 2): // Instructions for Installing Yii Framework within the runtime directory ?>
                    
                    <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
                        <div class="alert alert-error">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <strong>Warning!</strong> CiiMS doesn't have permissions to write to the runtime directory.
                            Please correct this using the information below before continuing.
                        </div>
                    <?php endif; ?>
                    
                    <h4><?php echo $breadCrumbs[$stage]; ?></h4>
                    <p>Don't have Yii installed already? That's OK, CiiMS can download it for you. Before we begin, make sure CiiMS has write permissions to the following directory.</p>
                    
                    <pre><?php echo str_replace('/modules/install', '', dirname(__FILE__) . '/runtime/'); ?></pre>
                    <p></p>
                    <p>You can modify the permissions via FTP or from terminal. On Linux, you can run the following command to correct the permissions.</p>
                    
                    <pre>chmod -R <?php echo str_replace('/modules/install', '', dirname(__FILE__) . '/runtime/'); ?> 777</pre>
                    
                    <a href="?stage=3" class="btn btn-small btn-inverse pull-right" type="button">Begin Download</a>
                <?php elseif ($stage == 3): // Handles the download of Yii
                        if (!is_writeable(str_replace('/modules/install', '', dirname(__FILE__) . '/runtime/')))
                            header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '?stage=2&error=1');
                ?>
                    <div class="alert alert-error" style="display:none;">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <strong>Warning!</strong> There was an an issue downloading and installing Yii Framework. Please verify the permissions are set correctly before attempting again.
                    </div>
                    <h4>Downloading Yii Framework</h4>
                    <p>CiiMS is now downloading Yii Framework. When the download has finished, press "Continue With Installation". The button will appear once Yii Framework is installed.</p>
                    <p style="text-align:center;"><strong>DO NOT LEAVE THIS PAGE UNTIL THE DOWNLOAD HAS COMPLETED.</strong></p>
                    
                    <div class="progress progress-striped active">
                        <div id="progressBar" class="bar" style="width: 1%;"></div>
                    </div> 
                    
                    <div class="clearfix"></div>
                    
                    <a href="?stage=2" class="btn btn-small btn-inverse pull-left" type="button">Review Previous Instructions</a>
                    <a href="?stage=4" id="continueButton" class="btn btn-small btn-inverse pull-right" style="display:none;" type="button">Continue With Installation</a>
                <?php elseif ($stage == 10): ?>
                    <h4>Address Issues Before Continuing</h4>
                    <p>Before CiiMS can continue with the installation, the following issues need to be addressed.</p>
                    <pre><?php echo $e->getMessage(); ?></pre>
                    <p>Most likely the error above is a permission error. You can correct this by making the following <strong>assets</strong>, <strong>runtime</strong> and <strong>config</strong> directories writable.</p>
                    <pre>chmod -R <?php echo str_replace('/modules/install', '', dirname(__FILE__) . '/runtime/'); ?> 777</pre>
                    <pre>chmod -R <?php echo str_replace('/modules/install', '', dirname(__FILE__) . '/config/'); ?> 777</pre>
                    <pre>chmod -R <?php echo str_replace('/protected/modules/install', '', dirname(__FILE__) . '/assets/'); ?> 777</pre>
                    
                    <p>When you have addressed the issue above, refresh the page to continue with the installation.</p>
                <?php else: ?>
                    <h4>Thanks for Choosing CiiMS!</h4>
                    <p>This installer will walk you through the installation of CiiMS. This process should only take around 5 minutes, but could take longer depending upon your configuration.</p>
                    
                    <a href="?stage=1" class="btn btn-small btn-inverse pull-right" type="button">Click To Begin</a>
                <?php endif; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
        <script src="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.0/js/bootstrap.min.js"></script>
        <script type="text/javascript">
            $(".disabled").click(function(e) {
                e.preventDefault();
                return; 
            });
            
            // Checks for Yii Framework
            $("#checkYiiPathButton").click(function(e) {
                e.preventDefault();
                $.post('', { _ajax : true, _method : 'pathExists', data : $('.yii-path').val() }, function(data) {
                    if (data.pathExists)
                    {
                        $(".yii-path").removeClass("inputError").attr('disabled', 'disabled').parent().removeClass("error");
                        $("#checkYiiPathButton").removeClass("btn-inverse").removeClass("btn-danger").addClass("btn-success").addClass("disabled");
                        $("#proceedButton").removeClass("disabled").removeClass("btn-inverse").addClass('btn-success').unbind('click');
                    }
                    else
                    {
                        $(".yii-path").addClass("inputError").parent().addClass("error");
                        $("#checkYiiPathButton").removeClass("btn-inverse").addClass("btn-danger");
                    }
                }) 
            });
            
            // If the user has elected to download Yii to runtime, then execute this
            <?php if ($stage == 3): ?>
                $(document).ready(function() {
                   var progress = 0;
                   // Initiate the download
                   $.post('', 
                        { 
                        	_ajax : true, 
                            _method : 'initYiiDownload', 
                            data : { 
                                runtime : "<?php echo str_replace('/modules/install', '', dirname(__FILE__) . '/runtime/'); ?>",
                                remote : "<?php echo $ciimsConfig['params']['yiiDownloadPath']; ?>",
                                version: "<?php echo $ciimsConfig['params']['yiiVersionPath']; ?>"
                            }
                       },
                       function(data) {
                           if (!data.completed)
                               progress = 200;
                   });
                   
                   interval = setInterval(function() {
                       $.post('', 
                            { 
                            	_ajax : true, 
                                _method : 'checkDownloadProgress', 
                                data : { 
                                    runtime : "<?php echo str_replace('/modules/install', '', dirname(__FILE__) . '/runtime/'); ?>",
                                    remote : "<?php echo $ciimsConfig['params']['yiiDownloadPath']; ?>"
                                }
                           },
                           function(data) {
                                progress = data.progress;
                                $("#progressBar").css('width', progress + '%');
                                if (progress >= 100)
                                {
                                    window.clearInterval(interval);
                                    if (progress == 100)
                                        $("#continueButton").show().removeClass('btn-inverse').addClass('btn-success');
                                    else
                                        $(".alert").slideDown();
                                }
                                
                       });
                   }, 100);
                   
                });
            <?php endif; ?>
        </script>
    </body>
</html>