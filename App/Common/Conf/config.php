<?php
return array(
//*************************************附加设置***********************************
	'SHOW_PAGE_TRACE' => false, // 是否显示调试面板
	'URL_CASE_INSENSITIVE' => false, // url区分大小写
	'TMPL_DETECT_THEME' => true,
	//'TMPL_EXCEPTION_FILE' => PUBLIC_PATH . '404/error.html',
	'LOAD_EXT_CONFIG' => 'db,smssettings,settings', // 加载网站设置文件
	'TMPL_PARSE_STRING' => array( // 定义常用路径
		'__HOME_CSS__' => PUBLIC_PATH . 'Home/css',
		'__HOME_PC_CSS__' => PUBLIC_PATH . 'Home/pc_css',
		'__HOME_BASS__' => PUBLIC_PATH . 'Home/bass',
		'__HOME_JS__' => PUBLIC_PATH . 'Home/js',
		'__HOME_LIB__' => PUBLIC_PATH . 'Home/lib',
		'__HOME_IMAGES__' => PUBLIC_PATH . 'Home/image',
		'__HOME_AI_CSS__' => PUBLIC_PATH . 'Home/zabxq/css',
		'__HOME_AI_JS__' => PUBLIC_PATH . 'Home/zabxq/js',
		'__HOME_AI_IMAGES__' => PUBLIC_PATH . 'Home/zabxq/img',
		'__ADMIN_LIB__' => PUBLIC_PATH . 'Admin/lib',
		'__UPLOAD__' => ROOT_PATH . 'Upload',

		/*'__ADMIN_CSS__' => __ROOT__ . trim(TMPL_PATH, '.') . 'Admin/Public/css',
			'__ADMIN_JS__' => __ROOT__ . trim(TMPL_PATH, '.') . 'Admin/Public/js',
			'__ADMIN_IMAGES__' => OSS_URL . trim(TMPL_PATH, '.') . 'Admin/Public/images',
			'__ADMIN_ACEADMIN__' => OSS_URL . __ROOT__ . '/Public/statics/aceadmin',
			'__PUBLIC_CSS__' => __ROOT__ . trim(TMPL_PATH, '.') . 'Public/css',
			'__PUBLIC_JS__' => __ROOT__ . trim(TMPL_PATH, '.') . 'Public/js',
			'__PUBLIC_IMAGES__' => OSS_URL . trim(TMPL_PATH, '.') . 'Public/images',
			'__APP_CSS__' => __ROOT__ . trim(TMPL_PATH, '.') . 'App/Public/css',
			'__APP_JS__' => __ROOT__ . trim(TMPL_PATH, '.') . 'App/Public/js',
		*/
	),
	//***********************************SESSION设置**********************************
	'SESSION_OPTIONS' => array(
		'name' => 'sgaicao', //设置session名
		'expire' => 24 * 3600, //SESSION保存1天
		'use_trans_sid' => 1, //跨页传递
		'use_only_cookies' => 0, //是否只开启基于cookies的session的会话方式
	),
);
