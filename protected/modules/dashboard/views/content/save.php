<?php $form = $this->beginWidget('cii.widgets.CiiActiveForm'); ?>
<div class="content-container">
	<div class="editor">
		<div id="main">
			<div class="content">
				<?php echo $form->textArea($model, 'content'); ?>
			</div>
		</div>
	</div>
	<div class="body-content">
		<div id="main" class="nano">
			<div class="content">
				<div class="preview"></div>
			</div>
		</div>
	</div>	
</div>

<?php $this->endWidget(); ?>

<?php $cs = Yii::app()->getClientScript(); ?>

<?php
	  $cs->registerCssFile($this->asset.'/highlight.js/default.css')
		 ->registerCssFile($this->asset.'/highlight.js/github.css')
		 ->registerCss('form', 'form { height: 100%; }')

		 ->registerScriptFile($this->asset.'/js/jquery.nanoscroller.min.js', CClientScript::POS_END)
		 ->registerScriptFile($this->asset.'/js/marked.js', CClientScript::POS_END)
		 ->registerScriptFile($this->asset.'/highlight.js/highlight.pack.js', CClientScript::POS_END)

		 ->registerScript('nano-scroller', '$(".nano").nanoScroller();')
		 ->registerScript('marked', '
				marked.setOptions({
				  gfm: true,
				  highlight: function (lang, code) {
				    return hljs.highlightAuto(lang, code).value;
				  },
				  tables: true,
				  breaks: true,
				  pedantic: false,
				  sanitize: true,
				  smartLists: true,
				  smartypants: true,
				  langPrefix: "lang-"
				});

				$("#Content_content").keyup(function() {
					var markdown = marked($(this).val());
					$(".preview").html(markdown);
					$(".nano").nanoScroller();
				})
		'); ?>