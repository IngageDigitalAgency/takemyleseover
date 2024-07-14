<?php
/*
 * This is an example of configuring the WURFL PHP API
 */
class wurfl extends Common {

	private $m_factory = null;
	private $m_manager = null;
	private $m_device = null;

	public function __construct() {
		return;
		parent::__construct ();
		$wurflDir = dirname(__FILE__) . '/WURFL';
		$resourcesDir = sprintf('%sfiles/resources',defined('FRONTEND') ? './':'../');
		require_once $wurflDir.'/Application.php';
		$persistenceDir = $resourcesDir.'/storage/persistence';
		$cacheDir = $resourcesDir.'/storage/cache';
		// Create WURFL Configuration
		$wurflConfig = new WURFL_Configuration_InMemoryConfig();
		// Set location of the WURFL File
		$wurflConfig->wurflFile($resourcesDir.'/wurfl.zip');
		// Set the match mode for the API ('performance' or 'accuracy')
		$wurflConfig->matchMode('performance');
		// Automatically reload the WURFL data if it changes
		$wurflConfig->allowReload(true);
		// Setup WURFL Persistence
		$wurflConfig->persistence('file', array('dir' => $persistenceDir));
		// Setup Caching
		$wurflConfig->cache('file', array('dir' => $cacheDir, 'expiration' => 36000));
		// Create a WURFL Manager Factory from the WURFL Configuration
		$wurflManagerFactory = new WURFL_WURFLManagerFactory($wurflConfig);
		// Create a WURFL Manager
		/* @var $wurflManager WURFL_WURFLManager */
		$wurflManager = $wurflManagerFactory->create();
		$this->m_manager = $wurflManager;
		$this->m_device = $this->m_manager->getDeviceForHttpRequest($_SERVER);
	}
	
	function getWurfl() {
		return $this->m_manager;
	}

	function getCapability($type) {
		return $this->m_device->getCapability($type);
	}

	function getAllCapabilities() {
		return $this->m_device->getAllCapabilities();
	}

}