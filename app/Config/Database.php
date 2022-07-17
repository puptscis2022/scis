<?php

namespace Config;

use CodeIgniter\Database\Config;

/**
 * Database Configuration
 */
class Database extends Config
{
	/**
	 * The directory that holds the Migrations
	 * and Seeds directories.
	 *
	 * @var string
	 */
	public $filesPath = APPPATH . 'Database' . DIRECTORY_SEPARATOR;

	/**
	 * Lets you choose which connection group to
	 * use if no other is specified.
	 *
	 * @var string
	 */
	public $defaultGroup = 'default';

	/**
	 * The default database connection.
	 *
	 * @var array
	 */
//Poduction DB=======================
	public $default = [
		'DSN'      => '',
		'hostname' => 'localhost',
		'username' => 'u877275727_pupt_scis',
		'password' => 'PUPTscis2022',
		'database' => 'u877275727_scis_db',
		'DBDriver' => 'MySQLi',
		'DBPrefix' => '',
		'pConnect' => false,
		'DBDebug'  => (ENVIRONMENT !== 'production'),
		'charset'  => 'utf8',
		'DBCollat' => 'utf8_general_ci',
		'swapPre'  => '',
		'encrypt'  => false,
		'compress' => false,
		'strictOn' => false,
		'failover' => [],
		'port'     => 3306,
	];

//Testing or Development DB ============================
// 	public $default = [
// 		'DSN'      => '',
// 		'hostname' => 'localhost',
// 		'username' => 'u877275727_crewmates',
// 		'password' => 'PUPTscis2022',
// 		'database' => 'u877275727_scis_test',
// 		'DBDriver' => 'MySQLi',
// 		'DBPrefix' => '',
// 		'pConnect' => false,
// 		'DBDebug'  => (ENVIRONMENT !== 'development'),
// 		'charset'  => 'utf8',
// 		'DBCollat' => 'utf8_general_ci',
// 		'swapPre'  => '',
// 		'encrypt'  => false,
// 		'compress' => false,
// 		'strictOn' => false,
// 		'failover' => [],
// 		'port'     => 3306,
// 	];

//Local Hosting =================================
//     public $default = [
// 		'DSN'      => '',
// 		'hostname' => 'localhost',
// 		'username' => 'root',
// 		'password' => '',
// 		'database' => 'scis_db',
// 		'DBDriver' => 'MySQLi',
// 		'DBPrefix' => '',
// 		'pConnect' => false,
// 		'DBDebug'  => (ENVIRONMENT !== 'production'),
// 		'charset'  => 'utf8',
// 		'DBCollat' => 'utf8_general_ci',
// 		'swapPre'  => '',
// 		'encrypt'  => false,
// 		'compress' => false,
// 		'strictOn' => false,
// 		'failover' => [],
// 		'port'     => 3306,
// 	];

	/**
	 * This database connection is used when
	 * running PHPUnit database tests.
	 *
	 * @var array
	 */
	public $tests = [
		'DSN'      => '',
		'hostname' => '127.0.0.1',
		'username' => '',
		'password' => '',
		'database' => ':memory:',
		'DBDriver' => 'SQLite3',
		'DBPrefix' => 'db_',  // Needed to ensure we're working correctly with prefixes live. DO NOT REMOVE FOR CI DEVS
		'pConnect' => false,
		'DBDebug'  => (ENVIRONMENT !== 'production'),
		'charset'  => 'utf8',
		'DBCollat' => 'utf8_general_ci',
		'swapPre'  => '',
		'encrypt'  => false,
		'compress' => false,
		'strictOn' => false,
		'failover' => [],
		'port'     => 3306,
	];

	//--------------------------------------------------------------------

	public function __construct()
	{
		parent::__construct();

		// Ensure that we always set the database group to 'tests' if
		// we are currently running an automated test suite, so that
		// we don't overwrite live data on accident.
		if (ENVIRONMENT === 'testing')
		{
			$this->defaultGroup = 'tests';
		}
	}

	//--------------------------------------------------------------------

}