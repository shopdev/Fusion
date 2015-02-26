<?php
/**
 * Fusion Single Switch Module
 *
 * Allows single switch toggle
 * buttons to be declared in a
 * theme's Fusion configuration
 * file.
 *
 * @author ShopDev
 * @version 2.4
 *
 * Copyright (c) 2014
 * Licensed under the GPL-3.0 software license agreement
 */
class Single_Switch implements Module
{
	protected $name = '';
	protected $value;
	protected $title = '';
	protected $description = '';

	public function __construct($value, $setting)
	{
		$attributes = $setting['attributes'];

		$this->value = isset($value) ? $value : self::parse_boolean($attributes['default']);
		$this->name = $attributes['name'];
		$this->title = $attributes['title'];

		if (isset($attributes['description'])) $this->description = $attributes['description'];
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

		// Return rendered template
		return $GLOBALS['smarty']->fetch('single_switch.tpl');
	}

	public function validate()
	{
		if (!in_array($this->value, array('on', '')))
		{
			return "Invalid value entered for {$this->title}";
		}

		return true;
	}

	public function yield()
	{
		if ($this->value == 'on') return true;
		return false;
	}

	public static function parse_boolean($string)
	{
	    return filter_var($string, FILTER_VALIDATE_BOOLEAN);
	}
}
