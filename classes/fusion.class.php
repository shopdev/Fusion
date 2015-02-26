<?php
/**
 * Fusion Framework Class
 *
 * Backbone of the Fusion Framework.
 *
 * @author ShopDev
 * @version 2.4
 *
 * Copyright (c) 2014
 * Licensed under the GPL-3.0 software license agreement
 */
class Fusion extends Configuration
{
	const FUSION_TABLE = 'CubeCart_fusion'; // Plugin database table name
	const FUSION_PERM = 'Fusion'; // Permissions name for plugin
	const FUSION_VERSION = '2.4'; // Plugin version

	protected $skin; // Name of the skin being configured
	protected $structure; // Full config file structure for the skin
	protected $configSettings;
	protected $data; // Full data retrieved from the database or otherwise set

	public function __construct($skin)
	{
		// Autoload modules as required
		spl_autoload_register(array($this, 'autoload_modules'));

		parent::__construct(CC_ROOT_DIR.CC_DS.'skins'.CC_DS.$skin.CC_DS.'Fusion'.CC_DS.'config.xml');
		$this->_install(); // Install Fusion's database tables if not already present
		$this->skin = $skin;
		$xml = $this->getXML();

		if ($xml instanceof XMLWrapper)
		{
			$this->structure = $xml->toArray(false);
			$this->data = $this->getSettings(); // Fetch the data for all settings from the database
			$this->configSettings = $this->getConfigSettings();
		}
	}

