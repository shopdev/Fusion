<?php
/**
 * Fusion Textarea Module
 *
 * Allows textarea fields to be
 * declared in a theme's Fusion
 * configuration file.
 *
 * @author ShopDev
 * @version 2.4
 *
 * Copyright (c) 2014
 * Licensed under the GPL-3.0 software license agreement
 */
class Textarea implements Module
{
	protected $name = '';
	protected $value;
	protected $title = '';
	protected $description = '';
	protected $rows = 10;
	protected $cols = 50;

	public function __construct($value, $setting)
	{
		$attributes = $setting['attributes'];

		$this->value = isset($value) ? $value : $attributes['default'];
		$this->name = $attributes['name'];
		$this->title = $attributes['title'];

		if (isset($attributes['rows'])) $this->rows = $attributes['rows'];
		if (isset($attributes['cols'])) $this->cols = $attributes['cols'];
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
		$GLOBALS['smarty']->assign('rows', $this->rows);
		$GLOBALS['smarty']->assign('cols', $this->cols);
		$GLOBALS['smarty']->assign('value', $this->value);

		// Return rendered template
		return $GLOBALS['smarty']->fetch('textarea.tpl');
	}

	public function validate()
	{
		if (strlen(trim($this->value)) <= 0) return "A value is required for {$this->title}";

		return true;
	}

	public function yield()
	{
		return $this->value;
	}
}
