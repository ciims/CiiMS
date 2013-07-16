<?php $this->widget('application.modules.dashboard.components.CiiSettingsForm', array('model' => $model, 'header' => $header)); ?>
<?php Yii::app()->getClientScript()->registerScriptFile($this->asset.'/js/jquery.nanoscroller.min.js', CClientScript::POS_END)
								   ->registerScript('nano-scroller', '$("#main.nano").nanoScroller();'); ?>