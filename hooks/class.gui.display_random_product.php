<?php
/**
 * Fusion Random Product Hook
 *
 * Exposes some additional variables
 * to the skins.
 *
 * @author ShopDev
 * @version 2.4
 *
 * Copyright (c) 2014
 * Licensed under the GPL-3.0 software license agreement
 */
if ($GLOBALS['config']->has('Fusion', 'status') && $GLOBALS['config']->get('Fusion', 'status'))
{
	// ===============================================
	// = Standardize random/featured product listing =
	// ===============================================
	$product_listing = new Product_Listing($product);
	$product_listing->standardize();
	$product = $product_listing->getProduct();
}
