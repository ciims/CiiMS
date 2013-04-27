<div class="login-container">
	<div class="sidebar" >
		<div class="well-span span10">
			<h4>Update Your Profile</h4>
			<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
			'id'=>'profile-form',
			)); ?>
			<?php echo $form->errorSummary($model); ?>
			    
			<?php echo $form->textFieldRow($model,'email',array('class'=>'span12','maxlength'=>255)); ?>        
			<?php echo $form->passwordFieldRow($model,'password',array('value'=>'', 'class'=>'span12','maxlength'=>64, 'placeholder' => 'Set a password for the user here. Leave blank to keep existing password.')); ?>    
			<?php echo $form->textFieldRow($model,'displayName',array('class'=>'span12','maxlength'=>255)); ?>    
			<?php echo $form->textFieldRow($model,'firstName',array('class'=>'span12','maxlength'=>255)); ?>        
			<?php echo $form->textFieldRow($model,'lastName',array('class'=>'span12','maxlength'=>255)); ?>
			<?php echo $form->textAreaRow($model, 'about', array('class' => 'span12')); ?>
			<?php $this->widget('bootstrap.widgets.TbButton', array(
								'buttonType' => 'submit',
	    	                    'type' => 'success',
	    	                    'label' => 'Submit',
	    	                    'htmlOptions' => array(
	    	                        'id' => 'submit-comment',
	    	                        'class' => 'sharebox-submit pull-right',
	    	                        'style' => 'margin-top: -4px'
	    	                    )
	    	                )); ?>
			<?php $this->endWidget(); ?>
		</div>
	</div>
</div>

<style>
	main .login-container { width: 80%; }
	main .main-body { margin-left: 80px; }
</style>