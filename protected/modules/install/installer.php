<?php
error_reporting(-1);
ini_set('display_errors', true);
require(dirname(__FILE__) . DIRECTORY_SEPARATOR . 'YiiUtilityHelper.php');

$stage = max((isset($ciimsConfig['params']['stage']) ? $ciimsConfig['params']['stage'] : 0), isset($_GET['stage']) ? $_GET['stage'] : 0);
$stage = isset($e) && !empty($e) ? 10 : $stage;
if ($stage == 10)
    header("HTTP/1.0 409 Conflict");

$breadCrumbs = array(
    0  => Yii::t('Install.main', 'Lets Get Started'),
    1  => Yii::t('Install.main', 'Where is Yii Framework At?'),
    2  => Yii::t('Install.main', 'Install Yii'),
    3  => Yii::t('Install.main', 'Downloading Yii...'),
    10 => Yii::t('Install.main', 'Things to Fix Before Proceeding'),
);

if (isset($_POST['_ajax']) && isset($_POST['_method']))
{
    // Retreive the installHelper 
    require(dirname(__FILE__) . DIRECTORY_SEPARATOR.'installHelper.php');
    $helper = new InstallHelper;
    if (method_exists($helper, $_POST['_method']))
        $helper->$_POST['_method']($_POST['data']);
}

