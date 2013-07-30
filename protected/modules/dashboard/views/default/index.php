<?php $cs = Yii::app()->getClientScript(); ?>
<div class="dashboard">
	<div class="header">
		<div class="content">
			<div class="welcome">
				<strong>Welcome Back, </strong> <?php echo Yii::app()->user->displayName; ?>
			</div>
			<div class="header-nav">
				<?php echo CHtml::link('<span class="icon-pencil"></span> New Post', $this->createUrl('/dashboard/content/save')); ?>
				<?php echo CHtml::link('<span class="icon-search"></span> Search', '#'); ?>
			</div>
			<?php echo CHtml::tag('span', array('id' => 'add-card', 'class' => 'icon-plus pull-right'), NULL); ?>
		</div>
	</div>
	<div class="clearfix push-header"></div>

	<div class="widget-container">

	</div>
</div>
<?php $cs->registerScriptFile($this->asset.'/shapeshift/core/vendor/jquery.touch-punch.min.js', CClientScript::POS_END)
		 ->registerScriptFile($this->asset.'/shapeshift/core/jquery.shapeshift.js', CClientScript::POS_END)
		 ->registerScriptFile($this->asset.'/js/jquery.flippy.min.js', CClientScript::POS_END)
		 ->registerScript('getCards', '

		 	$.get("' . $this->createUrl('/dashboard/card/getCards'). '", function(data) {
		 		$(".widget-container").html(data).shapeshift({
			        minColumns: 3,
			        gutterX: 20,
			        gutterY: 20,
			        paddingX: 0,
			        paddingY: 0
		        });

				bindResizeBehavior();
				bindDeleteBehavior();
				bindFlipEvent();
		 	});
		 ')
         ->registerScript('resizeBehavior', '
         	function bindResizeBehavior()
         	{
				$(".icon-resize-full").click(function() {
					var parent = $(this).parent().parent();
					var sizes = $(parent).attr("data-attr-sizes").split(",");
					var classEl = $(parent).attr("class").replace("card-", "").replace("ss-active-child", "").replace(/\s+/g, "");
					
					var i = sizes.indexOf(classEl);

					if (sizes.length - 1 == i)
						var newClass = "card-" + sizes[0];
					else
						var newClass = "card-" + sizes[i+1];

					if (newClass == "card-normal")
						$(parent).attr("data-ss-colspan", "1");
					else
						$(parent).attr("data-ss-colspan", "2");

					$(parent).removeClass("card-" + classEl).addClass(newClass);

					$(".widget-container").trigger("ss-rearrange");

					$.post("' . $this->createUrl('/dashboard/card/resize/id/'). '/" + $(parent).attr("id"), { activeSize : newClass});
				});
         	}')
        ->registerScript('deleteBehavior', '
         	function bindDeleteBehavior()
         	{
         		$(".icon-trash").click(function()
         		{
         			var parent = $(this).parent().parent();

         			$.post("' . $this->createUrl('/dashboard/card/delete/id/'). '/" + $(parent).attr("data-attr-id"), function(data, textStatus) {
         				if (textStatus == "success")
         				{
         					$(parent).fadeOut();
         					setTimeout(function() { $(parent).remove(); $("#" + $(parent).attr("data-attr-id")).remove(); }, 500);
         					$(".widget-container").trigger("ss-rearrange");
         					enableDragBehavior();
         				}
         			});
         		});
         	}')
        ->registerScript('enableDragBehavior', '
        	function enableDragBehavior()
        	{
        		// Prevent dragging until all settings menus are hidden
				var visible = false; 
				$(".settings").each(function() { 
					if (!visible && $(this).is(":visible"))
						visible = true; 
				});

				if (!visible)
				{
					$(".widget-container").trigger("ss-destroy").shapeshift({
				        minColumns: 3,
				        gutterX: 20,
				        gutterY: 20,
				        paddingX: 0,
				        paddingY: 0,
				        enableDrag : true
			        });
				}
			}
        ')
        ->registerScript('bindFlipEvent', '
        	function bindFlipEvent()
        	{
	        	$(".icon-flip").click(function() {
	        		var parent = $(this).parent().parent();
	        		
	        		var settings = $("." + $(parent).attr("id") + "-settings");

	        		$(parent).flippy({
				 		color_target : "#FFF",
					    duration: "500",
					    verso: $(settings),
					    onFinish : function() {
					    	bindResizeBehavior();
							bindDeleteBehavior();
							bindFlipEvent();
					    	$(settings).show();

					    	$(".widget-container").trigger("ss-destroy").shapeshift({
						        minColumns: 3,
						        gutterX: 20,
						        gutterY: 20,
						        paddingX: 0,
						        paddingY: 0,
						        enableDrag : false
					        });

					    },
					    onReverseStart : function() {
					    	$(parent).after($(settings));
					    	$(settings).hide();

					    },
					    onReverseFinish : function() {
					    	bindResizeBehavior();
							bindDeleteBehavior();
							bindFlipEvent();

							enableDragBehavior();
					    }
					 });
	        	});

		        $(".icon-reverse-flip").click(function() {
		        	var parent = $(this).parent().parent().parent();
		        	$(parent).flippyReverse();
		        });
	   		}'); ?>