<?php

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
class theme
{

/**
 * Path to where themes are stored
 */
	var $themesPath = null;

/**
 * URL to themes
 */
	var $themesURL = null;

/**
 * Custom theme name if used
 */
	var $themeName = null;

/**
 * File extension including . for template files
 */
	var $templateExtension = '.phtml';

/**
 * Layout Name
 */
	var $layoutName = 'layout';

/**
 * Main template to render
 */
    var $templateName = '';

/**
  * Stores assigned variables
  * @access protected
  */
	var $_vars = array();

/**
 * Stores internal list of rendered templates
 * @access private
 */
    var $_rendered = array();

/**
 * Constructor
 * @param $themes_path The path to the themes directory
 * @param $themes_url The url to the themes directory
 * @param $custom_theme (optional) name of a custom theme to use
 */
	function __construct($themes_path, $themes_url, $custom_theme = null)
	{
		$this->themesPath = $themes_path;
		$this->themesURL = $themes_url;
		$this->themeName = $custom_theme;
	}

/**
 * php 4 constructor
 */
    function theme($themes_path, $themes_url, $custom_theme = null)
    {
        $this->__construct($themes_path, $themes_url, $custom_theme);
    }

/**
 * Get the URL to this theme's files
 */
    function themesURL()
    {
        return $this->app->config->siteurl . 'modules/' . $this->app->moduleName . '/themes';
    }
/**
 * Gets the filename of the template to render.
 * Checks if the template exists in the custom theme folder, the current theme folder
 * and lastly the default theme folder.
 * @param $template_name The name of the template
 * @return The filename of the template to render
 */
	function getTemplateFilename($template_name)
	{
  	if( !$template_name ) {
			return '';
		}
		$template_filename = $this->themesPath . '/custom/' . $template_name . $this->templateExtension;
		if( !file_exists($template_filename) ) {
			$template_filename = $this->themesPath . '/' . $this->themeName . '/' . $template_name . $this->templateExtension;
			if( !$this->themeName || !file_exists($template_filename ) ) {
				$template_filename = $this->themesPath . '/default/' . $template_name . $this->templateExtension;
			}
		}
		return $template_filename;
	}
/**
 * Render a template
 * @param The name of the template to render, including any optional subdirectory
 * @return The output from rendering the template
 */
	function render($template_name)
	{
        extract($GLOBALS);
		extract($this->_vars);
		ob_start();
        $template = $this->getTemplateFilename($template_name);
        $this->_rendered[$template_name] = $template;
        if( $template ) {
    		include $template;
        	return ob_get_clean();
        }
        else{
            return '';
        }
	}

/**
 * Render a layout
 * @return The rendered layout
 */
	function renderTheme()
	{
		$this->assign('sideContent', $this->render('sidecontent'));
		if( $this->templateName ) {
			$this->templateContent = $this->render($this->templateName);
		}
		else {
			$this->_rendered[$this->templateName] = 'no render';
		}
		if( $this->layoutName ) {
			$output = $this->render($this->layoutName);
		}
		else{
			$output = $this->templateContent;
		}
		return $output;
	}

	function __set($name, $value)
	{
		$this->_vars[$name] = $value;
	}

	function __isset($name)
	{
		return isset($this->_vars[$name]);
	}

	function __get($name)
	{
		if( isset($this->_vars[$name]) ) {
			return $this->_vars[$name];
		}
	}

    function assign($var_name, $var_value)
    {
        $this->_vars[$var_name] = $var_value;
    }

		/**
	  * Generates a URL
	  * @param $action the "action" or page to view
	  * @param $query_string The query string to add
	  * @return A URL relative to the url of the site
	  */
	function url($action = null, $query_string = null, $module = null)
	{
		return $this->app->url($action, $query_string, $module);
	}

    function staticUrl($file, $module = null)
    {
        return $this->app->staticUrl($file, $module);
    }
    
	/**
	 * Translate some text
	 * @param Text to translate
	 * @return The translated text (or original if no translation exists)
	 */
	function _t($token)
	{
		return $this->app->translate($token);
	}

	/**
	 * Translate and escape for output the given text token
	 * @param The text token to translate
	 * @return The translated token escaped for output
	 */
	function t($token)
	{
		return $this->escape($this->app->translate($token));
	}

	function escape($what)
	{
		return htmlspecialchars($what);
	}

    function helper($name)
    {
        return $this->app->helper($name);
    }

}