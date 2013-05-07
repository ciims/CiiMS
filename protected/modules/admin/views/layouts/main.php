<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <title><?php echo CHtml::encode($this->pageTitle); ?></title>
        <?php $asset=Yii::app()->assetManager->publish(dirname(__FILE__).'/../../assets'); ?>
        <?php Yii::app()->clientScript->registerCssFile($asset.'/css/main.css'); ?>
    </head>
    <body>
        <?php $this->widget('bootstrap.widgets.TbNavbar', array(
              'fixed'=>'top',
              'collapse' => true,
              'brand'=>'Admin',
              'brandUrl' => Yii::app()->getBaseUrl(true),
              'items'=>array(
                  array(
                      'class'=>'bootstrap.widgets.TbMenu',
                      'items'=>array_merge($this->main_menu, $this->menu),
                  ),
                  array(
                      'class' => 'bootstrap.widgets.TbMenu',
                      'htmlOptions' => array('class' => 'pull-right'),
                      'items' => array(
                          array(
                            'label' => 'Logout',
                            'icon' => false,
                            'url' => Yii::app()->createUrl('/logout'),
                            'active' => false,
                          )
                      )
                  )
              ),
          )); ?>
          
          <div class="visible-desktop" style="margin-top:60px;"></div>
          <div class="container">
                <div class="row">
                      <div class="span12">
                          <?php $this->widget('bootstrap.widgets.TbAlert', array(
                              'block'=>true,
                              'fade'=>true,
                              'closeText'=>'×',
                          ));?>
                          <?php echo $content; ?>
                      </div>
                </div>
          </div>
    </body>
</html>