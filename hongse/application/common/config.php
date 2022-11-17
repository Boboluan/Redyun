<?php
return array(
	'DB_TYPE' => 'mysql',
//	'DB_HOST'=>'39.99.136.180',   //39.99.136.180
	'DB_HOST' => 'localhost',
	'DB_NAME' => 'car',
	'DB_USER' => 'root',
//	'DB_PWD'=>'0cdd24062e',
	'DB_PWD' => 'iuhnahs2020',
	'DB_PORT' => '3306',
	'DB_PREFIX' => 'kepu_',
	'DB_CHARSET' => 'utf8',
	'DEFAULT_CHARSET' => 'utf8',
	'COOKIE_PREFIX' => 'BkGOp9578O' . '_',
	'VERSION' => '2.2',
	'URL_MODEL' => 3, //rewrite模式改为2
	'URL_ROUTER_ON' => true,
	'URL_HTML_SUFFIX' => '.html',
	'URL_PATHINFO_DEPR' => '_',
	'TMPL_FILE_DEPR' => '_',
	'DEFAULT_THEME' => 'default',
	'TMPL_DETECT_THEME' => true, // 自动侦测模板主题
	'HTML_CACHE_ON' => false, // 默认关闭静态缓存
	'TMPL_CACHE_ON' => false, // 是否开启模板编译缓存,设为false则每次都会重新编
	'URL_CASE_INSENSITIVE' => true, //URL不区分大小写
	'IS_BUILD_HTML' => 0,
	'TMPL_PARSE_STRING' => array(
		'__WEB__' => __ROOT__ . '',
		'__ARTICLE__' => __ROOT__ . '/index.php?s=articles_',
		'__TYPE__' => __ROOT__ . '/index.php?s=lists_',
		'__VOTE__' => __ROOT__ . '/index.php?s=votes_',
		'__TPL__' => __ROOT__ . '/Kepu/Tpl',
	),
);

?>
