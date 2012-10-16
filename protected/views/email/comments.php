<?php echo $content->author->name; ?>,<br /><br />
<p>This is a notification informing you a new comment has been posted on your post: <?php echo $content->title; ?>.</p>
<p>
	<strong>From: </strong> <?php echo $comment->author->name; ?><br />
	<?php echo $comment->comment; ?>
</p>