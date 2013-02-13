<h4>Create Admin User</h4>
<p>Lets setup your first admin user, and set a few site settings. All of these settings can be changed later in the admin panel.</p>

<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'user-form',
    'type'=>'inline',
    'enableAjaxValidation'=>true,
    'enableClientValidation'=>true,
)); ?>
    <div class="path-field">
        <?php echo $form->textFieldRow($model, 'email',  array('class'=>'input-small', 'placeholder' => 'Email')); ?>
        <?php echo $form->passwordFieldRow($model, 'password',  array('class'=>'input-small', 'placeholder' => 'Password')); ?>
    </div>
    <div class="clearfix" style="margin: 5px;"></div>
    <div class="path-field">
        <?php echo $form->textFieldRow($model, 'firstName',  array('class'=>'input-small', 'placeholder' => 'First Name')); ?>
        <?php echo $form->textFieldRow($model, 'lastName',  array('class'=>'input-small', 'placeholder' => 'Last Name')); ?>
    </div>
    <div class="clearfix" style="margin: 5px;"></div>
    <div class="path-field">
        <?php echo $form->textFieldRow($model, 'displayName', array('class'=>'input-small', 'placeholder' => 'Display Name')); ?>
        <?php echo $form->textFieldRow($model, 'siteName', array('class'=>'input-small', 'placeholder' => 'Site Name')); ?>
    </div>
    <div class="clearfix" style="margin: 5px;"></div>
    <hr />
    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label'=>'Submit', 'htmlOptions' => array('class'=>'pull-right btn-inverse')) ); ?>
<?php $this->endWidget(); ?>