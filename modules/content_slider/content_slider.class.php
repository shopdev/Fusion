<?php
/**
 * Fusion Content Slider Module
 *
 * Enables content sliders to be declared
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
class Content_Slider implements Module
{
	protected $value;
	protected $name = '';
	protected $slides = array();
	protected $enabled;

	public function __construct($value, $setting)
	{
		$this->value = $value;
		$attributes = $setting['attributes'];

		$this->name = $attributes['name'];
		$this->title = $attributes['title'];
		$this->enabled = isset($value['enabled']) ? $value['enabled'] : false;

		$this->setSlides();
	}

	public function yield()
	{
		$slides = array();

		if ($this->enabled)
		{
			foreach ($this->slides as $slide)
			{
				$slides[] = $slide['content_html'];
			}
		}

		return $slides;
	}

	public function paintConfiguration()
	{
		// Change template directory
		$GLOBALS['smarty']->template_dir = dirname(__FILE__);

		// Assign variables
		$GLOBALS['smarty']->assign('name', $this->name);
		$GLOBALS['smarty']->assign('title', $this->title);
		$GLOBALS['smarty']->assign('slides', $this->slides);
		$GLOBALS['smarty']->assign('enabled', $this->enabled);

		// Return rendered template
		return $GLOBALS['smarty']->fetch('content_slider.tpl');
	}

	public function setSettings()
	{
		// Unset empty slides
		// We don't want to store uncompleted "New Slide" forms
		if (is_array($this->value['slides']))
		{
			foreach ($this->value['slides'] as $key => $slide)
			{
				if (strlen(trim($slide['content_html'])) <= 0) unset($this->value['slides'][$key]);
			}
		}

		return $this->value;
	}

	public function validate()
	{
		return true;
	}

	protected function setSlides()
	{
		if (isset($this->value['slides']) && is_array($this->value['slides']))
		{
			foreach ($this->value['slides'] as $slide)
			{
				$this->slides[] = array(
					'content_html' => $slide['content_html']
				);
			}
		}
		elseif (is_array($this->value))
		{
			// Fusion V1 backwards compatibility
			foreach ($this->value as $slide)
			{
				if (isset($slide['content_html']))
				{
					$this->slides[] = array(
						'content_html' => $slide['content_html']
					);
				}
			}
		}
	}
}
