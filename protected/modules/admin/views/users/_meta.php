<p class="help-block">User data often has many other pieces of information associated to it. This user has the following additional attributes.</p>
<? $meta = Users::model()->parseMeta($model->metadata); ?>
<br />
<ul>
	<? foreach ($meta as $k=>$v): ?>
		<li><strong><? echo ucwords(trim(preg_replace('/(?<=\\w)(?=[A-Z])/'," $1", $k))); ?>:</strong> <? echo $v['value']; ?></li>
	<? endforeach; ?>
</ul>