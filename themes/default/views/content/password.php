<center>
	 <div class="path-field">
        <div class="control-group" style="width: 100%;">
            <?php echo CHtml::beginForm(); ?>
				<?php echo CHtml::PasswordField('password', '', array('id'=>'password', 'placeholder'=>'Password',  'style'=>'width: 60%;')); ?>
				<?php echo CHtml::hiddenField('id', $id); ?>
				<?php echo CHtml::submitButton('Authenticate', array('id' => 'checkYiiPathButton', 'class'=>'btn btn-primary', 'escape'=>false, 'style'=>'margin-top: -9px; margin-left: 10px;')); ?>
			<?php echo CHtml::endForm(); ?>
        </div>
    </div>
</center>