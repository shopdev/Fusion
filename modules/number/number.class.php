<?php
/**
 * Fusion Multiselect Module
 *
 * Allows numeric fields to be
 * declared in a theme's Fusion
 * configuration file.
 *
 * @author ShopDev
 * @version 2.4
 *
 * Copyright (c) 2014
 * Licensed under the GPL-3.0 software license agreement
 */
class Number implements Module
{
	protected $name = '';
	protected $title = '';
	protected $description = '';
	protected $value;
	protected $min = 0;
	protected $max;
	protected $step = 1;

	public function __construct($value, $setting)
	{
		$attributes = $setting['attributes'];

		$this->value = isset($value) ? (int)$value : (int)$attributes['default'];
		$this->name = $attributes['name'];
		$this->title = $attributes['title'];

		if (isset($attributes['min'])) $this->min = $attributes['min'];
		if (isset($attributes['max'])) $this->max = $attributes['max'];
		if (isset($attributes['step'])) $this->step = $attributes['step'];
		if (isset($attributes['description'])) $this->description = $attributes['description'];
	}

	public function yield()
	{
		return $this->value;
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
		$GLOBALS['smarty']->assign('min', $this->min);
		$GLOBALS['smarty']->assign('max', $this->max);
		$GLOBALS['smarty']->assign('step', $this->step);

		// Return rendered template
		return $GLOBALS['smarty']->fetch('number.tpl');
	}

	public function validate()
	{
		if ($this->value < $this->min) return "{$this->title} does not accept values less than {$this->min}";
		if (isset($this->max) && $this->value > $this->max) return "{$this->title} does not accept values greater than {$this->max}";

		return true;
	}
}
