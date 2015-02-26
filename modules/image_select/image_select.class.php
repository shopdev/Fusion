<?php
/**
 * Fusion Textbox Module
 *
 * Allows textbox fields to be
 * declared in a theme's Fusion
 * configuration file.
 *
 * @author ShopDev
 * @version 2.4
 *
 * Copyright (c) 2014
 * Licensed under the GPL-3.0 software license agreement
 */
class Image_Select implements Module
{
	protected $name = '';
	protected $title = '';
	protected $description = '';
	protected $value;
	protected $directory;
	protected $background_images = array();

	public function __construct($value, $setting)
	{
		$attributes = $setting['attributes'];

		$this->value = isset($value) ? $value : $attributes['default'];
		$this->name = $attributes['name'];
		$this->title = $attributes['title'];
		$this->directory = str_replace('{SKIN}', $GLOBALS['gui']->getSkin(), $attributes['directory']);

		if (isset($attributes['description'])) $this->description = $attributes['description'];

		// Build list of background images
		if (file_exists(CC_ROOT_DIR.CC_DS.$this->directory))
		{
			foreach(glob(CC_ROOT_DIR.CC_DS.$this->directory."/*.{jpg,gif,png}", GLOB_BRACE) as $path)
			{
				$this->background_images[] = basename($path);
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
		$GLOBALS['smarty']->assign('value', $this->value);
		$GLOBALS['smarty']->assign('background_images', $this->background_images);

		// Return rendered template
		return $GLOBALS['smarty']->fetch('image_select.tpl');
	}

	public function validate()
	{

		if ($this->value == '' || in_array($this->value, $this->background_images)) return true;

		return "Please select an image from the list for {$this->title}";
	}

	public function yield()
	{
		if ($this->value != '')
		{
			return 'url("' . $this->directory . '/' . $this->value . '")';
		}

		return $this->value;
	}
}
