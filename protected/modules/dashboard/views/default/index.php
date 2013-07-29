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
         	}

         	bindResizeBehavior()')
        ->registerScript('deleteBehavior', '
         	function bindDeleteBehavior()
         	{
         		$(".icon-trash").click(function()
         		{
         			var parent = $(this).parent().parent();

         			$.post("' . $this->createUrl('/dashboard/card/delete/id/'). '/" + $(parent).attr("id"), function(data, textStatus) {
         				if (textStatus == "success")
         				{
         					$(parent).remove();
         					$(".widget-container").trigger("ss-rearrange");
         				}
         			});
         		});
         	}

         	bindDeleteBehavior();
        '); ?>