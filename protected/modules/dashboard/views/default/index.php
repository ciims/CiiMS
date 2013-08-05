<?php $cs = Yii::app()->getClientScript(); ?>
<div class="dashboard">
	<div class="header">
		<div class="content">
			<div class="welcome">
				<strong>Welcome Back, </strong> <?php echo Yii::app()->user->displayName; ?>
			</div>
			<div class="header-nav">
				<?php echo CHtml::link('<span class="icon-plus"></span> Add Card', '#', array('id' => 'add-card')); ?>
				<?php echo CHtml::link('<span class="icon-pencil"></span> New Post', $this->createUrl('/dashboard/content/save')); ?>
				<?php echo CHtml::link('<span class="icon-search"></span> Search', '#'); ?>
			</div>
		</div>
	</div>
	<div class="clearfix push-header"></div>

	<div class="widget-selector settings-container hidden">
		<div class="sidebar">
			<div id="main" class="nano">
				<div class="content">
					<?php $this->widget('zii.widgets.CMenu', array(
						'htmlOptions' => array('class' => 'menu'),
						'items' => $cards['available_cards']
					)); ?>
				</div>
			</div>
		</div>
		<div class="body-content">
			<div id="main" class="nano">
				<div class="content">
					<!-- Display Nothing Here by Default -->
				</div>
			</div>
		</div>
	</div>

	<div class="widget-container"></div>
	<div class="shader"></div>

</div>
<?php $asset = Yii::app()->assetManager->publish(YiiBase::getPathOfAlias('application.extensions.cii.assets'), true, -1, YII_DEBUG); ?>
<?php $cs->registerScriptFile($this->asset.'/shapeshift/core/vendor/jquery.touch-punch.min.js', CClientScript::POS_END)
		 ->registerCssFile($this->asset.'/css/image-picker.css')
		 ->registerCssFile($asset.'/css/pure.css')
		 ->registerScriptFile($this->asset.'/js/image-picker.min.js', CClientScript::POS_END)
		 ->registerScriptFile($this->asset.'/shapeshift/core/jquery.shapeshift.js', CClientScript::POS_END)
		 ->registerScriptFile($this->asset.'/js/jquery.flippy.min.js', CClientScript::POS_END)
		 ->registerScriptFile($this->asset.'/js/jquery.nanoscroller.min.js', CClientScript::POS_END)
		 ->registerScript('nano', '$("#main.nano").nanoScroller();')
		 ->registerScript('add-card', '
		 	$("#add-card").click(function(e) {
		 		e.preventDefault();
		 			$(".widget-selector").toggleClass("hidden");

		 			if (!$(".widget-selector").hasClass("hidden"))
		 				$(this).html("<span class=\"icon-plus\"></span> Hide Card Menu");
		 			else
		 				$(this).html("<span class=\"icon-plus\"></span> Add Card");

		 			$(".menu li:first-child").addClass("active");
		 			window.location = $(".menu li:first-child").find("a").attr("href");

		 			$.post("' . $this->createUrl('/dashboard/default/getCardsByCategory/') . '/id/" + $(".menu li:first-child").find("a").attr("href").replace("#", ""), function(data) {
			 			$(".body-content #main .content").html(data);
			 			$("select").imagepicker();
			 			bindAddCardsButton();
			 		});
		 		return false;
		 	});

		 	function bindAddCardsButton()
		 	{
		 		$("#add-cards-button").click(function(e) {
		 			e.preventDefault();

		 			var items = $("select").val();
		 			if (items === null)
		 				return false;

		 			$(items).each(function(key, value) {
		 				$.post("' . $this->createUrl('/dashboard/card/add/') . '/id/" + value, function(data) {
		 					$(".widget-container").append(data);
         					enableDragBehavior();
         					bindResizeBehavior();
							bindDeleteBehavior();
							bindSettingsBehavior();
							bindFlipEvent();
							rebuild(true);
         					$("#add-card").click();
		 				});
		 			});
		 		});
		 	}
		 ')
		 ->registerScript('li-click', '
			$(".menu li").click(function() { 
				window.location = $(this).find("a").attr("href");

				$(".menu li").each(function() {
					$(this).removeClass("active");
				});

				$(this).addClass("active");
			});
		')
		 ->registerScript('getCards', '

		 	function rebuild(draggable)
		 	{
		 		$(".widget-container").trigger("ss-destroy").trigger("ss-rearrange").shapeshift({
				        minColumns: 3,
				        gutterX: 20,
				        gutterY: 20,
				        paddingX: 0,
				        paddingY: 0,
				        enableDrag : draggable
			        });
		 	}

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
				bindSettingsBehavior();
				bindFlipEvent();
		 	});
		 ')
		 ->registerScript('settingsBehavior', '
		 	function bindSettingsBehavior()
		 	{
		 		$(".shader").click(function() {
		 			$(this).fadeOut();
		 			$(".modal").fadeOut();
		 		});

		 		$(".icon-gear").click(function() {
		 			var parent = $(this).parent().parent();

		 			var id = $(parent).attr("id");
		 			var modal = $("." + id + "-modal");

		 			$(".widget-container").after(modal);

		 			$(".shader").fadeIn();
					$(modal).fadeIn();
		 		});
		 	}
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
         					setTimeout(function() { 
         						$(parent).remove(); 
         						$("#" + $(parent).attr("data-attr-id")).remove(); 
	         					enableDragBehavior();
	         					$(".widget-container").trigger("ss-rearrange");
         					}, 500);

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
					rebuild(true);
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
							bindSettingsBehavior();
							bindFlipEvent();
					    	$(settings).show();

					    	rebuild(false)

					    },
					    onReverseStart : function() {
					    	$(parent).after($(settings));
					    	$(settings).hide();

					    },
					    onReverseFinish : function() {
					    	bindResizeBehavior();
							bindDeleteBehavior();
							bindSettingsBehavior();
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