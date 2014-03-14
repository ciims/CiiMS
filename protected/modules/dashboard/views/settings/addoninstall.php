<div class="modal-container">
	<div class="image-banner">
		<?php echo CHtml::image($details['screen_shot']); ?>
	</div>

	<?php if (!$this->isInstalled($id)): ?>
		<a href="#" class="pure-button pure-button-primary download-button" data-attr-id="<?php echo $id; ?>">
			<?php echo Yii::t('Dashboard.views', 'Install Addon'); ?>
		</a>
	<?php else: ?>
		<span class="pure-button pure-button-success download-button">
			<?php echo Yii::t('Dashboard.views', 'This Addon is already installed.'); ?>
		</span>
	<?php endif; ?>

	<div class="description">
		<h2><?php echo $details['name']; ?></h2>
		<?php echo $md->safeTransform($details['description']); ?>
	</div>
</div>

<script type="text/javascript">
	$(document).ready(function() {
		$("a.download-button").click(function(e) {
			e.preventDefault();

			// Indicate the the addon is installing
			$(this).html('<span class="icon-spinner icon-spin" style="margin-right: 10px;"></span>' + "<?php echo Yii::t('Dashboard.views', 'Installing Addon...'); ?>");
			var self = this;

			// Perform the installation request
			$.get(CiiDashboard.endPoint + "/<?php echo $type; ?>/install/id/<?php echo $id; ?>", function(data) {
				if (data.status == 200)
				{
					$(self).html("<?php echo Yii::t('Dashboard.views', 'Success! Refresh the page to see your changes.'); ?>").unbind("click").addClass("pure-button-success").removeClass("pure-button-primary");

					$(self).click(function() {
						window.location.reload();
					})
				}
			});
			
			// Ask the user to reload the page

			return false;
		});
	});
</script>