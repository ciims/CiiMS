<div class="content-container">
	<div class="editor">
		<div id="main" class="nano">
			<div class="content"></div>
		</div>
	</div>
	<div class="body-content">
		<div id="main" class="nano">
			<div class="content"></div>
		</div>
	</div>	
</div>

<?php Yii::app()->getClientScript()->registerScriptFile($this->asset.'/js/jquery.nanoscroller.min.js', CClientScript::POS_END); ?>
<?php Yii::app()->getClientScript()->registerScript('nano-scroller', '
		$("#posts.nano").nanoScroller();
'); ?>