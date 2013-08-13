<div class="login-container">
	<div class="sidebar">
		<div class="well-span">
			<h4><?php echo Yii::t('main', "Register An Account"); ?></h4>
			<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
						'id'					=>	'login-form',
						'focus'					=>'	input[type="text"]:first',
						'enableAjaxValidation'	=>	true
					)); ?>
				<div class="login-form-container">
					<div id="jsAlert" class="alert alert-warning" style="display:none">
						<button type="button" class="close" data-dismiss="alert">&times;</button>
						<div id="jsAlertContent"></div>
					</div>
					<?php if (!Yii::app()->user->isGuest): ?>
						<div class="alert alert-info" style="margin-top: 20px;">
						  	<button type="button" class="close" data-dismiss="alert">&times;</button>
						  	<?php echo Yii::t('main', "{{headsup}} Looks like you're already logged in as {{email}}. You should {{logout}} before logging in to another account.", array(
							  		'{{headsup}}' => CHtml::tag('strong', array(), Yii::t('main', 'Heads Up!')),
							  		'{{email}}'   => CHtml::tag('strong', array(), Yii::app()->user->email),
							  		'{{logout}}'  => CHtml::tag('strong', array(), CHtml::link(Yii::t('main', 'logout'), $this->createUrl('/logout')))
							  	)); ?>
						</div>
					<?php else: ?>
						<?php if ($model->hasErrors()): ?>
							<div class="alert alert-error" style="margin-bottom: -5px;">
							  	<button type="button" class="close" data-dismiss="alert">&times;</button>
							  	<?php echo Yii::t('main', "{{oops}} It looks like there were a few errors in your submission", array(
							  		'{{oops}}' => CHtml::tag('strong', array(), Yii::t('main', 'Oops!'))
							  	)); ?>
							</div>
						<?php endif; ?>
						<?php echo $form->TextField($model, 'email', array('id'=>'email', 'placeholder'=>Yii::t('main', 'Email Address'))); ?>
						<?php echo $form->TextField($model, 'displayName', array('id'=>'email', 'placeholder'=>Yii::t('main', 'Username'))); ?>
						<?php echo $form->PasswordField($model, 'password', array('id'=>'password', 'placeholder'=>Yii::t('main', 'Password'))); ?>
						<div id ="password_strength_1" class="password_strength_container">
							<div class="password_strength_bg"></div>
							<div class="password_strength" style="width: 0%;"></div>
							<div class="password_strength_separator" style="left: 25%;"></div>
							<div class="password_strength_separator" style="left: 50%;"></div>
							<div class="password_strength_separator" style="left: 75%;"></div>
							<div class="password_strength_desc"></div>
							<div class="clearfix"></div>
						</div>
						<?php echo $form->PasswordField($model, 'password2', array('id'=>'password', 'placeholder'=>Yii::t('main', 'Password (again)'), 'id' => 'password2')); ?>
						<div id ="password_strength_2" class="password_strength_container">
							<div class="password_strength_bg"></div>
							<div class="password_strength" style="width: 0%;"></div>
							<div class="password_strength_separator" style="left: 25%;"></div>
							<div class="password_strength_separator" style="left: 50%;"></div>
							<div class="password_strength_separator" style="left: 75%;"></div>
							<div class="password_strength_desc"></div>
							<div class="clearfix"></div>
						</div>
					</div>
					<div class="login-form-footer">
						<?php echo CHtml::link(Yii::t('main', 'login'), Yii::app()->createUrl('/login'), array('class' => 'login-form-links')); ?>
						<span class="login-form-links"> | </span>
						<?php echo CHtml::link(Yii::t('main', 'forgot'), Yii::app()->createUrl('/forgot'), array('class' => 'login-form-links')); ?>
						<?php $this->widget('bootstrap.widgets.TbButton', array(
								'buttonType' => 'submit',
	    	                    'type' => 'success',
	    	                    'label' => Yii::t('main', 'Register'),
	    	                    'htmlOptions' => array(
	    	                        'id' => 'submit-comment',
	    	                        'class' => 'sharebox-submit pull-right',
	    	                        'style' => 'margin-top: -4px'
	    	                    )
	    	                )); ?>
    	            <?php endif; ?>
    	            <?php if (Yii::app()->user->isGuest): ?>
	    	            <?php if (count(Cii::getHybridAuthProviders()) >= 1): ?>
	    	            <div class="clearfix" style="border-bottom: 1px solid #aaa; margin: 15px;"></div>
							<span class="login-form-links"><?php echo Yii::t('main', "Or register with one of these social networks"); ?></span>
	    	        	<?php endif; ?>
	    	        	<div class="clearfix"></div>
	    	        	<div class="social-buttons">
		    	            <?php foreach (Cii::getHybridAuthProviders() as $k=>$v): ?>
								<?php if (Cii::get($v, 'enabled', false) == 1): ?>
									<?php echo CHtml::link(NULL, $this->createUrl('/hybridauth/'.$k), array('class' => 'social-icons ' . strtolower($k))); ?>
								<?php endif; ?>
		    	        	<?php endforeach; ?>
		    	        </div>
		    	    <?php endif; ?>
				</div>
			<?php $this->endWidget(); ?>
		</div>
	</div>
</div>

<?php Yii::app()->clientScript->registerScriptFile($this->asset .'/js/zxcvbn.js'); ?>
<?php Yii::app()->clientScript->registerScript('password_strength_meter', '$(document).ready(function() { DefaultTheme.loadRegister(); });', CClientScript::POS_END);