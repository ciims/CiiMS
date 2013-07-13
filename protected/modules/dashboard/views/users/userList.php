<div class="settings-row">
	<?php echo CHtml::image($data->gravatarImage(35), NULL, array('class' => 'rounded-img pull-left')); ?>
	<div class="user-info pull-left">
		<?php echo CHtml::link($data->displayName, $this->createUrl('/dashboard/users/update/id/' . $data->id), array('class' => 'name')); ?>
		<?php echo CHtml::tag('span', array('class' => 'email'), $data->email); ?>
	</div>
	<?php if ($data->role->id >= 4): ?>
		<?php
			$class = NULL;
			switch ($data->role->id)
			{
				case 4:
					$class = 'pure-button-primary';
				break;
				case 5:
					$class = 'pure-button-secondary';
				break;
				case 6:
					$class = 'pure-button-secondary';
				break;
				case 7:
					$class = 'pure-button-warning';
				break;
				case 8:
					$class = 'pure-button-success';
				break;
				case 9:
					$class = 'pure-button-error';
				break;
				default:
					$class = NULL;
			}
		?>
		<span class="pure-button pure-button-link pure-button-xsmall pull-right element <?php echo $class; ?>"><?php echo $data->role->name; ?></span>
	<?php endif; ?>
	<div class="clearfix"></div>
</div>