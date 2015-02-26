<?php
/**
 * Fusion Front-End Display Hook
 *
 * Exposes Fusion to the skins.
 *
 * @author ShopDev
 * @version 2.4
 *
 * Copyright (c) 2014
 * Licensed under the GPL-3.0 software license agreement
 */
if ($GLOBALS['config']->has('Fusion', 'status') && $GLOBALS['config']->get('Fusion', 'status'))
{
	require CC_ROOT_DIR.CC_DS.'modules'.CC_DS.'plugins'.CC_DS.'Fusion'.CC_DS.'classes'.CC_DS.'xmlwrapper.class.php';
	require CC_ROOT_DIR.CC_DS.'modules'.CC_DS.'plugins'.CC_DS.'Fusion'.CC_DS.'classes'.CC_DS.'configuration.class.php';
	require CC_ROOT_DIR.CC_DS.'modules'.CC_DS.'plugins'.CC_DS.'Fusion'.CC_DS.'classes'.CC_DS.'fusion.class.php';
	require CC_ROOT_DIR.CC_DS.'modules'.CC_DS.'plugins'.CC_DS.'Fusion'.CC_DS.'classes'.CC_DS.'module.class.php';

	// Determine the current skin
	$skin = $this->getSkin();

	// Create a fusion instance
	$GLOBALS['fusion'] = new Fusion($skin);

	// Pass the fusion instance to SMARTY
	$GLOBALS['smarty']->assign('fusion', $GLOBALS['fusion']);
}
