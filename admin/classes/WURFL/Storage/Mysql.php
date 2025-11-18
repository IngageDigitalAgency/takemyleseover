<?php
/**
 * Copyright (c) 2014 ScientiaMobile, Inc.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * Refer to the COPYING.txt file distributed with this package.
 *
 * @category   WURFL
 * @package	WURFL_Storage
 * @copyright  ScientiaMobile, Inc.
 * @license	GNU Affero General Public License
 * @author	 Fantayeneh Asres Gizaw
 * @version	$id$
 */
/**
 * WURFL Storage using PDO (PHP 8+ compatible)
 * @package	WURFL_Storage
 */
class WURFL_Storage_Mysql extends WURFL_Storage_Base {

	private $defaultParams = array(
		"host" => "localhost",
		"port" => 3306,
		"db" => "wurfl_persistence_db",	
		"user" => "",
		"pass" => "",
		"table" => "wurfl_object_cache",
		"keycolumn" => "key",
		"valuecolumn" => "value"
	);

	private $pdo;
	private $host;
	private $db;
	private $user;
	private $pass;
	private $port;
	private $table;
	private $keycolumn;
	private $valuecolumn;

	public function __construct($params) {
		$currentParams = is_array($params) ? array_merge($this->defaultParams,$params) : $this->defaultParams;
		foreach($currentParams as $key => $value) {
			$this->$key = $value;
		}
		$this->initialize();
	}

	private function initialize() {
		$this->_ensureModuleExistance();

		/* Initializes PDO connection to MySQL */
		try {
			$dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db}";
			$this->pdo = new PDO($dsn, $this->user, $this->pass, array(
				PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
				PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
				PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
			));
		} catch (PDOException $e) {
			throw new WURFL_Storage_Exception("Couldn't connect to {$this->host}:{$this->port} - " . $e->getMessage());
		}

		/* Is Table there? */
		try {
			$stmt = $this->pdo->prepare("SHOW TABLES FROM `{$this->db}` LIKE :table");
			$stmt->bindParam(':table', $this->table);
			$stmt->execute();
			$result = $stmt->fetch();
		} catch (PDOException $e) {
			throw new WURFL_Storage_Exception("Couldn't show tables from database {$this->db} - " . $e->getMessage());
		}

		// create table if it's not there.
		if (!$result) {
			$sql = "CREATE TABLE `{$this->db}`.`{$this->table}` (
					  `{$this->keycolumn}` varchar(255) collate latin1_general_ci NOT NULL,
					  `{$this->valuecolumn}` mediumblob NOT NULL,
					  `ts` timestamp NOT NULL default CURRENT_TIMESTAMP,
					  PRIMARY KEY  (`{$this->keycolumn}`)
					) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_general_ci";
			try {
				$this->pdo->exec($sql);
			} catch (PDOException $e) {
				throw new WURFL_Storage_Exception("Table {$this->table} missing in {$this->db} - " . $e->getMessage());
			}
		}
	}
	
	public function save($objectId, $object, $expiration=null) {
		$serializedObject = serialize($object);
		$encodedId = $this->encode("", $objectId);
		
		try {
			// Delete existing entry
			$sql = "DELETE FROM `{$this->db}`.`{$this->table}` WHERE `{$this->keycolumn}` = :key";
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindParam(':key', $encodedId);
			$stmt->execute();

			// Insert new entry
			$sql = "INSERT INTO `{$this->db}`.`{$this->table}` (`{$this->keycolumn}`, `{$this->valuecolumn}`) VALUES (:key, :value)";
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindParam(':key', $encodedId);
			$stmt->bindParam(':value', $serializedObject);
			$success = $stmt->execute();
			
			return $success;
		} catch (PDOException $e) {
			throw new WURFL_Storage_Exception("MySQL error setting $objectId in {$this->db} - " . $e->getMessage());
		}
	}

	public function load($objectId) {
		$return = null;
		$encodedId = $this->encode("", $objectId);

		try {
			$sql = "SELECT `{$this->valuecolumn}` FROM `{$this->db}`.`{$this->table}` WHERE `{$this->keycolumn}` = :key";
			$stmt = $this->pdo->prepare($sql);
			$stmt->bindParam(':key', $encodedId);
			$stmt->execute();
			
			$row = $stmt->fetch();
			if ($row && isset($row['value'])) {
				$return = @unserialize($row['value']);
				if ($return === false) {
					$return = null;
				}
			}
		} catch (PDOException $e) {
			throw new WURFL_Storage_Exception("MySQL error loading $objectId from {$this->db} - " . $e->getMessage());
		}
		
		return $return;
	}

	public function clear() {
		try {
			$sql = "TRUNCATE TABLE `{$this->db}`.`{$this->table}`";
			$success = $this->pdo->exec($sql);
			return $success !== false;
		} catch (PDOException $e) {
			throw new WURFL_Storage_Exception("MySQL error clearing {$this->db}.{$this->table} - " . $e->getMessage());
		}
	}

	/**
	 * Ensures the existence of the PHP Extension PDO and PDO_MYSQL
	 * @throws WURFL_Storage_Exception required extension is unavailable
	 */
	private function _ensureModuleExistance() {
		if (!extension_loaded("pdo")) {
			throw new WURFL_Storage_Exception("The PHP extension PDO must be installed and loaded in order to use the MySQL storage.");
		}
		if (!extension_loaded("pdo_mysql")) {
			throw new WURFL_Storage_Exception("The PHP extension PDO_MYSQL must be installed and loaded in order to use the MySQL storage.");
		}
	}

}