<?php $form = $this->beginWidget('cii.widgets.CiiActiveForm', array(
	'htmlOptions' => array(
		'class' => 'pure-form pure-form-aligned content-container-form'
	)
)); ?>
	<?php echo $form->hiddenField($model, 'id'); ?>
	<?php echo $form->hiddenField($model, 'vid'); ?>
	<?php echo $form->hiddenField($model, 'created'); ?>
	<?php echo $form->hiddenField($model,'parent_id',array('value'=>1)); ?>
	<?php echo $form->hiddenField($model,'author_id',array('value'=>Yii::app()->user->id)); ?>
	<div class="content-container">
		<div class="header">
			<div class="content">
				<div class="pull-left" style="width: 80%;">
					<?php echo $form->textField($model, 'title', array('placeholder' => Yii::t('Dashboard.views', 'Enter your post title here'), 'class' => 'title')); ?>
				</div>
				<div class="pull-right">
					<?php echo CHtml::submitButton(Yii::t('Dashboard.views', 'Save Changes'), array('class' => 'pure-button-small pure-button pure-button-error pure-button-link')); ?>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>

		<div class="editor">
			<div class="top-header">
				<span><?php echo Yii::t('Dashboard.views', 'Markdown'); ?></span>
				<span class="pull-right fa fa-camera icon-camera"></span>
			</div>
			<div id="promotedDz" class="dropzone" style="display:none;"></div>
			<div id="main">
				<div class="content">
					<?php echo $form->textArea($model, 'content'); ?>
				</div>
			</div>
		</div>

		<div class="body-content">
			<div class="top-header">
				<span class="show-settings"><?php echo Yii::t('Dashboard.views', 'Preview'); ?></span>
				<span class="show-preview" style="display:none"><?php echo Yii::t('Dashboard.views', 'Content Settings'); ?></span>
				<span class="pull-right fa fa-trash"></span>
				<span class="pull-right fa fa-gear show-settings"></span>
				<span class="pull-right fa fa-gear show-preview" style="display:none"></span>
				<?php if ($model->isPublished()): ?>
					<?php echo CHtml::link(NULL, Yii::app()->getBaseUrl(true) . Yii::app()->createUrl($model->slug), array('class' => 'fa fa-eye-open pull-right')); ?>
				<?php endif; ?>
			</div>
			<div id="main" class="nano">				
				<div class="content flipbox">
					<?php $meta = Content::model()->parseMeta($model->metadata); ?>
					<p style="text-align:center;">
						<?php if (isset($meta['blog-image']['value'])): ?>
							<?php echo CHtml::image(Yii::app()->baseUrl . $meta['blog-image']['value'], NULL, array('class'=>'preview-image')); ?>
						<?php else: ?>
							<?php echo CHtml::image('data:image/gif;base64,R0lGODlhAQABAAAAACH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==', NULL, array('class'=>'preview-image')); ?>
						<?php endif; ?>
					</p>
					<div class="preview-metadata">
						<span class="blog-author minor-meta">
							<?php echo Yii::t('Dashboard.main', 'By {{user}}', array(
								'{{user}}' => CHtml::link(CHtml::encode($model->author->displayName), Yii::app()->createUrl("/profile/{$model->author->id}/"))
							)); ?>
							<span class="separator">⋅</span> 
						</span>
						<span class="date"><?php echo Cii::timeago($model->published); ?>
							<span class="separator">⋅</span> 
						</span>
						<span class="separator">⋅</span>
						<span class="minor-meta-wrap">
							<span class="blog-categories minor-meta">
								<?php echo Yii::t('Dashboard.main', 'In {{category}}', array(
								'{{category}}' => CHtml::link(CHtml::encode($model->category->name), Yii::app()->createUrl($model->category->slug))
							)); ?>
							</span>
						</span>
					</div>	
					<div class="preview"></div>
				</div>
			</div>
		</div>

		<div class="settings">
			<?php $htmlOptions = array('class' => 'pure-input-2-3'); ?>
			<div class="pure-control-group">
				<?php echo $form->dropDownListRow($model,'status', $model->getStatuses(), $htmlOptions); ?>
			</div>
			<div class="pure-control-group">
				<?php echo !$canPublish ? NULL : $form->dropDownListRow($model,'commentable', array(1=>Yii::t('Dashboard.views', 'Yes'), 0=>Yii::t('Dashboard.main', 'No')), $htmlOptions); ?>
			</div>
			<div class="pure-control-group">
				<?php echo $form->dropDownListRow($model,'category_id', CHtml::listData(Categories::model()->findAll(), 'id', 'name'), $htmlOptions); ?>
			</div>
			<div class="pure-control-group date form_datetime">
				<?php echo !$canPublish ? NULL : $form->textFieldRow($model, 'published', $htmlOptions); ?>
				<?php echo CHtml::hiddenField('timezone', NULL); ?>
			</div>
			<div class="pure-control-group">
				<?php echo !$canPublish ? NULL : $form->dropDownListRow($model,'type_id', array(2=> Yii::t('Dashboard.views', 'Blog Post'), 1=> Yii::t('Dashboard.views', 'Page')), $htmlOptions); ?>
			</div>
			<div class="pure-control-group">
				<?php echo !$canPublish ? NULL : $form->dropDownListRow($model, 'view', $views, array('class'=>'pure-input-2-3', 'options' => array($model->view => array('selected' => true)))); ?>
			</div>
			<div class="pure-control-group">
	            <?php echo !$canPublish ? NULL : $form->dropDownListRow($model, 'layout', $layouts, array('class'=>'pure-input-2-3', 'options' => array($model->layout => array('selected' => true)))); ?>
			</div>
			<div class="pure-control-group">
				<?php echo !$canPublish ? NULL : $form->textFieldRow($model,'password',array('class'=>'pure-input-2-3','maxlength'=>150, 'placeholder' =>  Yii::t('Dashboard.views', 'Password (Optional)'), 'value' => Cii::decrypt($model->password))); ?>
			</div>
			<div class="pure-control-group">
				<?php echo !$canPublish ? NULL :$form->textFieldRow($model,'slug',array('class'=>'pure-input-2-3','maxlength'=>150, 'placeholder' =>  Yii::t('Dashboard.views', 'Slug'))); ?>
			</div>
			<div class="pure-control-group">
				<?php echo $form->textField($model, 'tagsFlat', array('id' => 'tags')); ?>
			</div>
			<div class="pure-control-group">
				<label for="extract" class="left-label"><?php echo $model->getAttributeLabel('extract'); ?></label>
				<?php $htmlOptions['style'] = 'width: 100%; height: 250px;'; ?>
				<?php $htmlOptions['placeholder'] =  Yii::t('Dashboard.views', 'Add a content extract here'); ?>
				<?php echo $form->textArea($model, 'extract', $htmlOptions); ?>
			</div>
		</div>

	</div>

