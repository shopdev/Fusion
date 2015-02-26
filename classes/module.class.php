<?php
/**
 * Fusion Module Interface
 *
 * Enforces essential method declarations
 * for Fusion modules.
 *
 * @author ShopDev
 * @version 2.4
 *
 * Copyright (c) 2014
 * Licensed under the GPL-3.0 software license agreement
 */
interface Module
{
	/**
	 * Module constructors are passed their value (as stored
	 * in the database) and their config file structure for
	 * the worked setting.
	 */
	public function __construct($value, $setting);

	/**
	 * Returns HTML for rendering settings that use the module
	 * in the control panel.  Ensure that you return a valid
	 * HTML5 string.
	 */
	public function paintConfiguration();

	/**
	 * Validates the setting value.  Should return true if
	 * the submitted data is valid or a string (error message)
	 * otherwise.
	 */
	public function validate();

	/**
	 * Returns a value to be passed to be passed to the skin.
	 * The skin then takes some action depending on the value
	 * returned.
	 */
	public function yield();
}
