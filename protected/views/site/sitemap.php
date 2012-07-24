<? header ("content-type: text/xml"); ?>
<?php $url = 'http://'.Yii::app()->request->serverName . Yii::app()->baseUrl; ?>
<? echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>
	<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
	<? foreach ($content as $v): ?>
		<? if ($v->password != '') { continue; } ?>
		<url>
			<loc><? echo $url .'/'. htmlspecialchars(str_replace('/', '', $v['slug']), ENT_QUOTES, "utf-8"); ?></loc>
			<lastmod><? echo date('c', strtotime($v['updated']));?></lastmod>
			<changefreq>weekly</changefreq>
			<priority><? echo $v['type_id'] == 1 ? '0.6': '0.8'; ?></priority>
		</url>
	<? endforeach; ?>
	<? foreach ($categories as $v): ?>
		<url>
			<loc><? echo $url .'/'. htmlspecialchars(str_replace('/', '', $v['slug']), ENT_QUOTES, "utf-8"); ?></loc>
			<lastmod><? echo date('c', strtotime($v['updated']));?></lastmod>
			<changefreq>weekly</changefreq>
			<priority>0.7</priority>
		</url>
	<? endforeach; ?>
	<url>
		<loc><? echo $url .'/projects'; ?></loc>
		<lastmod><? echo date('c', strtotime('now'));?></lastmod>
		<changefreq>monthly</changefreq>
		<priority>0.5</priority>
	</url>
	</urlset>
