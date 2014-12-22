<?php echo '<?xml version="1.0" encoding="UTF-8" ?>'; ?>
<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	<channel>
		<atom:link href="<?php echo $url.Yii::app()->request->requestUri; ?>" rel="self" type="application/rss+xml" />
		<title><?php echo Cii::getConfig('name', Yii::app()->name); ?></title>
		<link><?php echo $url; ?></link>
		<description><?php echo Cii::getConfig('name', Yii::app()->name); ?> Blog</description>
		<language>en-us</language>
		<pubDate><?php echo date('D, d M Y H:i:s T'); ?></pubDate>
		<lastBuildDate><?php echo date('D, d M Y H:i:s T'); ?></lastBuildDate>
		<docs>http://blogs.law.harvard.edu/tech/rss</docs>
	
		<?php foreach ($data as $k=>$v): ?>
			<?php if ($v->password != '') { continue; } ?>
			<item>
				<title><?php echo htmlspecialchars(str_replace('/', '', $v['title']), ENT_QUOTES, "utf-8"); ?></title>
				<link><?php echo $url.'/'.htmlspecialchars(str_replace('/', '', $v['slug']), ENT_QUOTES, "utf-8"); ?></link>
				<description>
					<?php 
						$md = new CMarkdownParser; 
						echo htmlspecialchars(strip_tags($md->transform($v['extract'])), ENT_QUOTES, "utf-8"); 
					?>
				</description>
				<category><?php echo htmlspecialchars(Categories::model()->findByPk($v['category_id'])->name,  ENT_QUOTES, "utf-8"); ?></category>
				<author><?php echo Users::model()->findByPk($v['author_id'])->email; ?> (<?php echo Users::model()->findByPk($v['author_id'])->username; ?>)</author>
				<pubDate><?php echo date('D, d M Y H:i:s T', strtotime($v['created'])); ?></pubDate>
				<guid><?php echo $url.'/'.htmlspecialchars(str_replace('/', '', $v['slug']), ENT_QUOTES, "utf-8"); ?></guid>
				<?php if ($v['commentable']): ?>
					<comments><?php echo $url.'/'.htmlspecialchars(str_replace('/', '', $v['slug']), ENT_QUOTES, "utf-8");; ?>#comments</comments>
				<?php endif; ?>
			</item>
		<?php endforeach; ?>	
	</channel>
</rss>
