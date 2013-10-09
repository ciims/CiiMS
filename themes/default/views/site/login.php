<div class="login-container">
	<div class="sidebar">
		<div class="well-span">
			<h4><?php echo Yii::t('DefaultTheme', 'Login to Your Account'); ?></h4>
			<?php $form=$this->beginWidget('bootstrap.widgets.TbActiveForm', array(
						'id'					=>	'login-form',
						'focus'					=>'	input[type="text"]:first',
						'enableAjaxValidation'	=>	true
					)); ?>
				<div class="login-form-container">
					<?php if (!Yii::app()->user->isGuest): ?>
						<div class="alert alert-info" style="margin-top: 20px;">
						  	<button type="button" class="close" data-dismiss="alert">&times;</button>
						  	<?php echo Yii::t('DefaultTheme', "{{headsup}} Looks like you're already logged in as {{email}}. You should {{logout}} before logging in to another account.", array(
							  		'{{headsup}}' => CHtml::tag('strong', array(), Yii::t('DefaultTheme', 'Heads Up!')),
							  		'{{email}}'   => CHtml::tag('strong', array(), Yii::app()->user->email),
							  		'{{logout}}'  => CHtml::tag('strong', array(), CHtml::link(Yii::t('DefaultTheme', 'logout'), $this->createUrl('/logout')))
							  	)); ?>
						 </div>
					<?php else: ?>
						<?php if ($model->hasErrors()): ?>
							<div class="alert alert-error" style="margin-bottom: -5px;">
							  	<button type="button" class="close" data-dismiss="alert">&times;</button>
							  	<?php echo Yii::t('DefaultTheme', "{{oops}} We weren't able to log you in using the provided credentials.", array(
							  		'{{oops}}' => CHtml::tag('strong', array(), Yii::t('DefaultTheme', 'Oops!'))
							  	)); ?>
							</div>
						<?php endif; ?>
						<?php echo $form->TextField($model, 'username', array('id'=>'email', 'placeholder'=>Yii::t('DefaultTheme', 'Email Address'))); ?>
						<?php echo $form->PasswordField($model, 'password', array('id'=>'password', 'placeholder'=>Yii::t('DefaultTheme', 'Password'))); ?>
					</div>
					<div class="login-form-footer">
						<?php echo CHtml::link(Yii::t('DefaultTheme', 'register'), Yii::app()->createUrl('/register'), array('class' => 'login-form-links')); ?>
						<span class="login-form-links"> | </span>
						<?php echo CHtml::link(Yii::t('DefaultTheme', 'forgot'), Yii::app()->createUrl('/forgot'), array('class' => 'login-form-links')); ?>
						<?php $this->widget('bootstrap.widgets.TbButton', array(
								'buttonType' => 'submit',
	    	                    'type' => 'success',
	    	                    'label' => Yii::t('DefaultTheme', 'Submit'),
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
							<span class="login-form-links"><?php echo Yii::t('DefaultTheme', 'Or sign in with one of these social networks'); ?></span>
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