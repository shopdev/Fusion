<?php
/**
 * Fusion Multiselect Module
 *
 * Allows multiselect fields to be
 * declared in a theme's Fusion
 * configuration file.
 *
 * @author ShopDev
 * @version 2.4
 *
 * Copyright (c) 2014
 * Licensed under the GPL-3.0 software license agreement
 */
class Multiselect implements Module
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

	public function yield()
	{
		return $this->getSelected();
	}

	public function getSelected()
	{
		$selected = array();

		foreach ($this->options as $option)
		{
			if (isset($option['attributes']['name']) && is_array($this->value) && in_array($option['attributes']['name'], $this->value))
			{
				$selected[] .= $option['attributes']['name'];
			}
		}

		return $selected;
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
		return $GLOBALS['smarty']->fetch('multiselect.tpl');
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
		if (is_array($this->value))
		{
			foreach ($this->value as $selected)
			{
				if (!in_array($selected, $this->getOptionValues()))
				{
					return "Invalid selection made for {$this->title}";
				}
			}
		}

		return true;
	}

	public static function parse_boolean($string)
	{
	    return filter_var($string, FILTER_VALIDATE_BOOLEAN);
	}
}
