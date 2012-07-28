<p class="help-block">User data often has many other pieces of information associated to it. This user has the following additional attributes.</p>
<?php $meta = Users::model()->parseMeta($model->metadata); ?>
<br />
<ul>
	<?php foreach ($meta as $k=>$v): ?>
		<li><strong><?php echo ucwords(trim(preg_replace('/(?<=\\w)(?=[A-Z])/'," $1", $k))); ?>:</strong> <?php echo $v['value']; ?></li>
	<?php endforeach; ?>
</ul>
