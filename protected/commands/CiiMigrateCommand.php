<?php
// Import System/Cli/Commands/MigrateCommand
Yii::import('system.cli.commands.MigrateCommand');
/**
 * This class is an injection container for CDbMigration which permits us to
 * directly access CDbMigrations from our web application without having
 * access to CLI or knowing before hand what our DSN is.
 * 
 * Under no circumstances, should this be called directly from command line.
 * 
 */
class CiiMigrateCommand extends MigrateCommand
{
	/**
	 * @var array $dsn
	 * The DSN and CDbConnectionString information from CWebApplication
	 */
	public $dsn = array();
	
	/**
	 * This is our overloaded getDbConnection, allowing us to tell yii what our db connection is
	 * without it having to go through
	 */
	public function getDbConnection()
	{
		$connection = new CDbConnection("mysql:host={$this->dsn['host']};dbname={$this->dsn['dbname']}", $this->dsn['username'], $this->dsn['password']);
		$connection->setActive(true);
		return $connection;
	}
}