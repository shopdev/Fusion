<?php
/**
 * Fusion Custom LESS CSS parser
 *
 * Extends the LESSPHP compiler.
 *
 * @author ShopDev
 * @version 2.4
 *
 * Copyright (c) 2014
 * Licensed under the GPL-3.0 software license agreement
 */

require_once CC_ROOT_DIR.CC_DS.'modules'.CC_DS.'plugins'.CC_DS.'Fusion'.CC_DS.'classes'.CC_DS.'lessc.class.php';

/**
 * Extends lessphp, adding the following features:
 *
 * - Allow @import to take url to file
 * - Rebuild cache object when variables change
 */
class less extends lessc {

	// attempts to find the path of an import url, returns null for css files
	function findImport($url) {
		foreach ((array)$this->importDir as $dir) {
			$full = $dir.(substr($dir, -1) != '/' ? '/' : '').$url;
			if ($this->fileExists($file = $full.'.less') || $this->fileExists($file = $full)) {
				return $file;
			}

			// Check whether the file is a CSS file
			$ch = curl_init(CC_STORE_URL.'/'.$url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_exec($ch);
			if (strpos(curl_getinfo($ch, CURLINFO_CONTENT_TYPE), 'text/css') !== false) return CC_STORE_URL.'/'.$url;
			curl_close($ch);
		}

		return null;
	}

	// inject array of unparsed strings into environment as variables
	protected function injectVariables($args) {
		$this->pushEnv();
		$parser = new lessc_parser($this, __METHOD__);
		foreach ($args as $name => $str_value) {
			if (strlen((string)$str_value) > 0) {
				if ($name{0} != '@') $name = '@'.$name;
				$parser->count = 0;
				$parser->buffer = (string)$str_value;
				if (!$parser->propertyValue($value)) {
					throw new Exception("failed to parse passed in variable $name: $str_value");
				}

				$this->set($name, $value);
			}
		}
	}

	protected function addParsedFile($file) {
		if (file_exists($file)) {
			$this->allParsedFiles[realpath($file)] = @filemtime($file);
		} else {
			$this->allParsedFiles[realpath($file)] = 0;
		}
	}

	/**
	 * Execute lessphp on a .less file or a lessphp cache structure
	 *
	 * The lessphp cache structure contains information about a specific
	 * less file having been parsed. It can be used as a hint for future
	 * calls to determine whether or not a rebuild is required.
	 *
	 * The cache structure contains two important keys that may be used
	 * externally:
	 *
	 * compiled: The final compiled CSS
	 * updated: The time (in seconds) the CSS was last compiled
	 *
	 * The cache structure is a plain-ol' PHP associative array and can
	 * be serialized and unserialized without a hitch.
	 *
	 * @param mixed $in Input
	 * @param bool $force Force rebuild?
	 * @param array $vars Variables to pass in
	 * @return array lessphp cache structure
	 */
	public static function cexecute($in, $force = false, $vars = array()) {

		// assume no root
		$root = null;

		if (is_string($in)) {
			$root = $in;
		} elseif (is_array($in) and isset($in['root'])) {
			if ($force or !isset($in['files'])) {
				// If we are forcing a recompile or if for some reason the
				// structure does not contain any file information we should
				// specify the root to trigger a rebuild.
				$root = $in['root'];
				var_dump('one');
			} elseif (isset($in['vars']) and $vars !== $in['vars']) {
				// If the variables we're passing in have changed
				// we should look at the incoming root to trigger a rebuild.
				$root = $in['root'];
				var_dump('two');
			} elseif (isset($in['files']) and is_array($in['files'])) {
				foreach ($in['files'] as $fname => $ftime ) {
					if (file_exists($fname) and (strpos('.php', $fname < 0) or filemtime($fname) > $ftime)) {
						// One of the files we knew about previously has changed
						// so we should look at our incoming root again.
						$root = $in['root'];
						break;
					}
				}
			}
		} else {
			// TODO: Throw an exception? We got neither a string nor something
			// that looks like a compatible lessphp cache structure.
			return null;
		}

		if ($root !== null) {
			// We have a root value which means we should rebuild.
			$less = new less($root);
			$out = array();
			$out['root'] = $root;
			$out['compiled'] = $less->parse(null, $vars);
			$out['files'] = $less->allParsedFiles();
			$out['updated'] = time();
			$out['vars'] = $vars;
			return $out;
		} else {
			// No changes, pass back the structure
			// we were given initially.
			return $in;
		}

	}
}
