    <?php $md = new CMarkdownParser(); ?>
    <div id="content-well" class="well span4">
        <h4><?php echo CHtml::encode($data->title); ?></h4>
        <div class="author-block">
            <h6 class="nav-header pull-right"><?php echo CHtml::encode($data->author['displayName']); ?></h6>
        </div>
        <div class="clearfix"></div>
        <div class="content"><?php echo $md->safeTransform($data['content']); ?></div>
        
        <div class="author-block footer-date">
            <h6 class="nav-header pull-left"><?php echo $data->status == 1 ? 'Published' : 'Draft'; ?></h6>
            <h6 class="nav-header pull-right"><?php echo $data->createdFormatted; ?></h6>
        </div>
        <div class="footer">
            <div class="pull-left">
                <?php $this->widget('bootstrap.widgets.TbButtonGroup', array(
                    'htmlOptions' => array(
                        'id' => 'comment-button'
                    ),
                    'buttons'=>array(
                        array('label'=>$data->getCommentCount(), 'url'=>$this->createUrl('/admin/content/comments/id/' . $data['id']), 'icon'=>'icon-comment', 
                        	'htmlOptions' => array( 'style' => $data['commentable'] == 1 ?: 'display:none;')),
                        array('label'=>$data['like_count'], 'url'=>'#', 'icon'=>'icon-heart')
                    ),
                ));
                ?>
            </div>
            <div class="pull-right">
                <?php $this->widget('bootstrap.widgets.TbButtonGroup', array(
                    'buttons'=>array(
                        array('label'=>'', 'url'=>$this->createUrl('/admin/content/save/id/' . $data['id']), 'icon'=>'icon-edit'),
                        array('label'=>'', 'url'=>$this->createUrl('/' . $data['slug']), 'icon'=>'icon-eye-open'),
                        array('label'=>'', 'url'=>$this->createUrl('/admin/content/delete/id/' . $data['id']), 'icon'=>'icon-trash')
                    ),
                ));
                ?>
            </div>
        </div>
    </div>
<?php if (($index + 1) % 3 == 0): ?>
</div>
<div class="items">
<?php endif; ?>
