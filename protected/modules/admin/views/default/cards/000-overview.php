<?php
    // Posts Criteria
    $postsCriteria = new CDbCriteria;
    $postsCriteria->addCondition("vid=(SELECT MAX(vid) FROM content WHERE id=t.id)");
    $postsCriteria->addCondition('type_id=2');
    
    // Pages Criteria
    $pagesCriteria = new CDbCriteria;
    $pagesCriteria->addCondition("vid=(SELECT MAX(vid) FROM content WHERE id=t.id)");
    $pagesCriteria->addCondition('type_id=1');
     
    // Categories Criteria
    $categoriesCriteria = new CDbCriteria;
    $categoriesCriteria->addCondition('id!=1');
    
    // Needing Approval Comments
    $approval = new CDbCriteria;
    $approval->addCondition('approved=0');
    
    // Needing Approval Comments
    $flagged = new CDbCriteria;
    $flagged->addCondition('approved=-1');
?>
<div class="well span6 card">
    <h4>Blog Status and Overview</h4>
    <div class="left span6">
        <ul class="nav nav-list">
            <li class="nav-header">Content</li>
            <li><span class="bold red"><?php echo Content::model()->count($postsCriteria); ?></span> Posts</li>
            <li><span class="bold yellow"><?php echo Content::model()->count($pagesCriteria); ?></span> Pages</li>
            <li><span class="bold green"><?php echo Categories::model()->count($categoriesCriteria); ?></span> Categories</li>
            <li><span class="bold blue"><?php echo Users::model()->count(); ?></span> Users</li>
        </ul>
    </div>
    <div class="right span5">
        <ul class="nav nav-list">
            <li class="nav-header">Comments</li>
            <li><span class="bold purple"><?php echo Comments::model()->count(); ?></span> Comments</li>
            <li><span class="bold blue"><?php echo Comments::model()->count($approval); ?></span> Needing Approval</li>
            <li><span class="bold orange"><?php echo Comments::model()->count($flagged); ?></span> Flagged</li>
        </ul>
    </div>
</div>