<?php $this->endWidget(); ?>

<?php $asset = Yii::app()->assetManager->publish(YiiBase::getPathOfAlias('ext.cii.assets.js'), true, -1, YII_DEBUG); ?>
<?php echo CHtml::tag('input', array('type' => 'hidden', 'class' => 'preferMarkdown', 'value' => Cii::getConfig('preferMarkdown')), NULL); ?>
<?php  Yii::app()->getClientScript()
				 ->registerCssFile($this->asset.'/highlight.js/default.css')
				 ->registerCssFile($this->asset.'/highlight.js/github.css')
				 ->registerCssFile($this->asset.'/css/dropzone.css')
				 ->registerCssFile($this->asset . '/css/jquery.tags.css')
				 ->registerCssFile($this->asset.'/datepicker/css/datetimepicker.css')
				 ->registerScriptFile($this->asset.'/js/jquery.nanoscroller.min.js', CClientScript::POS_END)
				 ->registerScriptFile($this->asset .'/js/jquery.tags.min.js', CClientScript::POS_END)
				 ->registerScriptFile($this->asset.'/highlight.js/highlight.pack.js', CClientScript::POS_END)
				 ->registerScriptFile($asset.'/marked.js', CClientScript::POS_END)
				 ->registerScriptFile($this->asset.'/dropzone/dropzone.min.js', CClientScript::POS_END)
				 ->registerScriptFile($this->asset.'/js/jquery.flippy.min.js', CClientScript::POS_END)
				 ->registerScriptFile($this->asset.'/js/jstz.min.js', CClientScript::POS_END)
				 ->registerScriptFile($this->asset.'/datepicker/js/bootstrap-datetimepicker.min.js', CClientScript::POS_END); ?>
