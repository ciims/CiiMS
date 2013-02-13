<h4>Now Lets Connect To Your Database</h4>
<p>If you don't have a MySQL database setup yet, please do so now, then return to this page.</p>

<?php $form = $this->beginWidget('bootstrap.widgets.TbActiveForm', array(
    'id'=>'db-source-form',
    'type'=>'inline',
    'enableAjaxValidation'=>true,
    'enableClientValidation'=>true,
)); ?>
    <div class="path-field">
        <?php echo $form->textFieldRow($model, 'username',  array('class'=>'input-small', 'placeholder' => 'Username')); ?>
        <?php echo $form->passwordFieldRow($model, 'password',  array('class'=>'input-small', 'placeholder' => 'Password')); ?>
    </div>
    <div class="clearfix" style="margin: 5px;"></div>
    <div class="path-field">
        <?php echo $form->textFieldRow($model, 'dbname',  array('class'=>'input-small', 'placeholder' => 'Database Name')); ?>
        <?php echo $form->textFieldRow($model, 'host',  array('class'=>'input-small', 'placeholder' => 'Database Host')); ?>
    </div>
    <div class="clearfix" style="margin: 5px;"></div>
    <hr />
    <?php $this->widget('bootstrap.widgets.TbButton', array('buttonType'=>'submit', 'label'=>'Validate', 'htmlOptions' => array('class'=>'pull-right btn-inverse')) ); ?>
<?php $this->endWidget(); ?>