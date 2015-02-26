<?php
/**
 * Fusion Image Slider Module
 *
 * Enables image sliders to be declared
 * in a theme's Fusion configuration file.
 * Declared sliders can then be configured
 * through Fusion's configuration panel.
 *
 * @author ShopDev
 * @version 2.4
 *
 * Copyright (c) 2014
 * Licensed under the GPL-3.0 software license agreement
 */
class Image_Slider implements Module
{
	protected $value;
	protected $name = '';
	protected $enabled;
	protected $slides = array();
	protected $images = array();
	protected $background_images = array();

	public function __construct($value, $setting)
	{
		$this->value = $value;
		$attributes = $setting['attributes'];

		$this->name = $attributes['name'];
		$this->title = $attributes['title'];
		$this->enabled = isset($value['enabled']) ? $value['enabled'] : false;

		if (isset($value['slides']) && is_array($value['slides']))
		{
			foreach ($value['slides'] as $slide)
			{
				$this->slides[] = array(
					'image' => $slide['image'],
					'background_color' => $slide['background_color'],
					'background_image' => $slide['background_image'],
					'url' => $slide['url']
				);
			}
		}

		// Build list of background images
		foreach(glob($GLOBALS['fusion']->skinPath()."/images/backgrounds/*.{jpg,gif,png}", GLOB_BRACE) as $path)
		{
			$this->background_images[] = basename($path);
		}

		// Path to slide images directory
		$imagesPath = CC_ROOT_DIR.'/images/source/slides/';

		// Create slides directory if it doesn't exist
		if (!is_dir($imagesPath)) mkdir($imagesPath, chmod_writable(), true);

		// Build list of slide images
		foreach (glob($imagesPath . "*.{jpg,gif,png}", GLOB_BRACE) as $path)
		{
			$this->images[] = substr(str_replace(CC_ROOT_DIR, '', $path), 1);
		}
	}

	public function yield()
	{
		return ($this->enabled) ? $this->slides : array();
	}

	public function paintConfiguration()
	{
		// Change template directory
		$GLOBALS['smarty']->template_dir = dirname(__FILE__);

		// Assign variables
		$GLOBALS['smarty']->assign('name', $this->name);
		$GLOBALS['smarty']->assign('title', $this->title);
		$GLOBALS['smarty']->assign('slides', $this->slides);
		$GLOBALS['smarty']->assign('background_images', $this->background_images);
		$GLOBALS['smarty']->assign('images', $this->images);
		$GLOBALS['smarty']->assign('enabled', $this->enabled);

		// Return rendered template
		return $GLOBALS['smarty']->fetch('image_slider.tpl');
	}

	public function setSettings()
	{
		// Unset empty slides
		// We don't want to store uncompleted "New Slide" forms
		if (isset($this->value['slides']) && is_array($this->value['slides']))
		{
			foreach ($this->value['slides'] as $key => $slide)
			{
				if (strlen(trim($slide['image'])) <= 0) unset($this->value['slides'][$key]);
			}
		}

		return $this->value;
	}

	public function validate()
	{
		return true;
	}
}
