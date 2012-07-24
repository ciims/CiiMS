<?php header ("content-type: text/xml"); ?>
<?php $url = 'http://'.Yii::app()->request->serverName . Yii::app()->baseUrl; ?>
<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
	<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<?php foreach ($content as $v): ?>
		<?php if ($v->password != '') { continue; } ?>
		<url>
			<loc><?php echo $url .'/'. htmlspecialchars(str_replace('/', '', $v['slug']), ENT_QUOTES, "utf-8"); ?></loc>
			<lastmod><?php echo date('c', strtotime($v['updated']));?></lastmod>
			<changefreq>weekly</changefreq>
			<priority><?php echo $v['type_id'] == 1 ? '0.6': '0.8'; ?></priority>
		</url>
	<?php endforeach; ?>
	<?php foreach ($categories as $v): ?>
		<url>
			<loc><?php echo $url .'/'. htmlspecialchars(str_replace('/', '', $v['slug']), ENT_QUOTES, "utf-8"); ?></loc>
			<lastmod><?php echo date('c', strtotime($v['updated']));?></lastmod>
			<changefreq>weekly</changefreq>
			<priority>0.7</priority>
		</url>
	<?php endforeach; ?>
	<url>
		<loc><?php echo $url .'/projects'; ?></loc>
		<lastmod><?php echo date('c', strtotime('now'));?></lastmod>
		<changefreq>monthly</changefreq>
		<priority>0.5</priority>
	</url>
	</urlset>
