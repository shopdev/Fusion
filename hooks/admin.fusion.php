<?php
/**
 * Fusion Configuration Panel
 *
 * Theme configuration panel page.
 *
 * @author ShopDev
 * @version 2.4
 *
 * Copyright (c) 2014
 * Licensed under the GPL-3.0 software license agreement
 */
require_once CC_ROOT_DIR.CC_DS.'modules'.CC_DS.'plugins'.CC_DS.'Fusion'.CC_DS.'classes'.CC_DS.'xmlwrapper.class.php';
require_once CC_ROOT_DIR.CC_DS.'modules'.CC_DS.'plugins'.CC_DS.'Fusion'.CC_DS.'classes'.CC_DS.'configuration.class.php';
require_once CC_ROOT_DIR.CC_DS.'modules'.CC_DS.'plugins'.CC_DS.'Fusion'.CC_DS.'classes'.CC_DS.'fusion.class.php';
require_once CC_ROOT_DIR.CC_DS.'modules'.CC_DS.'plugins'.CC_DS.'Fusion'.CC_DS.'classes'.CC_DS.'module.class.php';

Admin::getInstance()->permissions('Fusion', CC_PERM_READ, true);

$skin = $_GET['skin'];

// Get the current skin being configured
$GLOBALS['smarty']->assign('SKIN', $skin);

// Create fusion instance
$GLOBALS['fusion'] = new Fusion($skin);
$GLOBALS['smarty']->assign('fusion', $GLOBALS['fusion']);

// Set helper paths
$GLOBALS['smarty']->assign('PUBLIC_PATH', $GLOBALS['fusion']->publicPath());
$GLOBALS['smarty']->assign('PLUGIN_PATH', $GLOBALS['fusion']->pluginPath());

// Parses the skin's fusion configuration
// file and returns an associative array.
$structure = $GLOBALS['fusion']->getStructure();

// Get the list of installed skins that
// are powered by the fusion framework.
$skins = $GLOBALS['gui']->listSkins();
$skinList = array();

foreach ($skins as $i => $skin)
{
	if (file_exists(CC_ROOT_DIR.CC_DS.'skins'.CC_DS.$skin['info']['name'].CC_DS.'Fusion'.CC_DS.'config.xml'))
	{
		$skinList[$skin['info']['name']] = $skin['info']['display'];
	}
}

// Ensure skin is compatible with current
// version of Fusion
if ($skin && $GLOBALS['fusion']->skinCompatible())
{
	// Load the default configuration included with the theme
	// if the current theme is unconfigured
	$default_config_file = $GLOBALS['fusion']->skinFusionPath().CC_DS.'settings'.CC_DS.'default.fusion';

	if ($GLOBALS['fusion']->unconfigured() && file_exists($default_config_file))
	{
		$default_file_contents = @file_get_contents($default_config_file);
		$default_data = $GLOBALS['fusion']->decode($default_file_contents);

		if (($default_data) !== false || base64_decode($default_file_contents) === 'b:0;')
		{
			unset($default_data['export_settings']);
			unset($default_data['import_settings']);
			unset($default_data['licensekey']);

			$GLOBALS['fusion']->setSettings($default_data);

			if ($GLOBALS['fusion']->validateAll() == true && $GLOBALS['fusion']->save())
			{
				$GLOBALS['smarty']->assign('LOADED_DEFAULT', true);
			}
		}
	}
	elseif ($_SERVER['REQUEST_METHOD'] == 'POST')
	{
		$data = array_merge_recursive($_POST, $_FILES);
		unset($data['token']); // We don't want to store the security token
		if ($data)
		{
			$GLOBALS['fusion']->setSettings($data);
			$validation = $GLOBALS['fusion']->validateAll();

			if ($validation === true)
			{
				$GLOBALS['fusion']->save();
			}
			else
			{
				$GLOBALS['smarty']->assign('VALIDATION', $validation);
			}
		}
	}

	// Get a collection of the panel names and titles
	$GLOBALS['smarty']->assign('PANELS_NAVIGATION', $GLOBALS['fusion']->getPanels());

	// Pass the full structure to SMARTY
	$GLOBALS['smarty']->assign('PANELS', $structure['panel']);
}
else
{
	// Skin is not compatible with currently
	// installed version of Fusion
	$GLOBALS['smarty']->assign('INCOMPATIBLE', $GLOBALS['fusion']->skinRequires());
}

// Pass the list of skins to SMARTY
$GLOBALS['smarty']->assign('SKINS', $skinList);

// Switch the template directory, preventing the standard plugin display
$GLOBALS['gui']->changeTemplateDir(str_replace('hooks','skin',dirname(__FILE__)));
