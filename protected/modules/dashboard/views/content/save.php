<?php $form = $this->beginWidget('cii.widgets.CiiActiveForm'); ?>
<div class="content-container">
	<div class="header">
		<div class="content">
			<div class="pull-left" style="width: 48%;">
				<?php echo $form->textField($model, 'title', array('placeholder' => 'Enter your post title here', 'class' => 'title')); ?>
			</div>
			<div class="pull-right">
				<?php echo CHtml::submitButton('Save Changes', array('class' => 'pure-button pure-button-error pure-button-link')); ?>
			</div>
			<div class="clearfix"></div>
		</div>
	</div>
	<div class="editor">
		<div id="main">
			<div class="top-header">
				<span>Markdown</span>
			</div>
			<div class="content">
				
				<?php echo $form->textArea($model, 'content'); ?>
			</div>
		</div>
	</div>
	<div class="body-content">
		<div id="main" class="nano">
			<div class="content">
				<div class="top-header">
					<span>Preview</span>
				</div>
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
		 ->registerCssFile($this->asset.'/dropzone/css/basic.css')
		 ->registerCssFile($this->asset.'/dropzone/css/dropzone.css')
		 ->registerCss('form', 'form { height: 100%; }')

		 ->registerScriptFile($this->asset.'/js/jquery.nanoscroller.min.js', CClientScript::POS_END)
		 ->registerScriptFile($this->asset.'/js/marked.js', CClientScript::POS_END)
		 ->registerScriptFile($this->asset.'/highlight.js/highlight.pack.js', CClientScript::POS_END)
		 ->registerScriptFile($this->asset.'/dropzone/dropzone.min.js', CClientScript::POS_END)

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
					var markdown = $("<div class=\"md-preview\">" + marked($(this).val()).replace(/{image}/g, "<div class=\"dropzone\"></div>") + "</div>");

					var i = 0;

					$(".preview div.dropzone").each(function() {
						$(markdown).find("div.dropzone:eq(" + i + ")").replaceWith($(this));
						i++;
					});	

					$(".preview").html(markdown);
					$(".nano").nanoScroller();

					$("div.dropzone").each(function() {
						if (!$(this).hasClass("dz-clickable"))
		 				{
		 					// Make sure we do not have a hash collision
		 					var hash = Math.random().toString(36).substring(7);

		 					while ($(".dropzone-" + hash).length > 0)
		 						hash = Math.random().toString(36).substring(15);

							$(this).addClass("dropzone-" + hash);
							var dz = new Dropzone(".preview div.dropzone-" + hash, {
								url : "/file/upload"
							});
		 				}
		 			});
				});

		 		$("#Content_content").keyup();
		'); ?>