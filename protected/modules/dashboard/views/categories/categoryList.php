<div class="settings-row">
		<?php echo CHtml::link($data->name, $this->createUrl('/dashboard/categories/save/id/' . $data->id), array('class' => 'name pull-left', 'style' => 'margin-top: 10px;')); ?>

		<?php echo CHtml::link('', $this->createUrl('/dashboard/categories/delete/id/' . $data->id), array('class' => 'fa fa-times pure-button pure-button-link pure-button-xsmall pull-right pure-button-error', 'style' => 'margin-top: 10px;
			top: -5px !important;
			color: #fff;
font-size: 12px !important;
padding: 6px 13px;
font-weight: normal !important;')); ?>

		<span class="pure-button pure-button-link pure-button-xsmall pull-right pure-button-primary" style="margin-top:10px">
			<?php $criteria = new CDbCriteria; ?>
			<?php $criteria->addCondition('category_id = :id'); ?>
			<?php $criteria->addCondition("vid=(SELECT MAX(vid) FROM content WHERE id=t.id)"); ?>
			<?php $criteria->params = array(':id' => $data->id); ?>
			<?php echo Content::model()->count($criteria); ?>
		</span>

		
	<div class="clearfix"></div>
</div>
