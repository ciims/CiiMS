<div class="form">
    <div class="header">
        <div class="pull-left">
            <p><?php echo $header['h3']; ?></p>
        </div>
        <form class="pure-form pull-right header-form">
            <span class="fa fa-search pull-right icon-legend"></span>
            <input id="text" name="text" class="pull-right pure-input pure-search pure-search-alt" placeholder="<?php echo Yii::t('Dashboard.views', 'Search for Themes...'); ?>" type="text">
        </form>
        <div class="clearfix"></div>
    </div>

    <div id="main" class="nano pure-form pure-form-aligned">
        <div class="content pure-form">

        <!-- Carousel Slider for Cards -->
            <div class="carousel-container">
                <a href="#" class="jcarousel-control-prev">&lsaquo;</a>
                    <div class="jcarousel-wrapper">
                        <div class="jcarousel">
                            <div class="loading"><?php echo CHtml::image($this->asset . '/jcarousel-master/carousel-preloader.gif'); ?></div>
                        </div>
                    </div>
                <a href="#" class="jcarousel-control-next">&rsaquo;</a>
            </div>
            <div class="clearfix"></div>

            <legend><?php echo Yii::t('Dashboard.views', 'Uninstalled Themes'); ?></legend>
            <div class="meta-container" id="uninstalled-container">
                <div class="no-items-notification center" id="uninstalled-notifier" style="display:none;"><?php echo Yii::t('Dashboard.main', "All themes associated to this instance are currently installed."); ?></div>
                <span class="install" style="display:none;"><?php echo Yii::t('Dashboard.main', 'Install Theme'); ?></span>
                <span class="installing" style="display:none;"><?php echo Yii::t('Dashboard.main', 'Installing Theme...'); ?></span>
                <span class="unregister" style="display:none;"><?php echo Yii::t('Dashboard.main', 'Unregister'); ?></span>
            </div>

            <div class="clearfix"></div>

            <!-- Container for managed themes by ciims.org // Ajax -->
            <legend><?php echo Yii::t('Dashboard.views', 'Managed Themes'); ?></legend>
            <div class="meta-container" id="installed-container">
            </div>

            <div class="clearfix"></div>

            <!-- Installed Items -->
            <?php $form = $this->beginWidget('cii.widgets.CiiActiveForm'); ?>
                <?php foreach ($model->getThemes() as $theme=>$options)
                      {
                        $attribute = ($theme == 'desktop' ? 'theme' : $theme.'Theme');

                        $elements = array();
                        $elementOptions = array('options' => array());

                        // Allow themes to be empty for non desktop theme
                        if ($attribute !== 'theme')
                        {
                            $elements = array(NULL);
                            $elementOptions = array('options' => array(array('value' => NULL)));
                        }
                        
                        foreach ($options as $k=>$v)
                        {
                            $themeFolder = str_replace('webroot.themes.', '', $v['folder']);
                            $elements[] = $themeFolder;

                            // This image SHOULD be publicly accessible at this location assuming you have a half sane setup
                            $elementOptions['options'][] = array(
                                'value' => $themeFolder, 
                                'data-img-src' => Yii::app()->getBaseUrl(true) . '/themes/'.$themeFolder.'/default.png',
                                'selected' => Cii::getConfig($attribute) == $themeFolder ? 'selected' : null
                            );
                        }		

                        echo CHtml::openTag('div', array('class' => 'pure-form-group', 'style' => 'padding-bottom: 20px'));
                            echo CHtml::tag('legend', array(), CiiInflector::titleize($attribute));
                            echo $form->dropDownListRow($model, $attribute, $elements, $elementOptions);

                            if (count($options) == 0)
                                echo CHtml::tag('div', array('class' => 'row noItemsMessage'), CHtml::tag('span', array(), Yii::t('Dashboard.views', 'There are no themes installed for this category.')));

                        echo CHtml::closeTag('div');		
                    } ?>
            <?php $this->endWidget(); ?>
        </div>
    </div>
    <div class="ow-overlay ow-closed"></div>
    <div class="modal ow-closed"></div>

    <!-- Template for themes -->
    <div class="pure-control-group template" style="display:none;">
        <p class="text-small text-small-inline inline"></p>
        <span class="pure-button pure-button-error pure-button-xsmall pure-button-link-xs pull-right remove-button" id="">
            <span class="fa fa-times"></span>
        </span>
        <span class="pure-button pure-button-primary pure-button-xsmall pure-button-link-xs pull-right" id="updater" data-attr-id="">
            <span class="fa fa-spinner fa-spin icon-spinner"></span>
            <span class="checking"><?php echo Yii::t('Dashboard.main', 'Checking for Updates'); ?></span>
            <span class="uptodate" style="display:none;"><?php echo Yii::t('Dashboard.main', 'Up to Date!'); ?></span>
            <span class="available" style="display:none;"><?php echo Yii::t('Dashboard.main', 'Click to Update'); ?></span>
            <span class="updating" style="display:none;"><?php echo Yii::t('Dashboard.main', 'Updating...'); ?></span>
            <span class="updating-error" style="display:none;"><?php echo Yii::t('Dashboard.main', 'Unable to Update'); ?></span>
        </span>
        <div class="clearfix"></div>
    </div>

</div>

<?php Yii::app()->clientScript->registerScriptFile($this->asset.'/js/image-picker.min.js', CClientScript::POS_END); ?>
<?php Yii::app()->clientScript->registerScriptFile($this->asset.'/jcarousel-master/dist/jquery.jcarousel.min.js', CCLientScript::POS_END); ?>
<?php Yii::app()->clientScript->registerScriptFile($this->asset.'/js/jquery.omniwindow.min.js'); ?>
<?php Yii::app()->clientScript->registerCssFile($this->asset.'/css/pure.css');  ?>
<?php Yii::app()->clientScript->registerCssFile($this->asset.'/css/image-picker.css'); ?>
<?php Yii::app()->clientScript->registerCss('no-labels', 'label { display: none; }'); ?>
