<div class="row-fluid">
	<? $count = 0; ?>
	<? foreach($files as $file): ?>
		<? if ($count % 2 == 0): ?>
			</div>
			<div class="row-fluid">
		<? endif; ?>	
		
		<? $this->renderFile($file); ?>	
		<? $count++; ?>
	<? endforeach; ?>
</div>