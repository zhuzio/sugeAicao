<?php
return array(
	//'配置项'=>'配置值'
	'DB_HOST' => '127.0.0.1', // 服务器地址
	'SESSION_AUTO_START' => true,
	'MODULE_ALLOW_LIST' => array('Home', 'Admin', 'Common', 'Wechat'),
	'DB_TYPE' => 'mysql', // 数据库类型
	'DB_NAME' => 'aicao',
	'DB_USER' => 'root', // 用户名
	'DB_PWD' => '', // 密码
	'DB_PREFIX' => 'ai_', // 数据库表前缀
	'URL_MODEL' => '3',
	'URL_HTML_SUFFIX' => 'shtml',
	// 默认模块名
	'DEFAULT_MODULE' => 'Home',
	// 默认控制器名
	'DEFAULT_CONTROLLER' => 'Login',
	// 默认操作名
	'DEFAULT_ACTION' => 'index',
	//'ERROR_PAGE' => PUBLIC_PATH . '404/error.html',
	'TMPL_ACTION_SUCCESS' => 'Public:success',
	'TMPL_ACTION_ERROR' => 'Public:error',
	'TMPL_L_DELIM' => '<{', // 模板引擎普通标签开始标记
	'TMPL_R_DELIM' => '}>', // 模板引擎普通标签结束标记
);