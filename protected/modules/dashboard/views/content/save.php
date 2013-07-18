<?php $form = $this->beginWidget('cii.widgets.CiiActiveForm'); ?>
<div class="content-container">
	<div class="editor">
		<div id="main">
			<div class="content">
			</div>
		</div>
	</div>
	<div class="body-content">
		<div id="main" class="nano">
			<div class="content">
			</div>
		</div>
	</div>	
</div>

<?php $this->endWidget(); ?>
<?php Yii::app()->getClientScript()->registerCss('form', 'form { height: 100%; }'); ?>
<?php Yii::app()->getClientScript()->registerScriptFile($this->asset.'/js/jquery.nanoscroller.min.js', CClientScript::POS_END); ?>
<?php Yii::app()->getClientScript()->registerScriptFile($this->asset.'/js/marked.js', CClientScript::POS_END); ?>
<?php Yii::app()->getClientScript()->registerScript('nano-scroller', '
		$(".nano").nanoScroller();
'); ?>
<?php Yii::app()->getClientScript()->registerScript('marked', '
	marked.setOptions({
	  gfm: true,
	  highlight: function (code, lang, callback) {
	    pygmentize({ lang: lang, format: "html" }, code, function (err, result) {
	      callback(err, result.toString());
	    });
	  },
	  tables: true,
	  breaks: true,
	  pedantic: false,
	  sanitize: true,
	  smartLists: true,
	  smartypants: false,
	  langPrefix: "lang-"
	});
'); ?>