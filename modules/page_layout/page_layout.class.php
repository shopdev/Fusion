<?php
/**
 * Fusion Page Layout Module
 *
 * An advanced module that allows themes
 * to declare multiple templates, each
 * of which may consist of multiple
 * regions that can house one or more
 * widgets.  Templates are declared on
 * a front-end section basis.  For instance,
 * one template may be available for
 * use on the store's homepage only.
 *
 * @author ShopDev
 * @version 2.4
 *
 * Copyright (c) 2014
 * Licensed under the GPL-3.0 software license agreement
 */
class Page_Layout implements Module
{
	protected $name = '';
	protected $title = '';
	protected $description = '';
	protected $value;
	protected $setting;
	protected $default = false;

	public function __construct($value, $setting)
	{
		$attributes = $setting['attributes'];
		$this->value = $value;
		$this->name = $attributes['name'];
		$this->title = $attributes['title'];
		$this->setting = $setting;

		if (isset($attributes['description'])) $this->description = $attributes['description'];
		if (isset($attributes['default']) && self::parse_boolean($attributes['default'])) $this->default = true;
	}

	public function yield()
	{
		$template = $this->value['template'];
		$regions = $this->value[$template];

		if (!$this->value['template']) return array();

		$return = array('template' => $this->value['template']);
		return (isset($regions)) ? array_merge($return, array('regions' => $regions)) : $return;
	}

	public function paintConfiguration()
	{
		// Change template directory
		$GLOBALS['smarty']->template_dir = dirname(__FILE__);

		// Assign variables
		$GLOBALS['smarty']->assign('name', $this->name);
		$GLOBALS['smarty']->assign('title', $this->title);
		$GLOBALS['smarty']->assign('description', $this->description);
		$GLOBALS['smarty']->assign('value', $this->value);

		$GLOBALS['smarty']->assign('default', self::parse_boolean($this->default));
		$GLOBALS['smarty']->assign('usedefault', self::parse_boolean($this->setting['attributes']['usedefault']));

		$GLOBALS['smarty']->assign('images_path', CC_STORE_URL.CC_DS.'skins'.CC_DS.$GLOBALS['fusion']->getSkin().CC_DS.'Fusion');

		// Default can't use default!
		$GLOBALS['smarty']->assign('can_use_default', $this->default !== true && (self::parse_boolean($this->setting['attributes']['usedefault']) !== false));

		// Clone options
		if (isset($this->setting['attributes']['clone']))
		{
			$clone = $this->setting['attributes']['clone'];

			if ($clone_settings = $GLOBALS['fusion']->getConfigSetting($clone))
			{
				if (is_array($clone_settings['template']))
				{
					if (!isset($this->setting['template'])) $this->setting['template'] = array();
					$this->setting['template'] = self::merge_arrays($this->setting['template'], $clone_settings['template']);
				}
			}
		}

		// Global widgets
		$globalDir = $GLOBALS['fusion']->skinPath().CC_DS.'templates'.CC_DS.'widgets'.CC_DS.'global'.CC_DS;
		$globalWidgets = array();

		foreach (glob($globalDir."*.{php,tpl}", GLOB_BRACE) as $path)
		{
			$globalWidgets[] = array(
				'attributes' => array(
					'name' => self::widgetName($path),
					'title' => self::widgetTitle($path)
				)
			);
		}

		foreach ($this->setting['template'] as $templateKey => $template)
		{
			foreach ($template['region'] as $regionKey => $region)
			{
				foreach ($globalWidgets as $globalWidget)
				{
					$this->setting['template'][$templateKey]['region'][$regionKey]['widget'][] = $globalWidget;
				}
			}
		}

		if (isset($this->setting['template'])) $GLOBALS['smarty']->assign('templates', $this->setting['template']);

		// Return rendered template
		return $GLOBALS['smarty']->fetch('page_layout.tpl');
	}

	public function validate()
	{
		return true;
	}

	public static function widgetName($file)
	{
		return str_replace(array('.php', '.tpl'), '', basename($file));
	}

	public static function widgetTitle($file)
	{
		return ucwords(str_replace(array('_', '-'), ' ', self::widgetName($file)));
	}

	public static function parse_boolean($string)
	{
	    return filter_var($string, FILTER_VALIDATE_BOOLEAN);
	}

	public static function merge_arrays($Arr1, $Arr2)
	{
		foreach($Arr2 as $key => $Value)
		{
			if (array_key_exists($key, $Arr1) && is_array($Value))
			{
				$Arr1[$key] = self::merge_arrays($Arr1[$key], $Arr2[$key]);
			}
			else
			{
				$Arr1[$key] = $Value;
			}
		}

		return $Arr1;
	}
}
