<?php $htmlOptions = array('class' => 'pure-input-2-3'); ?>
<ul class="providers">
	<?php foreach ($model->groups() as $group=>$keys): ?>
		<li class="provider">
			<div class="tile" data-name="<?php echo str_replace(".", "_", str_replace(" ", "_", $group)); ?>">
				<span class="title">
					<?php echo CHtml::image($this->asset.'/images/providers/' . $group .'/logo.png'); ?>
				</span>
				<?php $first = reset($keys); ?>
				<?php echo $form->toggleButtonRow($model, $first, $htmlOptions); ?>
				<span class="help">Click to view options</span>
			</div>
		</li>
	<?php endforeach; ?>
</ul>

<div class="transparent"></div>

<?php foreach ($model->groups() as $group=>$keys): ?>
	<div class="options-panel <?php echo str_replace(".", "_", str_replace(" ", "_", $group)); ?>">
		<legend>Attributes for <?php echo $group; ?></legend>
		<?php foreach ($keys as $key): ?>
			<div class="pure-control-group">
				<?php if (strpos($key, 'enabled') === false): ?>
					<?php echo $form->textFieldRowLabelFix($model, $key, $htmlOptions); ?>
				<?php endif; ?>
			</div>
		<?php endforeach; ?>
	</div>
<?php endforeach; ?>

<?php Yii::app()->getClientScript()->registerCss('analytics-form', 'main .settings-container .body-content #main .content { padding: 0px; }'); ?>

<?php Yii::app()->getClientScript()->registerScript('open-options', '

	$("label.checkbox.toggle input").click(function(e) {
		e.stopPropagation();

		var name = $(this).parent().parent().parent().attr("data-name");

		if ($(this).parent().find("[type=checkbox]").is(":checked"))
			window[name] = false;

		return;
	});

	$(".provider").click(function(e) {
		e.stopPropagation();

		// Get the current sidebarname
		var side = $(this).find(".tile").attr("data-name");

		if (typeof(window[side]) == "undefined")
			window[side] = true;

		if (!window[side])
		{
			window[side] = true;
			return;
		}

		// Hide the transparent overflow
		var top = ($(".content").scrollTop() - 6 + "px");

		console.log(top);

		$(".transparent").css("top", top).show();

		// Remove the active class from all options-panels
		$(".options-panel").removeClass("active");

		// Disable scrolling
		$("#main, .content").css("overflow", "hidden");
		$(".content").css("right", "0px");
		$(".nano").nanoScroller({ stop: true });

		$(".options-panel." + side).css("position", "absolute").css("top", top).animate({
	        right: 0
	    }, 300);
	});

	$(".transparent").click(function() {
		// Hide the transparent overflow
		$(".transparent").hide();

		// Remove the active class from all options-panels
		$(".options-panel").animate({
	        right: "-100%"
	    }, 500);

		// Restore scrolling
		$("#main, .content").css("overflow", "");
		$(".content").css("right", "-17px");
		$(".nano").nanoScroller({ stop: false });
	});
');