<?php
/**
 * Fusion Index Controller Hook
 *
 * Exposes some additional variables
 * to the skins and compiles LESS
 * files using lessphp.
 *
 * @author ShopDev
 * @version 2.4
 *
 * Copyright (c) 2014
 * Licensed under the GPL-3.0 software license agreement
 */

function getLessVars()
{
	$newvars = array();

	if ($GLOBALS['fusion']->get('custom_colors'))
	{
		$vars = $GLOBALS['fusion']->getBeginsWith('lessvar_');

		foreach ($vars as $varName => $varValue)
		{
			if (strlen((string)$varValue) > 0)
				$newvars[str_replace('lessvar_', '', $varName)] = $vars[$varName];
		}

		return $newvars;
	}

	return $newvars;
}

function getLessVarsString()
{
	$lessString = '';
	$lessvars = getLessVars();

	foreach ($lessvars as $varName => $varValue)
	{
		$lessString .= '@'.$varName.':'.$varValue.";\n";
	}

	return $lessString;
}

if ($GLOBALS['config']->has('Fusion', 'status') && $GLOBALS['config']->get('Fusion', 'status'))
{
	// LESS CSS compilation
	if (isset($_GET['compile']))
	{
		// Prevent the cart from displaying
		$GLOBALS['debug']->supress();

		// Set content type
		header('Content-type: text/css');

		// Output the virtual variables file
		if ($_GET['compile'] == 'vars' || $_GET['compile'] == 'variables')
		{
			if ($GLOBALS['fusion']->get('custom_colors')) exit(getLessVarsString());
			exit('/* Custom colors not enabled in Fusion */');
		}

		// Require less css compiler classes
		require_once CC_ROOT_DIR.CC_DS.'modules'.CC_DS.'plugins'.CC_DS.'Fusion'.CC_DS.'classes'.CC_DS.'less.class.php';

		// The requested file to compile
		$file = $_GET['compile'];

		// Ensure file has been specified
		if (!$file) exit('/* File was not specified */');

		// Security checking
		if (!in_array(pathinfo($file, PATHINFO_EXTENSION), array('less', 'css'))) exit('/* File type unrecognised */');

		// Change working directory
		chdir($GLOBALS['fusion']->skinPath());

		// Get path to the requested file
		$path = realpath($file);

		// Ensure file exists
		if (!file_exists($path)) exit('/* File not found */');

		// Compile and output the requested file,
		// checking the cache first
		try {
			// Load from cache
			$cache_fname = md5($path).'.less';
			$cache_obj = $GLOBALS['cache']->read($cache_fname);
			if ($cache_obj == false) $cache_obj = $path;

			// Generate the cache object
			$lessvars = getLessVars();
			$lessvars['skin'] = $GLOBALS['gui']->getSkin();
			$new_cache_obj = less::cexecute($cache_obj, isset($_GET['force']), $lessvars);

			// Cache the compiled file
			if (!is_array($cache_obj) || ($new_cache_obj['updated'] > $cache_obj['updated']))
			{
				$GLOBALS['cache']->write($new_cache_obj, $cache_fname);
			}

			if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE']))
			{
				$if_modified_since = preg_replace('/;.*$/', '', $_SERVER['HTTP_IF_MODIFIED_SINCE']);
			}
			else
			{
				$if_modified_since = '';
			}

			$gmdate_mod = gmdate('D, d M Y H:i:s', $new_cache_obj['updated']) . ' GMT';

			if ($if_modified_since == $gmdate_mod)
			{
				header("HTTP/1.0 304 Not Modified");
				exit;
			}

			header("Last-Modified: $gmdate_mod");
			header('Expires: ' . date('D, d M Y H:i:s', time() + (60*60*24*7)) . ' GMT');

			// Output compiled code
		    exit($new_cache_obj['compiled']);
		} catch (exception $ex) {
		    exit('/*' . $ex->getMessage() . '*/');
		}
	}

	require_once CC_ROOT_DIR.CC_DS.'modules'.CC_DS.'plugins'.CC_DS.'Fusion'.CC_DS.'classes'.CC_DS.'product_listing.class.php';

	// Add support for custom recaptchas
	$GLOBALS['smarty']->assign('captcha_public', $GLOBALS['recaptcha_keys']['captcha_public']);

	// Gravitar
	$GLOBALS['smarty']->assign('profile', array(
		'title' => $GLOBALS['user']->get('title'),
		'first_name' => $GLOBALS['user']->get('first_name'),
		'last_name' => $GLOBALS['user']->get('last_name'),
		'email' => $GLOBALS['user']->get('email'),
		'ip_address' => $GLOBALS['user']->get('ip_address')
	));

	// Cart count and total
	$basket_items = 0;
	$contents = $GLOBALS['cart']->basket['contents'];

	if (is_array($contents))
	{
		foreach ($contents as $hash => $product)
		{
			$basket_items += $product['quantity'];
		}
	}

	$GLOBALS['smarty']->assign('CART_ITEMS', $basket_items);
	$GLOBALS['smarty']->assign('CART_TOTAL', $GLOBALS['cart']->getTotal());

	// Globals
	$GLOBALS['smarty']->assign('GLOBALS', $GLOBALS);
}
