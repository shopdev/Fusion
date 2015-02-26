<?php
/**
 * Fusion Select Module
 *
 * Allows select fields to be
 * declared in a theme's Fusion
 * configuration file.
 *
 * @author ShopDev
 * @version 2.4
 *
 * Copyright (c) 2014
 * Licensed under the GPL-3.0 software license agreement
 */
class Select implements Module
{
	protected $name = '';
	protected $value;
	protected $title = '';
	protected $description = '';
	protected $options;

	public function __construct($value, $setting)
	{
		$attributes = $setting['attributes'];

		$this->value = $value;
		$this->name = $attributes['name'];
		$this->title = $attributes['title'];
		$this->options = $setting['option'];

		if (isset($attributes['description'])) $this->description = $attributes['description'];
	}

	public function getSelected()
	{
		foreach ($this->options as $option)
		{
			if (isset($option['attributes']['name']) && $option['attributes']['name'] == $this->value)
			{
				return $option['attributes']['name'];
			}
		}

		foreach ($this->options as $option)
		{
			if (isset($option['attributes']['name']) && self::parse_boolean($option['attributes']['default']) == true)
			{
				return $option['attributes']['name'];
			}
		}
	}

	public function paintConfiguration()
	{
		// Change template directory
		$GLOBALS['smarty']->template_dir = dirname(__FILE__);

		// Assign variables
		$GLOBALS['smarty']->assign('name', $this->name);
		$GLOBALS['smarty']->assign('title', $this->title);
		$GLOBALS['smarty']->assign('description', $this->description);
		$GLOBALS['smarty']->assign('options', $this->options);
		$GLOBALS['smarty']->assign('selected', $this->getSelected());

		// Return rendered template
		return $GLOBALS['smarty']->fetch('select.tpl');
	}

	protected function getOptionValues()
	{
		$optionValues = array();

		foreach ($this->options as $option)
		{
			$attributes = $option['attributes'];

			if (isset($attributes['name']))
			{
				$optionValues[] .= $attributes['name'];
			}
		}

		return $optionValues;
	}

	public function validate()
	{
		// Ensure that the value submitted
		// is one of the valid options
		if (!in_array($this->value, $this->getOptionValues()))
		{
			return "Invalid selection made for {$this->title}";
		}

		return true;
	}

	public function yield()
	{
		return $this->getSelected();
	}

	public static function parse_boolean($string)
	{
	    return filter_var($string, FILTER_VALIDATE_BOOLEAN);
	}
}
