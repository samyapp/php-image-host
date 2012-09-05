<?php
if( !defined('PIH' ) ) {
    header('HTTP/1.0 404 Not Found');
    exit();
}
/*

PHP Image Host
www.phpace.com/php-image-host

Copyright (c) 2004,2008,2009 Sam Yapp

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.

*/
require_once dirname(__FILE__) . '/action.class.php';
require_once dirname(__FILE__) . '/helper.php';

define('APP_DIR', realpath(dirname(__FILE__) . '/..'));
define('MODULE_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'modules');
define('CLASS_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'classes');
define('INCLUDE_DIR', APP_DIR . DIRECTORY_SEPARATOR . 'includes');

class app
{

	/**
	* Theme object (templates)
	*/
	var $theme = null;

	/**
	* Config object
	*/
	var $config = null;

	/**
	 * Action Object
	 */
	var $action = null;

	/**
	 * Name of the action to execute
	 */
	var $actionName = 'index';

	/**
	 * Database Object
	 */
	var $db = null;
	
	/**
	 * Has this app's action been successfully dispatched?
	 */
	var $dispatched = false;
	
	/**
	 * List of loaded class objects
	 */
	var $classes = array();
	
	/**
	 * Translations
	 */
	var $_translations = array();

	/**
	 * Module
	 */
	var $moduleName = 'default';

	/**
	 * Other modules
	 */
	var $modules = array('install', 'admin');

    /**
     * Helper class objects
     */
     var $helpers = array();

     var $startTime = 0;

	/**
	 * Contains names / urls of action urls for display in translation files
	 */
	var $urls = array();
	/**
	 * Constructor
	 * @param $db The database object
	 */
	function __construct()
	{
        $this->startTime = microtime(true);
	}

	function getActionsPath()
	{
		return $this->getModuleDir() . '/actions';
	}

	function getModuleDir()
	{
		$path = MODULE_DIR . '/' . $this->moduleName;
		return $path;
	}
	
	/**
	 * php 4 constructor
	 * @param $db The database object
	 */
	function app()
	{
		$this->__construct();
	}

	/**
	 * Sets up default module stuff
	 */
	function initDefaultModule()
	{
		// some stuff from the old config.inc.php file that should be moved elsewhere...

		require_once(dirname(__FILE__).'/users.class.php');
		$this->users = new users($this);

		$this->users->resetbandwidth();
		$this->users->checkbandwidth();

		// user login init stuff
		require_once dirname(__FILE__) . '/userSession.class.php';
		$this->userSession = new userSession($this);
		$this->userSession->init();

		// more initialization stuff

		$sql = "SELECT * FROM account_types  ";
		$res = $this->query($sql, 'Get Account Types');

		if( mysql_num_rows($res)<2 ) die("Error retrieving account details from database.");

		$plans = array();
		while( $p = mysql_fetch_object($res) ) $plans[$p->type_type] = $p;
		$this->config->price_1 = $plans['paid']->cost_1;
		$this->config->price_3 = $plans['paid']->cost_3;
		$this->config->price_6 = $plans['paid']->cost_6;
		$this->config->price_12 = $plans['paid']->cost_12;

        $plans['anon'] = $plans['anonymous'];

        $vars = array();
		foreach( $this->config as $n=>$v ){
			if( !is_array($v) ) $vars[$n] = $v;
		}
		foreach( $plans as $type=>$p ){
			foreach( $p as $n=>$v ){
				$vars[$type.'_'.$n] = $v;
				if( in_array($n, array('bandwidth', 'storage', 'max_images') ) && $v == 0 )$vars[$type.'_'.$n] = 'unlimited';
			}
		}

		$uptype = $this->userSession->loggedin ? $this->userSession->user->account_type : 'free';
		foreach( $plans[$uptype] as $n=>$v ){
			$vars['user_'.$n] = $v;
		}
		$vars['username'] = $vars['email'] = '';
		$vars['paid_account_name'] = $plans['paid']->type_name;
		$vars['paid_account_cost'] = number_format($plans['paid']->cost_1,2);
		$vars['free_account_name'] = $plans['free']->type_name;
		if( $this->userSession->user ){
			$vars['username'] = $this->userSession->user->username;
			$vars['email'] = $this->userSession->user->email;
			foreach( $this->userSession->user as $n=>$v ){
				if( !is_array($v) && !isset($vars['user_'.$n])){
					$vars['user_'.$n] = $v;
				}
				if( in_array($n, array('bandwidth', 'storage', 'max_images') ) && $v == 0 ){
					$vars['user_'.$n] = 'unlimited';
				}
			}
		}
		else {
			foreach( array('user_images', 'user_bandwidth_used', 'user_storage_used') as $n ) {
				$vars[$n] = 0;
			}
		}
		$this->theme->assign('plans', $plans);
		$this->pageContentVars = $vars;
	}

    function initAdminModule()
    {
        $adminloggedin = false;
        $username = '';
        $config = $this->config;
        if( $this->getParamStr('login') != '' ){
        	$username = $this->getParamStr('username');
        	$password = $this->getParamStr('password');
        	if( strcmp($username, $config->admin_username) == 0 && strcmp($password, $config->admin_password) == 0){
        		$_SESSION['admin_loggedin'] = true;
        	}
        }elseif( $this->getParamStr('logout') != '' ){
        	unset($_SESSION['admin_loggedin']);
        }

        if( isset($_SESSION['admin_loggedin']) ) $adminloggedin = $_SESSION['admin_loggedin'];

        if( !$adminloggedin ){
?>
<html>
<head>
<title>Php Image Host Admin</title>
<style>
body{
    text-align: center;
    font-family: tahoma, verdana, arial;
}

form{
    margin: auto;
    width: 30em;
    text-align: left;
    margin-top: 5em;
}

fieldset{
        border: 1px solid #acacac;
    background-color: #f0f0f0;

}
legend{
    font-weight: bold;
    font-family: helvetica;
    font-size: 1.5em;
}

label{
    font-weight: bold;
}

input{
    clear: left;
    width: 96%;
    border: 1px solid #acacac;
    font-size: 1.1em;
    padding: 0.5em;
    background-color: white;
}
</style>
</head>
<body onLoad="document.forms['loginform'].username.focus();">
<form name="loginform" action="<?php echo $this->url('index')?>" method="post">
    <fieldset>  
        <legend>Admin Login</legend>
        <p>
            <label for="username">Username</label>
            <input type="text" name="username" id="username" />
        </p>
        <p>
            <label for="pword">Password</label>
            <input type="password" name="password" id="password" />
        </p>
        <input type="submit" name="login" value="Login" />
    </fieldset>
</form>
</body>
</html>
<?php
            exit();
        }
    }

	/**
	 * Inits default objects if not already setup
	 */
	function init()
	{
		if( !$this->db ) {
			$this->initDb();
		}
		if( !$this->config ) {
			$this->initConfig();
		}
		if( !$this->theme ) {
			$this->initTheme();
		}
        $this->initDefaultModule();
        if( $this->moduleName == 'admin' ) {
            $this->initAdminModule();
        }
        foreach( array('index', 'join', 'browse','upload','upgrade','faq',
							'terms', 'privacy-policy', 'contact','images',
							'login', 'remind', 'galleries') as $action ) {
			$this->urls[$action] = $this->url($action);
            $this->urls['images'] = $this->url('myimages');
            $this->urls['myimages'] = $this->url('myimages');
		}
		foreach( $this->userSession as $k => $v ) {
			$this->theme->assign($k, $v);
		}
		$this->initLanguage();
	}

	/**
	 * Setup the language / translation stuff
	 */
	function initLanguage()
	{
		extract($this->pageContentVars);
		$urls = $this->urls;
        if( file_exists($this->getModuleDir() . '/languages/default.php')) {
    		include $this->getModuleDir() . '/languages/default.php';
    		$this->_translations = $lang;
    		// if extra language specified...
    	}
        if( $this->config->language != 'default' && file_exists($this->getModuleDir().'/languages/'.$this->config->language.'.php')) {
            include $this->getModuleDir() . '/languages/'.$this->config->language.'.php';
            $this->_translations = array_merge($this->_translations, $lang);
        }
        if( file_exists($this->getModuleDir().'/languages/custom.php')) {
            include $this->getModuleDir() . '/languages/custom.php';
            $this->_translations = array_merge($this->_translations, $lang);
        }
    }

	function translate($token)
	{
		if( isset($this->_translations[$token]) ) {
			return $this->_translations[$token];
		}
		else {
			return $token;
		}
	}

    function staticUrl($file, $module = null)
    {
        if( $module == null ) {
            $module = $this->moduleName;
        }
        if( !$module ) {
            $module = 'default';
        }
        $themeDir = MODULE_DIR . DIRECTORY_SEPARATOR . $module . DIRECTORY_SEPARATOR . 'themes' . DIRECTORY_SEPARATOR;
        $filename = $themeDir . $file;
        $url = '';
        if( file_exists($filename)){
//            $url = $this->url('static/'.filemtime($filename).'/'.$module . '/themes/'.$file, null,'default');
            $url = $this->config->siteurl.'modules/'.$module.'/themes/'.$file;
        }
        return $url;
    }

	/**
	  * Generates a URL
	  * @param $action the "action" or page to view
	  * @param $query_string The query string to add
	  * @return A URL relative to the url of the site
	  */
	function url($action = null, $query_string = null, $module = null)
	{
		if( $module == null ) {
			$module = $this->moduleName;
		}
		$base = '';//$this->config->siteurl;
		$command = array();
		if( $module != 'default' && $module != '' ) {
			$command[] = $module;
		}
		if( !is_null($action) && $action != 'index' ) {
			$command[] = $action;
		}
        $query_string = preg_replace('#[a-z0-9]+=&#i', '', $query_string);
		if( count($command) ) {
			$base .= '?cmd=' . join('/', $command);
            if( $query_string ) {
                $base .= '&' . $query_string;
            }
		}
        elseif( $query_string ) {
            $base .= '?' . $query_string;
        }
        if( $this->config->rewrite_urls ) {
            $base = str_replace('?', '', $base);
            $base = str_replace('cmd=', '', $base);
            $base = str_replace(array('&', '='), array('/', '/'), $base);
        }
		return $this->config->siteurl . $base;
	}
	
	/**
	 * Setup the db object
	 */
	function initDb()
	{
        $conf = INCLUDE_DIR . '/db.conf.php';
        if( file_exists($conf) ) {
    		include INCLUDE_DIR . '/db.conf.php';
        }
        else {
            $db_settings = array('host'=>'localhost','name'=>'','user'=>'','password'=>'');
        }
        require_once CLASS_DIR . '/database.php';
		$this->db = new database($db_settings);
	}
	
	/**
	 * Sets up the default theme object
	 */
	function initTheme()
	{
		// setup the theme / template engine
		require_once CLASS_DIR . '/theme.class.php';
		$this->theme = new theme($this->getModuleDir().'/themes', $this->config->siteurl.'modules/'.$this->moduleName.'/themes', $this->config->theme);
		$this->theme->assign('config', $this->config);
	}
	
	/**
	 * Sets up the default config object
	 */
	function initConfig()
	{
		$this->config = $this->loadsettings();
		if( !$this->config ){
            $this->install();
            exit();
        }
		if( !$this->config->show_errors ){
			error_reporting(E_ALL ^ (E_WARNING | E_NOTICE));
		}
	}

    function doStatic($url)
    {
				$fp = fopen(dirname(__FILE__).'/log.txt', 'a');
				if( $fp ) {
					fwrite($fp, "\n".date('H:i:s')." $url ");
				}
        if( preg_match('#^static/[0-9]+/(.+)\.(css|js|gif|ico|png|jpg)$#i', $url, $match ) ) {
            $file = $match[1].'.'.$match[2];
            $path = MODULE_DIR . DIRECTORY_SEPARATOR . $file;
            if( !preg_match('#\.\.#', $path) ) {
                $lastModified = filemtime($path);
                header('Pragma:');
                header('Last-Modified: ' . gmdate('D, d M Y H:i:s', $lastModified) . ' GMT');
                header("Expires: ".gmdate("D, d M Y H:i:s", $lastModified+315360000)." GMT");
                header("Cache-Control: max-age=315360000");
                if( isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
                    header('Status: 304 Not Modified');
										fwrite($fp, 'NOT MODIFIED');
                    exit();
                }

                header("Status: 200 Ok");
                if( in_array($match[2], array('css','js') ) ) {
                    $base_url = preg_replace('#[a-z0-9_-]+$#i', '', $match[1]);
                    $lines = file($path);
                    $content = join("\n", $lines);
                    $s = array('{site_url}', '{themes_url}', '{base_url}');
                    $r = array();
                    $r[] = $this->config->siteurl;
                    $r[] = $this->config->siteurl.'modules/'.preg_replace('#^([a-z0-9_-]+/[a-z0-9_-]+/).*$#i', '$1', $base_url);
                    $r[] = $this->config->siteurl.'modules/'.$base_url;
                    $content = str_replace($s, $r, $content);
                    $contenttype = 'text/'.($match[2] == 'css' ? 'css' : 'javascript');
            				header("Content-Type: $contenttype");
                    header('Content-Length:'.strlen($content));
										fwrite($fp, 'Modified');
                    echo $content;
                }
                else {
//										fwrite($fp, 'Image');

                    header("Content-Type: image/".$match[2]);
                    readfile($path);
                }
            }
						else {
//							fwrite($fp, 'Dodgy Dir');
						}
        }
        else {
//					fwrite($fp, 'Not Static');
        }
        exit();
    }
	
	/**
	 * Process the command string
	 */
	function processCommand($command)
	{
		$this->moduleName = 'default';
		$parts = explode('/', $command);
		if( in_array($parts[0], $this->modules) ) {
			$this->moduleName = $parts[0];
			array_shift($parts);
		}
		$action_name = 'index';
		if( count($parts) > 0 ) {
			$action_name = array_shift($parts);
		}
        if( $action_name == 'static' ) {
            $this->doStatic($command);
        }
        else {
            // start sessions
            session_start();
        }
		$this->setActionName($action_name);
		$this->params = array();
		for( $i = 0; $i < count($parts) - 1; $i+=2 ) {
			$this->params[$parts[$i]] = $parts[$i+1];
		}
//		echo "Module: {$this->moduleName}<br />Action: {$this->actionName}<br />";print_r($this->params);
	}

	/**
	 * Run the specified action
	 * @param $command The command to run
	 */
	function run($command)
	{
		if( !$this->db ) {
			$this->initDb();
		}
		if( !$this->config ) {
			$this->initConfig();
		}
		$this->processCommand($command);
		$this->init();
		if( $this->userSession->banned ) {
			$this->setActionName('banned');
		}
        elseif( $this->moduleName != 'admin' && $this->moduleName != 'bandwidth' && $this->config->disable_site ) {
            $this->setActionName('disabled');
        }
		do{
			$this->initAction();
			$this->runAction();
		}while( !$this->dispatched && $this->action );
	}
	
	/**
	 * Sets the action to run by name
	 * @param $action_name The name of the action
	 */
	function setActionName($action_name)
	{
		if( !$this->action || $action_name != $this->actionName ) {
			$this->dispatched = false;
			$this->actionName = $action_name;
		}
	}
	
	/**
	 * Inits the action stored in actionName
	 */
	function initAction()
	{
		if( $this->actionName ) {
			$this->action = $this->loadAction($this->actionName);
			if( $this->action ) {
				$this->theme->templateName = $this->actionName;
			}
		}
	}

	/**
	 * Loads an action
	 * @param $action_name The name of the action to load
	 * @return The action object or null
	 */
	function loadAction($action_name)
	{
		$action = null;
		if( preg_match('#^[a-z0-9_-]+$#', $action_name ) ) {
			$action_class = str_replace('-', '_',$action_name) . 'Action';
			if( !class_exists($action_class) ) {
				$action_file = $this->getActionsPath() . '/' . str_replace('-', '_',$action_name) . 'Action.php';
				if( file_exists( $action_file ) ) {
					require_once $action_file;
				}
			}
			if( class_exists($action_class) ) {
				$action = new $action_class($this);
			}
		}
		return $action;
	}
	
	/**
	 * Runs the current action
	 */
	function runAction()
	{
		if( $this->action ) {
			if( $this->action->requireLogin && !$this->userSession->loggedin ) {
				header("Location: ".$this->url('login'));
				exit();
			}
			$this->action->init();
			$this->dispatched = true;
			$this->action->run();
		}
	}
	
	/**
	  * Gets an instance of a class
	  * @param The name of the class
	  */
	function loadClass($name)
	{
		if( !isset($this->classes[$name]) ) {
			if( !class_exists($name) ) {
				$path = CLASS_DIR . '/' . $name . '.class.php';
				require_once $path;
			}
			if( class_exists($name) ) {
				$this->classes[$name] = new $name($this);
				$this->$name = $this->classes[$name];
			}
		}
		if( isset($this->classes[$name]) ) {
			return $this->classes[$name];
		}
		return null;
	}
	
	/** Old stuff from ace class which needs to be moved somewhere else eventually  **/

	function loadsettings($settings = array()){
		$conf = 0;
		if( !is_array($settings) ) $settings = array($settings);
		if( count($settings) == 0 ){
			$sql = "SELECT * ";
		}else{
			$sql = "SELECT ".join(',',array_values($settings))." ";
		}
		$sql .= "FROM settings LIMIT 0,1 ";
        // don't die if there is an error because we want to install if no settings
		$res = $this->query($sql, 'Load Settings', false);
        if( $res ) {
    		if( mysql_num_rows($res) == 1){
    			$conf = mysql_fetch_object($res);
    			if( !$this->config ){
        			$this->config = $conf;
            	}else{
                	foreach( $conf as $c=>$v ) $this->config->$c = $v;
                }
			}
		}
		return $conf;
	}

	function savesettings($sets = array()){
		if( !is_array($sets) ) $sets = array($sets);
		$ups = array();
		if( count($sets) == 0 ){
			foreach( $this->config as $n=>$v ) {
                if( !preg_match('#^price#', $n )) {
                    $ups[] = "$n='".mysql_real_escape_string($v)."' ";
                }
            }
		}else{
			foreach( $sets as $n ) $ups[] = "$n='".mysql_real_escape_string($this->config->$n)."' ";
		}
		if( count($ups) > 0 ){
			$sql = "UPDATE {pa_dbprefix}settings SET ".join(", ",$ups)." ";
			$this->query($sql, 'Save Settings');
			return true;
		}
		return false;
	}

	function query($sql, $action = 'unspecified', $die_on_error = true){
		// no support yet for database table prefix...
        return $this->db->query($sql, $action, $die_on_error);
	}

	function getParam($name, $default = '')
	{
		if( isset($this->params[$name]) ) {
			return $this->params[$name];
		}
		elseif( isset($_REQUEST[$name]) ) {
			return $_REQUEST[$name];
		}
		else {
			return $default;
		}
	}

	function getParamStr($name, $default = '')
	{
		$result = trim($this->getParam($name, null));
		if( $result != null ) {
			if( get_magic_quotes_gpc() ) {
				$result = stripslashes($result);
			}
		}
		else {
			$result = $default;
		}
		return $result;
	}

	function getParamInt($name, $default = 0)
	{
		return (int)$this->getParam($name, $default);
	}

	function getParamDouble($name, $default = 0)
	{
		return (double)$this->getParam($name, $default);
	}

	function getids($ids){
		$tmp = array();
		if( !is_array($ids) ) $ids = array($ids);
		foreach( $ids as $i ){
			settype($i, 'integer');
			if( $i != 0 ) $tmp[] = $i;
		}
		return $tmp;
	}

	function validateemail($email){  
		if (eregi("(@.*@)(\.\.)|(@\.)|(\.@)|(^\.)", $email) ||  
			!eregi ("^.+\@(\[?)[-_a-zA-Z0-9\.]+\.([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)$", $email)) {  
			return(false);  
		}else{
			list($user, $domain) = explode("@", $email);  
			if ((!eregi("^[_a-zA-Z0-9\.\-]+$", $user)) ||  
				(!eregi("^[_a-zA-Z0-9\.\-]+$", $domain))) {  
				return false;  
			} else {
				return(true);  
			}
		}
	}

	function selectoptions($arr, $cur){
		foreach( $arr as $n=>$v ){
			$sel = $cur == $n ? ' SELECTED ' : '';
			echo '<option value="'.htmlspecialchars($n).'"'.$sel.'>'.htmlspecialchars($v).'</option>'."\n";
		}
	}

	function banips($ips){
		$is = array();
		foreach( $ips as $i ){
			if( preg_match('/^[0-9]([0-9.]*)[0-9]$/i', $i) ) $is[$i] = true;
		}
		$ips = array_keys($is);
		if( count($ips) > 0 ){
			$is = array();
			foreach( $ips as $i ) $is[] = "('".mysql_real_escape_string($i)."') ";
			$sql = "INSERT IGNORE INTO {pa_dbprefix}banned_ips (ip) VALUES ".join(",",$is)." ";
			$this->query($sql, 'Ban Ips');
			return mysql_affected_rows();
		}
		return 0;
	}

	function ipbanned(){
		$ip = isset($_SERVER['X_FORWARDED_FOR']) && $_SERVER['X_FORWARDED_FOR'] != '' ? $_SERVER['X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
		$dotcount = substr_count($ip, '.');
		$ips = array();
		while( $dotcount >= 0 ){
			$ips[] = "'".preg_replace('/[^0-9.]/i', '', $ip)."'";
			$ip = substr($ip, 0, strrpos($ip, '.'));
			$dotcount--;
		}
		if( count($ips) > 0 ){
			$sql = "SELECT COUNT(*) FROM {pa_dbprefix}banned_ips WHERE ip IN (".join(",",$ips).") ";
			$res = $this->query($sql, 'Check IP');
			if( !mysql_result($res,0,0) ) return false;
		}
		return true;
	}

	function unbanips($ips){
		$is = array();
		foreach( $ips as $i ){
			if( preg_match('/^[0-9]([0-9.]*)[0-9]$/i', $i) ) $is[$i] = true;
		}
		$ips = array_keys($is);
		if( count($ips) > 0 ){
			$is = array();
			foreach( $ips as $i ){
				$is[] = "'".mysql_real_escape_string($i)."'";
			}
			$sql = "DELETE FROM {pa_dbprefix}banned_ips WHERE ip IN (".join(",",$is).") ";
			$this->query($sql, 'Unban Ips');
			return mysql_affected_rows();
		}
		return 0;

	}

	function getbannedips(){
		$sql = "SELECT ip FROM {pa_dbprefix}banned_ips ORDER BY ip ";
		$res = $this->query($sql, 'Get Ips');
		$ips = array();
		while( list($ip) = mysql_fetch_row($res) ) $ips[] = $ip;
		return $ips;
	}

/**
 * Two stages:
 * 1) Ensure database connection information is correct
 * 2) Create tables and insert default data
 */
    function install()
    {
        $sql = "SHOW TABLES";
        $result = $this->db->query($sql, 'show tables', false);
?>
<html>
    <head>
    <title>PHP Image Host Installer</title>
    <link rel="stylesheet" type="text/css" href="instyles.css" />
    </head>
    <body>
        <h1>PHP Image Host Installer</h1>
        <p><a href="docs/install.htm" target="_blank">Installation Instructions</a></p>
<?php
        // a database error occurred - presumably incorrect connection settings
        if( !$result ) {
?>
    <h1>Database Settings Invalid</h1>
<?php if( file_exists(INCLUDE_DIR . '/db.conf.php')) { ?>
    <p>Please check the settings in your database configuration file
        <br /><br />
        <?php echo INCLUDE_DIR . DIRECTORY_SEPARATOR?>db.conf.php
    </p>
    <p>
    The current settings give the error:
    <?php echo $this->db->lastError?>

    </p>
<?php
    }else {
    ?>
    <p>The file <?php echo INCLUDE_DIR. DIRECTORY_SEPARATOR . 'db.conf.php'?> does not appear to exist.</p>
    <p>Please copy the db.conf.php.sample file to db.conf.php and enter your database settings.</p>
    <?php
    }
        }
        // database connection settings are ok...
        else {
            $installed = false;
            $installer = $this->loadClass('installer');
            $error = false;
            if( !$installer ) {
                die('No installer found');
            }
            if( isset($_POST['install']) ) {
                if( $installer->setOptions($_POST['install']) ) {
                    // create the database tables
                    require_once INCLUDE_DIR . DIRECTORY_SEPARATOR . 'dbschema.inc.php';
                    $ok = $this->db->setupSchema($dbschema);
                    if( $ok ) {
                        // get the installer to do any post installation stuff
                        $installed = $installer->postInstall();

                    }
                    else {
                        $installed = false;
                        $error = $this->db->lastError;
                    }
                }
                else {
                     echo 'installing...';
                }
            }
            if( !$installed ) {
                // was there a database error?
                if( $error ) {
?>
    <h2>An Error Occurred :(</h2>
    <p>Sorry, an error has occurred whilst setting up the database tables. The error message was<br /><br />
        <b><?php echo htmlspecialchars($this->db->lastError)?></b>
    </p>
<?php
                }
                else {
                    $installer->render();
                }
            }
            else {
                echo <<<EOF
    <h1>Installation complete :)</h1>
    <p>Please now go to the site admin to change your password and configure your website</p>
    <p>Login with username "admin" and password "password"</p>
    <p><a href="?cmd=admin">Admin Control Panel</a></p>
    <p><a href="http://forum.phpace.com/" target="_blank">Visit the forum</a> for more information and support.</p>
EOF;
            }
        }
?>
    </body>
</html>
<?php
    }

    function poweredBy()
    {
        $strings = array(
            'image hosting script',
            'php image hosting script',
            'free image hosting script',
            'photo album script',
            'image host script',
            'php image host script',
            'php image host',
            'photo gallery script',
            'image gallery script'
        );
        $i = ord($_SERVER['HTTP_HOST'][0]) - ord('a');
        $i2 = $i % count($strings);
        return $strings[$i2];
    }

    function helper($name)
    {
        if( !isset($this->helpers[$name]) ) {
            if( !class_exists($name.'Helper') ) {
                require_once APP_DIR . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . $name.'Helper.php';
            }
            $class_name = $name.'Helper';
            $this->helpers[$name] = new $class_name($this);
        }
        return $this->helpers[$name];
    }

    function getTimeTaken($precision = 2)
    {
        return number_format(microtime(true) - $this->startTime, $precision);
    }
}
