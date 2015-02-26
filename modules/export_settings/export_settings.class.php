<?php
/**
 * Fusion Export Settings Module
 *
 * Allows exporting of settings for
 * backup or transfer purposes.
 *
 * @author ShopDev
 * @version 2.4
 *
 * Copyright (c) 2014
 * Licensed under the GPL-3.0 software license agreement
 */
class Export_Settings implements Module
{
	protected $value;
	protected $name = '';
	protected $title = '';
	protected $description = '';

	public function __construct($value, $setting)
	{
		$attributes = $setting['attributes'];

		$this->name = $attributes['name'];
		$this->value = $value;
		$this->title = $attributes['title'];

		if (isset($attributes['description'])) $this->description = $attributes['description'];
	}

	public function yield() // Not used
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

		// Return rendered template
		return $GLOBALS['smarty']->fetch('export_settings.tpl');
	}

	public function validate()
	{
		if ($this->value === 'Export')
		{
			$data = $GLOBALS['fusion']->getData();
			unset($data['licensekey']);
			unset($data['export_settings']);
			unset($data['import_settings']);
			$encodedData = Fusion::encode($data);
			$skin = $GLOBALS['fusion']->getSkin();
			$filename = 'settings.'.$skin.'.'.date('Y-m-d_H-i', time());

			if ($encodedData)
			{
				$GLOBALS['debug']->supress(true); // We don't want the debugging mess in our file!
				header('Content-Type: application/octet-stream');
				header('Content-Disposition: attachment; filename='.$filename.'.fusion');
				echo $encodedData;
				exit;
			}

			return "Unable to generate settings file";
		}
		else
		{
			return true;
		}
	}
}