	/**
	 * Checks whether the data structure for Fusion
	 * exists in the database and creates the tables
	 * otherwise.
	 */
	private function _install()
	{
		if (!$GLOBALS['db']->getFields(self::FUSION_TABLE))
		{
			// Create table
			$sql = 'CREATE TABLE IF NOT EXISTS `'.self::FUSION_TABLE.'` (
						`skin`		VARCHAR(100) NOT NULL,
						`data`		text NOT NULL,
						UNIQUE KEY `skin` (`skin`)
					) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci; #EOQ';

			if ($GLOBALS['db']->parseSchema($sql) == false)
			{
				exit('Unable to create Fusion database table.');
			}
		}
	}

	/**
	 * Returns path to the plugin directory
	 */
	public function pluginPath()
	{
		return str_replace('\\', '/', CC_ROOT_REL.'modules'.CC_DS.'plugins'.CC_DS.'Fusion');
	}

	/**
	 * Returns path to the public directory
	 */
	public function publicPath()
	{
		return $this->pluginPath().'/public';
	}

	/**
	 * Returns path to the current skin
	 */
	public function skinPath()
	{
		return CC_ROOT_DIR.CC_DS.'skins'.CC_DS.$this->skin;
	}

	/**
	 * Returns path to the Fusion directory of
	 * the current skin
	 */
	public function skinFusionPath()
	{
		return $this->skinPath().CC_DS.'Fusion';
	}

	/**
	 * Returns the version number of Fusion
	 * currently installed.
	 */
	public function getVersion()
	{
		return self::FUSION_VERSION;
	}

	/**
	 * Returns the version of Fusion required
	 * by the current skin.
	 */
	public function skinRequires()
	{
		// Get the minimum required version of
		// Fusion, specified by the skin
		$configPath = $this->skinPath().CC_DS.'config.xml';

		if (file_exists($configPath))
		{
			$config = new Configuration($configPath);
			$requires = $config->getXML()->xpath('/skin/info/fusion')->toArray();

			if (isset($requires[0])) return $requires[0];
		}

		return false;
	}

	/**
	 * Returns true if the skin is compatible
	 * with the currently installed version of
	 * Fusion; false otherwise.
	 */
	public function skinCompatible()
	{
		$required = $this->skinRequires();

		if ($required && version_compare(self::FUSION_VERSION, $required, '>=')) return true;
		return false;
	}

	/**
	 * Returns the name of the skin being configured.
	 */
	public function getSkin()
	{
		return $this->skin;
	}

	/**
	 * Returns an array of data retrieved from the database or otherwise set.
	 */
	public function getData()
	{
		return $this->data;
	}

	/**
	 * Returns the full fusion config file structure.
	 */
	public function getStructure()
	{
		return $this->structure;
	}

	/**
	 * Returns a list of all the panels defined in the config file.
	 */
	public function getPanels()
	{
		$collection = array();
		$panels = $this->getXML()->panel;

		foreach ($panels as $panel)
		{
			$collection[] = $panel->getAttributes();
		}

		return $collection;
	}

	/**
	 * Returns a collection of all the settings defined in the config file.
	 */
	public function getConfigSettings()
	{
		$settings = array();
		$results = $this->getXML()->xpath('//setting')->toArray();

		foreach ($results as $result)
		{
			array_push($settings, $result['setting']);
		}

		return $settings;
	}

	/**
	 * Returns the requested setting as defined in the config file.
	 */
	public function getConfigSetting($settingName)
	{
		foreach ($this->configSettings as $setting)
		{
			if (isset($setting['attributes']['name']) && $setting['attributes']['name'] == $settingName)
			{
				return $setting;
			}
		}
	}

	/**
	 * Returns the module name used by the specified setting name.
	 */
	public function getModule($settingName)
	{
		foreach ($this->configSettings as $setting)
		{
			// If the setting matches the required name
			if ($setting['attributes']['name'] == $settingName)
			{
				// Return the setting's module name
				return $setting['attributes']['module'];
			}
		}

		return false;
	}

	/**
	 * Returns the configuration HTML for the passed module.
	 */
	public function module(Array $setting)
	{
		// Get the module to be executed
		$moduleName = trim($setting['attributes']['module']);

		// Get the name of the setting
		$settingName = trim($setting['attributes']['name']);

		$moduleData = $this->getSetting($settingName);
		$module = new $moduleName($moduleData, $setting);
		$moduleOutput = $module->paintConfiguration();

		if (is_string($moduleOutput))
		{
			return $moduleOutput;
		}

		return false;
	}

	/**
	 * Validates all the submitted fields and returns an array of validation
	 * errors or true if all is valid.
	 */
	public function validateAll()
	{
		$errorMessages = array();

		// For each field
		foreach ($this->data as $setting => $value)
		{
			$moduleName = $this->getModule($setting);

			if ($moduleName !== false)
			{
				$moduleName = strtolower(trim($moduleName));

				$module = new $moduleName($value, $this->getConfigSetting($setting));
				$validation = $module->validate();

				if ($validation !== true)
				{
					$errorMessages[$setting] .= $validation;
				}
			}
		}

		if (!empty($errorMessages)) return $errorMessages;

		return true;
	}

	/**
	 * Returns the encoded representation of an array for safe storage.
	 */
	public static function encode($data)
	{
		return base64_encode(serialize($data));
	}

	/**
	 * Decodes the encoded data stored in the database.
	 */
	public static function decode($data)
	{
		return @unserialize(base64_decode($data));
	}

	/**
	 * Saves the data for all modules corresponding
	 * to the current skin to the database.
	 */
	public function save()
	{
		if (Admin::getInstance()->permissions(self::FUSION_PERM, CC_PERM_EDIT))
		{
			$data = array(
				'skin' => $this->skin,
				'data' => self::encode($this->data)
			);

			// Check whether an entry already exists for this skin
			if ($this->getSettings() === false)
			{
				// Create a new entry
				return $GLOBALS['db']->insert(self::FUSION_TABLE, $data);
			}
			else
			{
				// Update the existing entry
				return $GLOBALS['db']->update(self::FUSION_TABLE, $data, array('skin' => $this->skin));
			}
		}

		return false;
	}

	/**
	 * Returns data for the specified setting
	 * from the database.
	 */
	public function getSetting($setting)
	{
		return self::strip_slashes($this->data[$setting]);
	}

	/**
	 * Returns the data stored in the database for all modules corresponding
	 * to the current skin.
	 */
	protected function getSettings()
	{
		$where = array('skin' => $this->skin);
		$data = $GLOBALS['db']->select(self::FUSION_TABLE, array('data'), $where, false, 1);

		if ($data !== false)
		{
			return self::decode($data[0]['data']);
		}

		return false;
	}

	/**
	 * Sets/updates data for the specified module.
	 * Does not save the data.  Use Fusion::save()
	 * to save the data after making any updates.
	 */
	public function setSetting($module, $data)
	{
		return $this->data[$module] = $data;
	}

	/**
	 * Sets the entire data structure for
	 * all modules.  Does not save the data.
	 * Use Fusion::save() to save the data
	 * to the database.
	 */
	public function setSettings($data)
	{
		// Allow modules to make changes to
		// values submitted before setting
		// the data structure.
		foreach ($data as $setting => $value)
		{
			$moduleName = $this->getModule($setting);

			if ($moduleName !== false)
			{
				$moduleName = strtolower(trim($moduleName));

				$module = new $moduleName($value, $this->getConfigSetting($setting));

				if (method_exists($module, 'setSettings')) {
					$data[$setting] = $module->setSettings();
				}
			}
		}

		// Set empty values for missing form data
		foreach ($this->configSettings as $setting)
		{
			$settingName = $setting['attributes']['name'];

			if (!isset($data[$settingName]))
			{
				$data[$settingName] = '';
			}
		}

		// Set the data structure
		$this->data = $data;
	}

	/**
	 * Returns the value to be passed to
	 * the skin for the specified setting.
	 */
	public function get($settingName)
	{
		$moduleName = $this->getModule($settingName);

		if ($moduleName !== false)
		{
			$moduleName = strtolower(trim($moduleName));
			$moduleData = $this->getSetting($settingName);
			$module = new $moduleName($moduleData, $this->getConfigSetting($settingName));
			return $module->yield();
		}
	}

	/**
	 * Returns value to be passed to the
	 * skin in JSON form.  Useful for
	 * use in JavaScript.
	 */
	public function getJSON($settingNames)
	{
		if (is_array($settingNames))
		{
			$settings = array();

			foreach ($settingNames as $settingName)
			{
				$settings[$settingName] = $this->get($settingName);
			}

			return json_encode($settings);
		}

		return json_encode($this->get($settingNames));
	}

	/**
	 * Returns array of values to be passed
	 * to the skin for setting names
	 * beginning with the specified string.
	 */
	public function getBeginsWith($settingNameBeginsWith)
	{
		$settings = array();
		$searchLength = strlen($settingNameBeginsWith);

		foreach ($this->data as $settingName => $settingValue)
		{
		    if (substr($settingName, 0, $searchLength) == $settingNameBeginsWith) {
		        $settings[$settingName] = $this->get($settingName);
		    }
		}

		return $settings;
	}

	/**
	 * Returns true if there is no configuration
	 * saved for the current theme.
	 */
	public function unconfigured()
	{
		$data = $this->data;
		unset($data['licensekey']);

		if (empty($data)) return true;
		return false;
	}

	/**
	 * Removes slashes added by CubeCart's
	 * sanitisation.
	 */
	public static function strip_slashes($data)
	{
		if (is_array($data))
		{
			foreach ($data as $key => $value)
			{
				$data[$key] = self::strip_slashes($value);
			}
		}
		elseif (is_string($data))
		{
			return $GLOBALS['db']->strip_slashes($data);
		}

		return $data;
	}

	/**
	 * Loads modules as required.
	 */
	public function autoload_modules($class)
	{
		// Path to included module
		$includedPath = CC_ROOT_DIR.CC_DS.'modules'.CC_DS.'plugins'.CC_DS.'Fusion'.CC_DS.'modules'.CC_DS.$class.CC_DS.$class.'.class.php';

		// Path to third party module
		$thirdPartyPath = CC_ROOT_DIR.CC_DS.'skins'.CC_DS.$this->skin.CC_DS.'Fusion'.CC_DS.'modules'.CC_DS.$class.CC_DS.$class.'.class.php';

		// Check the module exists
		if (file_exists($thirdPartyPath))
		{
			require_once $thirdPartyPath;
		}
		else if (file_exists($includedPath))
		{
			// A third party module does not
			// exist with this name, so let's
			// try to load from the included
			// modules
			require_once $includedPath;
		}
		else
		{
			return false;
		}
	}
}
