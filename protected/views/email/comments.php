<?php echo $content->author->displayName; ?>,<br /><br />
<p>This is a notification informing you a new comment has been posted on your post: <em><?php echo $content->title; ?></em>.</p>
<p>
	<strong>From: </strong> <?php echo $comment->author->displayName; ?><br />
	<?php echo $comment->comment; ?>
</p>
