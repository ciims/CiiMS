<!DOCTYPE html>
<html>
    <head>
        <title>CiiMS Installer</title>
        <?php Yii::app()->clientScript->registerCssFile('https://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.0/css/bootstrap-combined.min.css'); ?>
        <?php Yii::app()->clientScript->registerCssFile('https://netdna.bootstrapcdn.com/font-awesome/3.0.2/css/font-awesome.css'); ?>
        <?php Yii::app()->clientScript->registerCssFile('css/install/main.css'); ?>
    </head>
    <body>
        <div class="well well-container">
            <div class="navbar navbar-inverse">
                <div class="navbar-inner">
                    <a class="brand" href="#">CiiMS Installer</a>
                </div>
            </div>
            <ul class="breadcrumb">
                <li class ="active">BREADCRUMBS<span class="divider">></span></li>
            </ul>
            <?php echo $content; ?>
            <div class="clearfix"></div>
        </div>
        <?php Yii::app()->clientScript->registerScriptFile('https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js'); ?>
        <?php Yii::app()->clientScript->registerScriptFile('https://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.0/js/bootstrap.min.js'); ?>
    </body>
</html>