?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo Yii::t('Install.main', 'CiiMS Installer'); ?></title>
        <link href="//netdna.bootstrapcdn.com/twitter-bootstrap/2.3.0/css/bootstrap-combined.min.css" rel="stylesheet">
        <link href="//netdna.bootstrapcdn.com/font-awesome/3.0.2/css/font-awesome.css" rel="stylesheet">
        <link href="css/install/main.css" rel="stylesheet" />
    </head>
    <body>
        <div class="well well-container">
            <div class="navbar navbar-inverse">
                <div class="navbar-inner">
                    <a class="brand" href="#"><?php echo Yii::t('Install.main', 'CiiMS Installer'); ?></a>
                </div>
            </div>
            <ul class="breadcrumb">
                <li class ="active"><?php echo $breadCrumbs[$stage]; ?><span class="divider">></span></li>
            </ul>
            <div class="content">
                <hr />
                <?php if ($stage == 1):  // Checks for Yii Path?>
                    <h4><?php echo $breadCrumbs[$stage]; ?></h4>
                    <p>
                        <?php echo Yii::t('Install.main', "CiiMS requires Yii Framework to run. In order to proceed with the installation, we need to know the patch where Yii's {{framework}} folder is located at.", array(
                            '{{framework}}' => Yii::tag('strong', array(), Yii::t('Install.main', 'framework'))
                        )); ?>
                    </p>
                    
                    <div class="path-field">
                        <div class="control-group" style="width: 100%;">
                            <input type="text" placeholder="/path/to/yii/framework" required class="yii-path" />
                            <a href="#" id="checkYiiPathButton" class="btn btn-inverse pull-right" type="button"><?php echo Yii::t('Install.main', 'Check'); ?></a>
                        </div>
                    </div>
                    
                    <div class="clearfix"></div>
                    <hr />
                    <a href="?stage=2" class="btn btn-small btn-info pull-left" type="button"><?php echo Yii::t('Install.main', 'Install Yii for Me'); ?></a>
                    <a href="?stage=4" id="proceedButton" class="btn btn-small btn-inverse pull-right disabled" type="button"><?php echo Yii::t('Install.main', 'Proceed'); ?></a>
                    <div class="clearfix"></div>
                <?php elseif ($stage == 2): // Instructions for Installing Yii Framework within the runtime directory ?>
                    
                    <?php if (isset($_GET['error']) && $_GET['error'] == 1): ?>
                        <div class="alert alert-error">
                            <button type="button" class="close" data-dismiss="alert">&times;</button>
                            <?php echo Yii::t('Install.main', "{{warning}} CiiMS doesn't have permissions to write to the runtime directory. Please correct this using the information below before continuing.", array(
                            '{{warning}}' => Yii::tag('strong', array(), Yii::t('Install.main', 'Warning!'))
                        )); ?>
                        </div>
                    <?php endif; ?>
                    
                    <h4><?php echo $breadCrumbs[$stage]; ?></h4>
                    <p> <?php echo Yii::t('Install.main', "Don't have Yii installed already? That's OK, CiiMS can download it for you. Before we begin, make sure CiiMS has write permissions to the following directory."); ?></p>
                    
                    <pre><?php echo str_replace('/modules/install', '', dirname(__FILE__) . '/runtime/'); ?></pre>
                    <p></p>
                    <p> <?php echo Yii::t('Install.main', 'You can modify the permissions via FTP or from terminal. On Linux, you can run the following command to correct the permissions.'); ?></p>
                    
                    <pre>chmod -R 777 <?php echo str_replace('/modules/install', '', dirname(__FILE__) . '/runtime/'); ?></pre>
                    
                    <a href="?stage=3" class="btn btn-small btn-inverse pull-right" type="button"> <?php echo Yii::t('Install.main', 'Begin Download'); ?></a>
                <?php elseif ($stage == 3): // Handles the download of Yii
                        if (!is_writeable(str_replace('/modules/install', '', dirname(__FILE__) . '/runtime/')))
                            header('Location: ' . 'http://' . $_SERVER['SERVER_NAME'] . '?stage=2&error=1');
                ?>
                    <div class="alert alert-error" style="display:none;">
                        <button type="button" class="close" data-dismiss="alert">&times;</button>
                        <?php echo Yii::t('Install.main', '{{warning}} There was an an issue downloading and installing Yii Framework. Please verify the permissions are set correctly before attempting again.', array(
                            '{{warning}}' => Yii::tag('strong', array(), Yii::t('Install.main', 'Warning!'))
                        )); ?>
                    </div>
                    <h4><?php echo Yii::t('Install.main', 'Downloading Yii Framework'); ?></h4>
                    <p><?php echo Yii::t('Install.main', 'CiiMS is now downloading Yii Framework. When the download has finished, press "Continue With Installation". The button will appear once Yii Framework is installed.'); ?></p>
                    <p style="text-align:center;"><strong><?php echo Yii::t('Install.main', 'DO NOT LEAVE THIS PAGE UNTIL THE DOWNLOAD HAS COMPLETED.'); ?></strong></p>
                    
                    <div class="progress progress-striped active">
                        <div id="progressBar" class="bar" style="width: 1%;"></div>
                    </div> 
                    
                    <div class="clearfix"></div>
                    
                    <a href="?stage=2" class="btn btn-small btn-inverse pull-left" type="button"><?php echo Yii::t('Install.main', 'Review Previous Instructions'); ?></a>
                    <a href="?stage=4" id="continueButton" class="btn btn-small btn-inverse pull-right" style="display:none;" type="button"><?php echo Yii::t('Install.main', 'Continue With Installation'); ?></a>
                <?php elseif ($stage == 10): ?>
                    <h4><?php echo Yii::t('Install.main', 'Address Issues Before Continuing'); ?></h4>
                    <p><?php echo Yii::t('Install.main', 'Before CiiMS can continue with the installation, the following issues need to be addressed.'); ?></p>
                    <pre><?php echo $e->getMessage(); ?></pre>
                    <p>
                        <?php echo Yii::t('Install.main', "Most likely the error above is a permission error. You can correct this by making the following {{assets}, {{runtime}} and {{config}} directories writable.", array(
                            '{{assets}}' => Yii::tag('strong', array(), 'assets'),
                            '{{runtime}}' => Yii::tag('strong', array(), 'runtime'),
                            '{{config}}' => Yii::tag('strong', array(), 'config')
                        )); ?>
                    </p>
                    <pre>chmod -R 777 <?php echo str_replace('/modules/install', '', dirname(__FILE__) . '/runtime/'); ?></pre>
                    <pre>chmod -R 777 <?php echo str_replace('/modules/install', '', dirname(__FILE__) . '/config/'); ?></pre>
                    <pre>chmod -R 777 <?php echo str_replace('/protected/modules/install', '', dirname(__FILE__) . '/assets/'); ?></pre>
                    
                    <p><?php echo Yii::t('Install.main', 'When you have addressed the issue above, refresh the page to continue with the installation.'); ?></p>
                <?php else: ?>
                    <h4><?php echo Yii::t('Install.main', 'Thanks for choosing CiiMS!'); ?></h4>
                    <p><?php echo Yii::t('Install.main', 'This installer will walk you through the installation of CiiMS. This process should only take around 5 minutes, but could take longer depending upon your configuration.'); ?></p>
                    
                    <a href="?stage=1" class="btn btn-small btn-inverse pull-right" type="button"><?php echo Yii::t('Install.main', 'Click To Begin'); ?></a>
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
                   progress = 0;
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
                            progress = 100;
                            $("#progressBar").css('width', progress + '%');
                            if (!data.completed)
                                $(".alert-error").slideDown();
                            else 
                                $("#continueButton").show().removeClass('btn-inverse').addClass('btn-success');
                            
                            window.clearInterval(interval);
                   });
                   
                   interval = setInterval(function() {
                      progress++;
                      if (progress >= 98)
                        progress = 98;
                      $("#progressBar").css('width', progress + '%');
                   }, 200);
                   
                });
            <?php endif; ?>
        </script>
    </body>
</html>