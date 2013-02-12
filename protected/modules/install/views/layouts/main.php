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
                <li class ="active"><?php echo $this->breadcrumbs[$this->stage]; ?><span class="divider">></span></li>
            </ul>
            <div class="content">
                <hr />
                <?php $this->widget('bootstrap.widgets.TbAlert', array(
                    'block'=>true, // display a larger alert block?
                    'fade'=>true, // use transitions?
                    'closeText'=>'x', // close link text - if set to false, no close link is displayed
                ));
                ?>
                <?php echo $content; ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <?php Yii::app()->clientScript->registerScriptFile('https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js'); ?>
        <?php Yii::app()->clientScript->registerScriptFile('https://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.0/js/bootstrap.min.js'); ?>
    </body>
</html>