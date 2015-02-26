<?php
/**
 * Fusion XML Wrapper Class
 *
 * Simplifies traversal of complicated
 * SimpleXML objects.
 *
 * @author ShopDev
 * @version 2.4
 *
 * Copyright (c) 2014
 * Licensed under the GPL-3.0 software license agreement
 */
class XMLWrapper
{
    private $xml;

    public function __construct($xml)
	{
        $this->xml = $xml;
    }

    public function __get($name)
	{
		return $this->getTag($name);
    }

	public function __call($methodName, $args)
	{
		if (method_exists($this->xml, $methodName))
		{
			return new XMLWrapper(call_user_func_array(array($this->xml, $methodName), $args));
		}
	}

	/**
	 * Returns child tags by name.
	 */
	public function getTag($tagName)
	{
		$collection = array();
		$tags = $this->xml->$tagName;

		foreach ($tags as $tag)
		{
			$collection[] = new XMLWrapper($tag);
		}

		return $collection;
	}

	/**
	 * Returns all the attributes as an associative array.
	 */
	public function getAttributes()
	{
		$arr = array();
		$attributes = $this->xml->attributes();

		foreach ($attributes as $name => $value)
		{
			$arr[$name] = self::parseAttributeValue($value);
		}

		return $arr;
	}

	/**
	 * Returns attribute value by attribute name.
	 */
	public function getAttribute($attrName)
	{
		return self::parseAttributeValue($this->xml->attributes()->$attrName);
	}

	/**
	 * Converts SimpleXML object into an array.
	 */
	public function toArray($root = true)
	{
		$xml = $this->xml;

		$array = array();

		if (is_array($xml))
		{
			foreach ($xml as $node)
			{
				$node = new XMLWrapper($node);
				$array[] = $node->toArray(true);
			}

			return $array;
		}

		if (!$xml->children())
		{
			return (string)$xml;
		}

		foreach ($xml->children() as $element => $node)
		{
			$node = new XMLWrapper($node);

			$totalElement = count($xml->{$element});

			if (!isset($array[$element]))
			{
				$array[$element] = "";
			}

			// Has attributes
			if ($attributes = $node->getAttributes())
			{
				$data = array(
					'attributes' => $attributes,
				);

	 			if (!count($node->children()))
				{
					$data['value'] = (string)$node;
				}
				else
				{
					$data = array_merge($data, $node->toArray(false));
				}

				$array[$element][] = $data;
			}
			else // Just a value
			{
				$array[$element][] = $node->toArray(false);
			}
		}

		if ($root)
		{
			return array($xml->getName() =>
				array_merge(
					array('attributes' => $this->getAttributes()),
					$array
				)
			);
		}

		return $array;
	}

	/**
	 * Parse attribute values by recognising basic data types.
	 */
	public static function parseAttributeValue($value)
	{
		$value = (string) $value;

		if (preg_match('/^\d+$/', $value))
		{
			return (integer) $value;
		}

		return $value;
	}
}
