<?php $form = $this->beginWidget('cii.widgets.CiiActiveForm', array(
	'htmlOptions' => array(
		'class' => 'pure-form pure-form-aligned content-container-form'
	)
)); ?>
	<?php echo $form->hiddenField($model, 'id'); ?>
	<?php echo $form->hiddenField($model, 'vid'); ?>
	<?php echo $form->hiddenField($model, 'created'); ?>
	<?php echo $form->hiddenField($model,'parent_id',array('value'=>1)); ?>
	<?php echo $form->hiddenField($model,'author_id',array('value'=>Yii::app()->user->id)); ?>
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
			<div class="top-header">
				<?php if ((bool)Cii::getConfig('preferMarkdown', false) == true): ?>
					<span>Markdown</span>
				<?php else: ?>
					<span>Rich Text</span>
				<?php endif; ?>
			</div>
			<div id="main">
				<div class="content">
					<?php if ((bool)Cii::getConfig('preferMarkdown', false) == true): ?>
						<?php echo $form->textArea($model, 'content'); ?>
					<?php else: ?>
						<?php $this->widget('ext.redactor.ImperaviRedactorWidget', array(
	    	                    'model' => $model,
	    	                    'attribute' => 'content',
	    	                    'options' => array(
	    	                        'focus' => false,
	    	                        'autoresize' => false,
	    	                        'minHeight' =>'100%',
	    	                        'changeCallback' => 'js:function() { $("#Content_content").change(); }'
	    	                    )
	    	                ));
	    	            ?>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="body-content">
			<div class="top-header">
				<span class="show-settings">Preview</span>
				<span class="show-preview" style="display:none">Content Settings</span>
				<span class="pull-right icon-gear show-settings"></span>
				<span class="pull-right icon-gear show-preview" style="display:none"></span>
			</div>
			<div id="main" class="nano">				
				<div class="content flipbox">					
					<div class="preview"></div>
				</div>
			</div>
		</div>

		<div class="settings">
			<?php $htmlOptions = array('class' => 'pure-input-2-3'); ?>
			<div class="pure-control-group">
				<?php echo $form->dropDownListRow($model,'status', array(1=>'Published', 0=>'Draft'), $htmlOptions); ?>
			</div>
			<div class="pure-control-group">
				<?php echo $form->dropDownListRow($model,'commentable', array(1=>'Yes', 0=>'No'), $htmlOptions); ?>
			</div>
			<div class="pure-control-group">
				<?php echo $form->dropDownListRow($model,'category_id', CHtml::listData(Categories::model()->findAll(), 'id', 'name'), $htmlOptions); ?>
			</div>
			<div class="pure-control-group date form_datetime">
					<?php echo $form->textFieldRow($model, 'published', $htmlOptions); ?>
			</div>
			<div class="pure-control-group">
				<?php echo $form->dropDownListRow($model,'type_id', array(2=>'Blog Post', 1=>'Page'), $htmlOptions); ?>
			</div>
			<div class="pure-control-group">
				<?php echo $form->dropDownListRow($model, 'view', $views, array('class'=>'pure-input-2-3', 'options' => array($model->view => array('selected' => true)))); ?>
			</div>
			<div class="pure-control-group">
	            <?php echo $form->dropDownListRow($model, 'layout', $layouts, array('class'=>'pure-input-2-3', 'options' => array($model->layout => array('selected' => true)))); ?>
			</div>
			<div class="pure-control-group">
				<?php echo $form->textFieldRow($model,'password',array('class'=>'pure-input-2-3','maxlength'=>150, 'placeholder' => 'Password (Optional)', 'value' => Cii::decrypt($model->password))); ?>
			</div>
			<div class="pure-control-group">
				<?php echo $form->textFieldRow($model,'slug',array('class'=>'pure-input-2-3','maxlength'=>150, 'placeholder' => 'Slug')); ?>
			</div>
			<div class="pure-control-group">
				<?php echo $form->textField($model, 'tagsFlat', array('id' => 'tags')); ?>
			</div>
			<div class="pure-control-group">
				<?php $htmlOptions['style'] = 'width: 100%; height: 250px;'; ?>
				<?php $htmlOptions['placeholder'] = 'Add a content extract here'; ?>
				<?php echo $form->textArea($model, 'extract', $htmlOptions); ?>
			</div>
		</div>

	</div>

<?php $this->endWidget(); ?>

<?php echo CHtml::tag('input', array('type' => 'hidden', 'class' => 'preferMarkdown', 'value' => Cii::getConfig('preferMarkdown')), NULL); ?>
<?php $cs = Yii::app()->getClientScript(); ?>

