<?php

    /**
     * Settings API
     *
     * @package ccwp
     */

    namespace ccwp\api;

    class Settings
    {
        /**
    	 * Settings array
    	 *
    	 * @var private array
    	 */
    	private static $settings = array();

    	/**
    	 * Sections array
    	 *
    	 * @var private array
    	 */
    	private static $sections = array();

    	/**
    	 * Fields array
    	 *
    	 * @var private array
    	 */
    	private static $fields = array();

    	/**
    	 * Script path
    	 *
    	 * @var string
    	 */
    	private static $scriptPath;

    	/**
    	 * Enqueues array
    	 *
    	 * @var private array
    	 */
    	private static $enqueues = array();

    	/**
    	 * Admin pages array to enqueue scripts
         *
    	 * @var private array
    	 */
    	private static $enqueueOnPages = array();

    	/**
    	 * Admin pages array
         *
    	 * @var private array
    	 */
    	private static $adminPages = array();

    	/**
    	 * Admin subpages array
         *
    	 * @var private array
    	 */
    	private static $adminSubpages = array();

    	/**
    	 * Contrusct class to activate actions and hooks as soon as the class is initialized
    	 */
    	public function __construct()
    	{
    		if (!empty(self::$enqueues))
    			add_action('admin_enqueue_scripts', array($this, 'adminScripts'));

    		if (!empty(self::$adminPages) || !empty(self::$adminSubpages))
    			add_action('admin_menu', array($this, 'addAdminMenu'));

    		if (!empty(self::$settings))
    			add_action('admin_init', array($this, 'registerCustomSettings'));
    	}

    	/**
    	 * Dinamically enqueue styles and scripts in admin area
    	 *
    	 * @param  array  $scripts file paths or wp related keywords of embedded files
    	 * @param  array $page    pages id where to load scripts
    	 */
    	public static function adminEnqueue($scripts = array(), $pages = array())
    	{
    		if (empty($scripts))
    			return;

    		$i = 0;
    		foreach ($scripts as $key => $value):
    			foreach ($value as $val):
    				self::$enqueues[$i] = self::enqueueScript($val, $key);
    				$i++;
    			endforeach;
    		endforeach;

    		if (!empty($pages)):
    			self::$enqueueOnPages = $pages;
    		endif;
    	}

    	/**
    	 * Call the right WP functions based on the file or string passed
    	 *
    	 * @param  array $script  file path or wp related keyword of embedded file
    	 * @param  var $type      style | script
    	 * @return variable functions
    	 */
    	private static function enqueueScript($script, $type)
        {
    		if ($script === 'media_uplaoder')
    			return 'wp_enqueue_media';

    		return ($type === 'style') ? array('wp_enqueue_style' => $script) : array('wp_enqueue_script' => $script);
    	}

    	/**
    	 * Print the methods to be called by the admin_enqueue_scripts hook
    	 *
    	 * @param  var $hook      page id or filename passed by admin_enqueue_scripts
    	 */
    	public function adminScripts($hook)
    	{
    		// dd($hook);
    		self::$enqueueOnPages = (!empty(self::$enqueueOnPages)) ? self::$enqueueOnPages : array($hook);
    		if (in_array($hook, self::$enqueueOnPages)):
    			foreach (self::$enqueues as $enqueue):
    				if ($enqueue === 'wp_enqueue_media'):
    					$enqueue();
    				else:
    					foreach ($enqueue as $key => $val):
    						$key($val, $val);
    					endforeach;
    				endif;
    			endforeach;
    		endif;
    	}

    	/**
    	 * Injects user's defined pages array into $adminPages array
    	 *
    	 * @param  var $pages      array of user's defined pages
    	 */
    	public static function addAdminPages($pages)
    	{
    		self::$adminPages = $pages;
    	}

    	/**
    	 * Injects user's defined pages array into $adminSubpages array
    	 *
    	 * @param  var $pages      array of user's defined pages
    	 */
    	public static function addAdminSubpages($pages)
    	{
    		self::$adminSubpages = $pages;
    	}

    	/**
    	 * Call WordPress methods to generate Admin pages and subpages
    	 */
    	public function addAdminMenu()
    	{
    		foreach (self::$adminPages as $page):
    			add_menu_page($page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback'], $page['icon_url'], $page['position']);
    		endforeach;

    		foreach(self::$adminSubpages as $page):
    			add_submenu_page($page['parent_slug'], $page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['callback']);
    		endforeach;
    	}

    	/**
    	 * Injects user's defined settings array into $settings array
    	 *
    	 * @param  var $args      array of user's defined settings
    	 */
    	public static function addSettings($args)
    	{
    		self::$settings = $args;
    	}

    	/**
    	 * Injects user's defined sections array into $sections array
    	 *
    	 * @param  var $args      array of user's defined sections
    	 */
    	public static function addSections($args)
    	{
    		self::$sections = $args;
    	}

    	/**
    	 * Injects user's defined fields array into $fields array
    	 *
    	 * @param  var $args      array of user's defined fields
    	 */
    	public static function addFields($args)
    	{
    		self::$fields = $args;
    	}

    	/**
    	 * Call WordPress methods to register settings, sections, and fields
    	 */
    	public function registerCustomSettings()
    	{
    		foreach (self::$settings as $setting):
    			register_setting($setting["option_group"], $setting["option_name"], (isset($setting["callback"]) ? $setting["callback"] : ''));
    		endforeach;

    		foreach (self::$sections as $section):
    			add_settings_section($section["id"], $section["title"], (isset($section["callback"]) ? $section["callback"] : ''), $section["page"]);
    		endforeach;

    		foreach (self::$fields as $field):
    			add_settings_field($field["id"], $field["title"], (isset($field["callback"]) ? $field["callback"] : ''), $field["page"], $field["section"], (isset($field["args"]) ? $field["args"] : ''));
    		endforeach;
    	}
    }

?>
