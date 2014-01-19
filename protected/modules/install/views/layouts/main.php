<?php
/**
 * @author Charles R. Portwood II <charlesportwoodii@ethreal.net>
 * @package CiiMS https://www.github.com/charlesportwoodii/CiiMS
 * @license MIT License
 * @copyright 2011-2014 Charles R. Portwood II
 *
 * @notice  This file is part of CiiMS, and likely will not function without the necessary CiiMS classes
 */
?>
<!DOCTYPE html>
<html lang="<?php echo Yii::app()->getLanguage(); ?>">
    <head>
        <link href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.min.css" rel="stylesheet">
        <link href="//cdnjs.cloudflare.com/ajax/libs/pure/0.3.0/pure-min.css" rel="stylesheet" type="text/css">
        <?php $asset = Yii::app()->assetManager->publish(YiiBase::getPathOfAlias('application.modules.install.assets'), true, -1, YII_DEBUG); ?>
        <?php Yii::app()->clientScript->registerCssFile($asset . (YII_DEBUG ? '/css/install.css' : '/css/install.min.css')); ?>
        <link href="http://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet" type="text/css">
        <link href="http://fonts.googleapis.com/css?family=Oswald:400,700" rel="stylesheet" type="text/css">
        <script src="//code.jquery.com/jquery-2.0.3.min.js"></script>
        <title><?php echo Yii::t('Install.main', 'CiiMS Installer'); ?></title>
    </head>
    <body>
        <main>
            <?php echo $content; ?>
        </main>
    </body>
</html>