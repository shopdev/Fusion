<?php
/**
 * Fusion Import Settings Module
 *
 * Allows importing of settings for
 * backup restoration or transfer
 * purposes.
 *
 * @author ShopDev
 * @version 2.4
 *
 * Copyright (c) 2014
 * Licensed under the GPL-3.0 software license agreement
 */
class Import_Settings implements Module
{
	protected $name = '';
	protected $value;
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
		return false;
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
		return $GLOBALS['smarty']->fetch('import_settings.tpl');
	}

	public function validate()
	{
		if ($this->value['tmp_name'])
		{
			if ($this->value['error'] == UPLOAD_ERR_OK && is_uploaded_file($this->value['tmp_name']))
			{
				$file_contents = @file_get_contents($this->value['tmp_name']);
				$data = $GLOBALS['fusion']->decode($file_contents);

				if (($data) !== false || base64_decode($file_contents) === 'b:0;')
				{
					unset($data['export_settings']);
					unset($data['import_settings']);
					unset($data['licensekey']);
					$GLOBALS['fusion']->setSettings($data);

					if ($GLOBALS['fusion']->validateAll() == true)
					{
						return $GLOBALS['fusion']->save();
					}
					else
					{
						return "Settings file failed validation";
					}
				}
				else
				{
					return "Malformed settings file";
				}

			}

			return "Unable to import settings";
		}

		return true; // Nothing uploaded (no import requested), so no validation required!
	}
}
