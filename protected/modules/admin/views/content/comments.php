<?php $this->beginWidget('bootstrap.widgets.TbBox', array(
    'title' => 'Comments',
    'headerIcon' => 'icon-comment',
    'headerButtons' => array(
        array(
            'class' => 'bootstrap.widgets.TbButton',
            'type' => 'primary',
            'size' => 'small',
            'url' => '#comment-box', 
            'label' => 'Comment',
            'htmlOptions' => array(
                'style' => 'margin-right: 5px;'
            )
        )
    )
)); ?>
    <?php foreach($comments as $comment): ?>
        <div class="comment-container <?php echo $comment->content->author->id == $comment->author->id ? 'byself' : NULL; ?>">
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
    <?php endforeach; ?>
    <div id="new-comment"></div>
<?php $this->endWidget(); ?>

<?php $this->beginWidget('bootstrap.widgets.TbBox', array(
    'title' => 'New Comment',
    'headerIcon' => 'icon-leaf',
)); ?>
    <a id="comment-box"></a>
    <div id="sharebox" class="comment-box">
        <div id="a">
            <div id="textbox" contenteditable="true"></div>
            <div id="close"></div>
            <div style="clear:both"></div>
        </div>
        <div id="b" style="color:#999">Comment on this post</div> 
    </div>
    <?php $this->widget('bootstrap.widgets.TbButton', array(
        'type' => 'success',
        'label' => 'Submit',
        'url' => '#',
        'htmlOptions' => array(
            'id' => 'submit',
            'class' => 'sharebox-submit',
            'style' => 'display:none'
        )
    )); ?>
<?php $this->endWidget(); ?>


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

<?php Yii::app()->clientScript->registerScript('comment-box', '
    $("#b").click( function () {
        $(this).html("");
        $("#a").slideDown("fast");
        $("#submit").show();
        setTimeout(function() {
            $("#textbox").focus();
        }, 100);
    });
    $("#textbox").keydown( function() {
        if($(this).text() != "")
            $("#submit").css("background","#3b9000");
        else
            $("#submit").css("background","#9eca80");
        });
    $("#close").click( function () {
        $("#b").html("Comment on this post");
        $("#textbox").html("");
        $("#a").slideUp("fast");
        $("#submit").hide();
    });
    
    $("#submit").click(function(e) {
        e.preventDefault();
        if ($("#textbox").text() == "")
            return;
        
        $.post("../../../comments/comment/id/' . $content->id .'", { comment : $("#textbox").text() }, function(data, textStatus, jqXHR) {
            if (jqXHR.status == 200)
            {
                $("#new-comment").replaceWith(data);
                $(".comment-container:last").fadeIn();
                $("#textbox").html("");
                $("#close").click();
            }
            else
            {
                console.log(jqXHR);   
            }
        }); 
    });
'); ?>