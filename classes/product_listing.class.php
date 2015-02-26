<?php
/**
 * Fusion Product Listings Class
 *
 * Used for standardising product
 * information accessible by the
 * skin for listings throughout.
 *
 * @author ShopDev
 * @version 2.4
 *
 * Copyright (c) 2014
 * Licensed under the GPL-3.0 software license agreement
 */
class Product_Listing
{
	protected $product;

	public function __construct($product)
	{
		$this->product = $product;
	}

	public function getProduct()
	{
		return $this->product;
	}

	public function standardize()
	{
		$query = $GLOBALS['db']->select('CubeCart_inventory', false,  array('product_id' => $this->product['product_id']));
		$this->product = array_merge($query[0], $this->product);
		$this->setImages();
		$this->setShortDescription();
		$this->setReviewScore();
		$this->setPrices();
		$this->setPurchase();
		$GLOBALS['language']->translateProduct($this->product);
	}

	public function setImages()
	{
		$skins = $GLOBALS['gui']->getSkinData();

		if (isset($skins['images']))
		{
			$image_types = $skins['images'];

			if (!isset($image_types['source']))
			{
				$image_types['source'] = array();
			}
			foreach ($image_types as $image_key => $values)
			{
				if (!isset($this->product[$image_key]))
				{
					$this->product[$image_key] = $GLOBALS['gui']->getProductImage($this->product['product_id'], $image_key);
				}
			}
		}
	}

	public function setShortDescription()
	{
		if (!isset($this->product['description_short']))
		{
			$this->product['description_short'] = (strlen($this->product['description']) > $GLOBALS['config']->get('config', 'product_precis')) ? substr(strip_tags($this->product['description']), 0, $GLOBALS['config']->get('config', 'product_precis')).'&hellip;' : $this->product['description'];
		}
	}

	public function setReviewScore()
	{
		if (!isset($this->product['review_score']) && $GLOBALS['config']->get('config','enable_reviews') && ($reviews = $GLOBALS['db']->select('CubeCart_reviews', array('rating'), array('product_id' => (int)$this->product['product_id'], 'approved' => '1'))) !== false)
		{
			$score	= 0;
			$count	= 0;

			foreach ($reviews as $review)
			{
				$score += $review['rating'];
				$count++;
			}

			$this->product['review_score'] = round($score/$count, 1);
		}
		else
		{
			$this->product['review_score'] = false;
		}
	}

	public function setPrices()
	{
		$rawPrice = preg_replace('/[^.0-9]/', '', $this->product['price']);
		$rawSalePrice = preg_replace('/[^.0-9]/', '', $this->product['sale_price']);

		if (!isset($this->product['ctrl_sale']))
		{
			$this->product['ctrl_sale'] = (!$GLOBALS['tax']->salePrice($rawPrice, $rawSalePrice) || !$GLOBALS['config']->get('config', 'catalogue_sale_mode')) ? false : true;
		}

		if (!isset($this->product['price_unformatted']))
		{
			$this->product['price_unformatted'] = $rawPrice;
		}

		if (!isset($this->product['sale_price_unformatted']))
		{
			$this->product['sale_price_unformatted'] = $rawSalePrice;
		}

		if (isset($this->product['price']) && ($rawPrice == $this->product['price']))
		{
			$this->product['price'] = $GLOBALS['tax']->priceFormat($rawPrice);
		}

		if (isset($this->product['sale_price']) && ($rawSalePrice == $this->product['sale_price']))
		{
			$this->product['sale_price'] = $GLOBALS['tax']->priceFormat($rawSalePrice);
		}
	}

	public function setPurchase()
	{
		if (!isset($this->product['ctrl_purchase']) && !isset($this->product['ctrl_stock']))
		{
			$this->product['ctrl_purchase'] = true;

			if ($this->product['use_stock_level']) {
				// Get Stock Level
				$stock_level = $GLOBALS['catalogue']->getProductStock($this->product['product_id']);
				if ((int)$stock_level <= 0) {
					// Out of Stock
					if (!$GLOBALS['config']->get('config', 'basket_out_of_stock_purchase')) {
						// Not Allowed
						$this->product['ctrl_purchase'] = false;
					}
				}
			}
		}
	}
}
