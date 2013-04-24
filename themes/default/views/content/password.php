<center>
	 <div class="path-field">
        <div class="control-group" style="width: 100%;">
        	<?php $this->widget('bootstrap.widgets.TbAlert', array(
			    'block'=>true, // display a larger alert block?
			    'fade'=>true, // use transitions?
			    'closeText'=>'×', // close link text - if set to false, no close link is displayed
			    'alerts'=>array( // configurations per alert type
				    'error'=>array('block'=>true, 'fade'=>true, 'closeText'=>'×'), // success, info, warning, error or danger
			    ),
			));
			?>
            <?php echo CHtml::beginForm(); ?>
				<?php echo CHtml::PasswordField('password', '', array('id'=>'password', 'placeholder'=>'Password',  'style'=>'width: 60%;')); ?>
				<?php echo CHtml::hiddenField('id', $id); ?>
				<?php echo CHtml::submitButton('Authenticate', array('id' => 'checkYiiPathButton', 'class'=>'btn btn-primary', 'escape'=>false, 'style'=>'margin-top: -9px; margin-left: 10px;')); ?>
			<?php echo CHtml::endForm(); ?>
        </div>
    </div>
</center>