<?php
	  $cs->registerCssFile($this->asset.'/highlight.js/default.css')
		 ->registerCssFile($this->asset.'/highlight.js/github.css')
		 ->registerCssFile($this->asset.'/dropzone/css/dropzone.css')
		 ->registerCssFile($this->asset . '/css/jquery.tags.css')
		 ->registerCssFile($this->asset.'/datepicker/css/datetimepicker.css')


		 ->registerScriptFile($this->asset.'/datepicker/js/bootstrap-datetimepicker.min.js', CClientScript::POS_END)
		 ->registerScriptFile($this->asset.'/js/jquery.nanoscroller.min.js', CClientScript::POS_END)
		 ->registerScriptFile($this->asset . '/js/jquery.tags.min.js', CClientScript::POS_END)
		 ->registerScriptFile($this->asset.'/js/marked.js', CClientScript::POS_END)
		 ->registerScriptFile($this->asset.'/highlight.js/highlight.pack.js', CClientScript::POS_END)
		 ->registerScriptFile($this->asset.'/dropzone/dropzone.min.js', CClientScript::POS_END)
		 ->registerScriptFile($this->asset.'/js/jquery.flippy.min.js', CClientScript::POS_END)

		 ->registerScript('nano-scroller', '$(".nano").nanoScroller();')
		 ->registerScript('admin_tags', '
			$("#tags").tagsInput({
				defaultText : "Add a Tag",
			    width: "99%",
			    height : "40px",
			    onRemoveTag : function(e) {
			    	$.post("../../removeTag", { id : ' . $model->id . ', keyword : e });
			    },
			    onAddTag : function(e) {
			    	$.post("../../addTag", { id : ' . $model->id . ', keyword : e });
			    }
			});
		')
		 ->registerScript('datepicker', '
		 	$("#Content_published").datetimepicker({
			    format: "yyyy-mm-dd hh:ii:ss"
			});
		 ')
		 ->registerScript('flip-behavior', '
		 	function bindFlipEvent()
		 	{
			 	$(".show-settings").click(function() {
				 	$(".flipbox").flippy({
				 		color_target : "#FFF",
					    duration: "500",
					    verso: $(".settings"),
					    onStart : function() {
					    	$(".nano").nanoScroller({ destroy: true });
					    	$(".nano").removeClass("has-scrollbar");
					    },
					    onFinish : function() {
					    	$(".show-settings").hide();
					    	$(".show-preview").show();
					    	$(".settings").show();
					    	$(".nano").nanoScroller({ flash : true});
					    	bindFlipEvent();
					    },
					    onReverseStart : function() {
					    	$(".body-content").after($(".settings"));
					    	$(".nano").nanoScroller({ destroy: true });
					    	$(".nano").removeClass("has-scrollbar");
					    },
					    onReverseFinish : function() {
					    	$(".settings").hide();
					    	$(".show-preview").hide();
					    	$(".show-settings").show();
					    	$(".nano").nanoScroller({ flash : true});
					    	bindFlipEvent();
					    }
					 });
				 });

		 		$(".show-preview").click(function() {
			 		$(".flipbox").flippyReverse();
			 	});
			}

			bindFlipEvent();
		 ')
		 ->registerScript('marked', '
				marked.setOptions({
				  gfm: true,
				  highlight: function (lang, code) {
				    return hljs.highlightAuto(lang, code).value;
				  },
				  tables: true,
				  breaks: true,
				  pedantic: false,
				  sanitize: false,
				  smartLists: true,
				  smartypants: true,
				  langPrefix: "lang-"
				});

				$("#Content_content").bind("input propertychange change", function(event) {

					if(typeof(Storage)!=="undefined")
						localStorage.setItem("content-" + $("#Content_id").val(), $(this).val());
					
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
								url : "' . $this->createUrl('/dashboard/content/upload/id/' . $model->id) . '",
								dictDefaultMessage : "Drop files here to upload - or click",
								success : function(data) {
									var response = $.parseJSON(data.xhr.response);
									if (response.success == true)
									{
										var instance = 0;

										var self = $(this);
										var classEl = "";

										var classes = $(this)[0].element.className.split(" ");
										$(classes).each(function() { 
									        var classElement = this + "";
									        if (classElement != "dropzone" && classElement != "dz-clickable" && classElement != "dz-started")
									        	classEl = classElement
									    });

										// Iterate through all the dropzone objects on the page until this one is reached
										var i = 0;
										$(".preview div.dropzone").each(function() {
											if ($(this).hasClass(classEl))
												return false;
											i++;
										});

										var index = GetSubstringIndex($("#Content_content").val(), "{image}", i + 1);

										// Remove the uploader
										$("." + classEl).remove();

										// Append the text to the item at that index
										var md = $("#Content_content").val();

										// Insert either Markdown or an image tag depending upon the user preference
										if ($(".preferMarkdown").val() == 1)
											md = splice(md, index, 7, "![" + response.filename + "](" + response.filepath +")");
										else
										{
											var text = $(".redactor_editor").html();
											md = splice(md, index, 7, "<img src=\"" + response.filepath +"\" />");
											var index2 = GetSubstringIndex($(".redactor_editor").html(), "{image}", i + 1);
											text = splice(text, index2, 7, "<img src=\"" + response.filepath +"\" />");
											console.log(text);
											$(".redactor_editor").html(text);
										}

										// Then modify the markdown
										$("#Content_content").val(md).change();

										// Then change the redactor view if it exists
										

										if(typeof(Storage)!=="undefined")
											localStorage.setItem("content-" + $("#Content_id").val(), md);
									}
								}
							});
		 				}
		 			});
				});

		 		$("#Content_content").change();

		 		function GetSubstringIndex(str, substring, n) {
				    var times = 0, index = null;

				    while (times < n && index !== -1) {
				        index = str.indexOf(substring, index+1);
				        times++;
				    }

				    return index;
				}

				function splice(str, idx, rem, s ) {
				    return (str.slice(0,idx) + s + str.slice(idx + Math.abs(rem)));
				};

		'); ?>