<div class="row-fluid">
    <?php $count = 0; ?>
    <?php foreach($files as $file): ?>
        <?php if ($count % 2 == 0): ?>
            </div>
            <div class="row-fluid">
        <?php endif; ?> 
        
        <?php $this->renderFile($file); ?>  
        <?php $count++; ?>
    <?php endforeach; ?>
</div>