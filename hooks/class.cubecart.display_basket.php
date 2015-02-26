<?php
/**
 * Fusion Related Products Hook
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
	// ========================================
	// = Standardize related product listings =
	// ========================================
	if (isset($related_list) && is_array($related_list))
	{
		for ($i=0; $i<count($related_list); $i++)
		{
			$product_listing = new Product_Listing($related_list[$i]);
			$product_listing->standardize();
			$related_list[$i] = $product_listing->getProduct();
		}

		$GLOBALS['smarty']->assign('RELATED', $related_list);
	}
}
