<?php

/**
 * A wrapper for a documentation entity which is created when registering the
 * path with {@link DocumentationService::register()}. This refers to a whole package
 * rather than a specific page but if we need page options we may need to introduce 
 * a class for that.
 *
 * @package sapphiredocs
 */

class DocumentationEntity extends ViewableData {
	
	static $casting = array(
		'Name' => 'Text'
	);
	
	/**
	 * @var String $module folder name
	 */
	private $moduleFolder;
	
	/**
	 * @var String $title nice title
	 */
	private $title;

	/**
	 * @var Array $version version numbers and the paths to each
	 */
	private $versions = array();
	
	/**
	 * @var Array $langs a list of available langauges
	 */
	private $langs = array();
	
	/**
	 * Constructor. You do not need to pass the langs to this as
	 * it will work out the languages from the filesystem
	 *
	 * @param String $module name of module
	 * @param String $version version of this module
	 * @param String $path path to this module
	 */
	function __construct($module, $version = '', $path, $title = false) {
		$this->addVersion($version, $path);
		$this->title = (!$title) ? $this->module : $title;
		$this->moduleFolder = $module;
	}
	
	/**
	 * Return the languages which are available
	 *
	 * @return Array
	 */
	public function getLanguages() {
		return $this->langs;
	}
	
	/**
	 * Return whether this entity has a given language
	 *
	 * @return bool
	 */
	public function hasLanguage($lang) {
		return (in_array($lang, $this->langs));
	}
	
	/**
	 * Add a langauge or languages to the entity
	 *
	 * @param Array|String languages
	 */
	public function addLanguage($language) {
		if(is_array($language)) {
			$this->langs = array_unique(array_merge($this->langs, $language));
		}
		else {
			$this->langs[] = $language;
		}
	}
	
	/**
	 * Get the folder name of this module
	 *
	 * @return String
	 */
	public function getModuleFolder() {
		return $this->moduleFolder;
	}
	
	/**
	 * Get the title of this module
	 *
	 * @return String
	 */
	public function getTitle() {
		return $this->title;
	}
	
	/**
	 * Return the versions which are available
	 *
	 * @return Array
	 */
	public function getVersions() {
		return array_keys($this->versions);
	}
	
	/**
	 * Return whether we have a given version of this entity
	 *
	 * @return bool
	 */
	public function hasVersion($version) {
		return (isset($this->versions[$version]));
	}
	
	/**
	 * Return whether we have any versions at all0
	 *
	 * @return bool
	 */
	public function hasVersions() {
		return (sizeof($this->versions) > 0);
	}
	
	/**
	 * Add another version to this entity
	 *
	 * @param Float $version Version number
	 * @param String $path path to folder
	 */
	public function addVersion($version = '', $path) {
		// determine the langs in this path
		
		$langs = scandir($path);
		
		$available = array();
		
		if($langs) {
			foreach($langs as $key => $lang) {
				if(!is_dir($path . $lang) || strlen($lang) > 2 || in_array($lang, DocumentationService::get_ignored_files(), true)) 
					$lang = 'en';
				
				if(!in_array($lang, $available))
					$available[] = $lang;
			}
		}
		
		$this->addLanguage($available);
		$this->versions[$version] = $path;
	}
	
	/**
	 * Remove a version from this entity
	 *
	 * @param Float $version
	 */
	public function removeVersion($version = '') {
		if(isset($this->versions[$version])) {
			unset($this->versions[$version]);
		}
	}
	
	/**
	 * Return the path to this documentation entity
	 *
	 * @return String
	 */
	public function getPath($version = false, $lang = false) {
		
		if(!$version) $version = '';
		if(!$lang) $lang = 'en';
		
		if(!$this->hasVersion($version)) $path = array_pop($this->versions);
		else $path = $this->versions[$version];

		return $path . $lang .'/';
	}
}