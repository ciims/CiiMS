<div class="comment-container <?php echo $comment->content->author->id == $comment->author->id ? 'byself' : NULL; ?>" style="display:none;">
    <div class="user-photo rounded-img"><?php echo CHtml::image($comment->author->gravatarImage(40)); ?></div>
    <div class="comment">
        <div class="info-row">
            <span class="name"><?php echo $comment->author->name; ?>:</span>
            <span class="timeago" title="<?php echo CTimestamp::formatDate('M d, Y @ h:i a', strtotime($comment->created)); ?>"><?php echo CTimestamp::formatDate('M d, Y @ h:i a', strtotime($comment->created)); ?></span>
            <span class="options">
                <?php echo CHtml::link('Delete', $this->createUrl('/admin/comments/delete/id/' . $comment->id), array('data-attr-id' => $comment->id, 'class'=>'delete-comment')); ?> |
                <?php echo CHtml::link($comment->approved == 1 ? 'Flag' : 'Approve', $this->createUrl('/admin/comments/approve/id/' . $comment->id), array('data-attr-id' => $comment->id, 'class' => 'flag-comment')); ?>
            </span>
        </div>
        <?php echo $md->safeTransform($comment->comment); ?>
    </div>
</div>
<div class="clearfix"></div>
<div id="new-comment"></div>
<?php $this->widget('ext.timeago.JTimeAgo', array(
    'selector' => ' .timeago',
)); ?>
<?php Yii::app()->clientScript->registerScript('rounded-corners', '
  $(".rounded-img, .rounded-img2").load(function() {
    $(this).wrap(function(){
      return \'<span class="\' + $(this).attr(\'class\') + \'" style="background:url(\' + $(this).attr(\'src\') + \') no-repeat center center; width: \' + $(this).width() + \'px; height: \' + $(this).height() + \'px;" />\';
    });
    $(this).css("opacity","0");
  });
'); ?>

<?php Yii::app()->clientScript->registerScript('delete-comment', '
    $(".delete-comment").click(function(e) {
       e.preventDefault();
       var element = $(this).parent().parent().parent().parent();
       $.post($(this).attr("href"), function(data, textStatus, jqXHR) {
           if (textStatus == "success")
               $(element).fadeOut();
       });
    });
'); ?>

<?php Yii::app()->clientScript->registerScript('flag-comment', '
    $(".flag-comment").click(function(e) {
       e.preventDefault();
       var element = $(this);
       $.post($(this).attr("href"), function(data, textStatus) {
           if (textStatus == "success")
           {
               if ($(element).text() == "Flag")
                   $(element).text("Approve");
               else
                   $(element).text("Flag");
           }
       });
    });
'); ?>