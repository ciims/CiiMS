<?php

class m130712_220749_settings extends CDbMigration
{
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
		$connection = $this->getDbConnection();
		$this->generalSettings($connection);
		$this->emailSettings($connection);
		$this->socialSettings($connection);
		return true;
	}

	/**
	 * This migration should not be undone
	 */
	public function safeDown()
	{
		echo "m130712_220749_settings does not support migration down.\n";
		return false;
	}

	/**
	 * Attempts to import settings from HybridAuth config.
	 */
	private function socialSettings(&$connection)
	{
		$config = Yii::app()->getModules(false);
		if (isset($config['hybridauth']))
		{
			foreach (Cii::get(Cii::get($config, 'hybridauth', array()), 'providers', array()) as $k=>$v)
			{
				$key = 'ha_'.strtolower($k) . '_';
				foreach ($v as $j=>$l)
				{
					if ($j == 'keys')
					{
						foreach ($l as $m=>$n)
						{
							$key  = 'ha_'.strtolower($k) . '_' . $m;
							$value = $n;
							$connection->createCommand('INSERT IGNORE INTO `configuration` (`key`, value, created, updated) VALUES (:key, :value, NOW(), NOW())')
				 	  			 ->bindParam(':key', $key)->bindParam(':value', $n)->execute();
						}
					}
					else
					{
						$value = $l;
						$key = 'ha_'.strtolower($k) . '_' .  $j;
						$connection->createCommand('INSERT IGNORE INTO `configuration` (`key`, value, created, updated) VALUES (:key, :value, NOW(), NOW())')
				 	  		 ->bindParam(':key', $key)->bindParam(':value', $value)->execute();
					}
				}
			}
		}
	}

	/**
	 * Sets the notifyName
	 * @param CDbConnection $connection
	 */
	private function emailSettings(&$connection)
	{
		$v = 'CiiMS No Reply';
		$key = 'notifyName';
		$connection->createCommand('INSERT IGNORE INTO `configuration` (`key`, value, created, updated) VALUES (:key, :value, NOW(), NOW())')
				 ->bindParam(':key', $key)->bindParam(':value', $v)->execute();
	}

	/**
	 * Sets general settings where possible
	 * @param CDbConnection $connection
	 */
	private function generalSettings(&$connection)
	{
		if (Yii::app()->name !== 'CiiMS Installer' && Yii::app()->name !== NULL)
		{
			$name = Yii::app()->name;
			$key = 'name';
			$connection->createCommand('INSERT IGNORE INTO `configuration` (`key`, value, created, updated) VALUES (:key, :value, NOW(), NOW())')
				 ->bindParam(':key', $key)->bindParam(':value', $name)->execute();
		}

		$key = 'dateFormat';
		$v = 'F jS, Y';
		$connection->createCommand('INSERT IGNORE INTO `configuration` (`key`, value, created, updated) VALUES (:key, :value, NOW(), NOW())')
				 ->bindParam(':key', $key)->bindParam(':value', $v)->execute();

		$key = 'timeFormat';
		$v = 'H:i';
		$connection->createCommand('INSERT IGNORE INTO `configuration` (`key`, value, created, updated) VALUES (:key, :value, NOW(), NOW())')
				 ->bindParam(':key', $key)->bindParam(':value', $v)->execute();

		$key = 'timezone';
		$v = date('e');
		$connection->createCommand('INSERT IGNORE INTO `configuration` (`key`, value, created, updated) VALUES (:key, :value, NOW(), NOW())')
				 ->bindParam(':key', $key)->bindParam(':value', $v)->execute();

		$key = 'defaultLanguage';
		$v = 'en_US';
		$connection->createCommand('INSERT IGNORE INTO `configuration` (`key`, value, created, updated) VALUES (:key, :value, NOW(), NOW())')
				 ->bindParam(':key', $key)->bindParam(':value', $v)->execute();

		$key = 'menu';
		$v = 'dashboard|blog';
		$connection->createCommand('INSERT IGNORE INTO `configuration` (`key`, value, created, updated) VALUES (:key, :value, NOW(), NOW())')
				 ->bindParam(':key', $key)->bindParam(':value', $v)->execute();

		$key = 'offline';
		$v = '0';
		$connection->createCommand('INSERT IGNORE INTO `configuration` (`key`, value, created, updated) VALUES (:key, :value, NOW(), NOW())')
				 ->bindParam(':key', $key)->bindParam(':value', $v)->execute();

		$key = 'preferMarkdown';
		$v = '1';
		$connection->createCommand('INSERT IGNORE INTO `configuration` (`key`, value, created, updated) VALUES (:key, :value, NOW(), NOW())')
				 ->bindParam(':key',$key)->bindParam(':value', $v)->execute();

		$key = 'bcrypt_cost';
		$v = '13';
		$connection->createCommand('INSERT IGNORE INTO `configuration` (`key`, value, created, updated) VALUES (:key, :value, NOW(), NOW())')
				 ->bindParam(':key', $key)->bindParam(':value', $v)->execute();

		$key = 'searchPaginationSize';
		$v = '10';
		$connection->createCommand('INSERT IGNORE INTO `configuration` (`key`, value, created, updated) VALUES (:key, :value, NOW(), NOW())')
				 ->bindParam(':key', $key)->bindParam(':value', $v)->execute();

		$key = 'categoryPaginationSize';
		$v = '10';
		$connection->createCommand('INSERT IGNORE INTO `configuration` (`key`, value, created, updated) VALUES (:key, :value, NOW(), NOW())')
				 ->bindParam(':key', $key)->bindParam(':value', $v)->execute();

		$key = 'contentPaginationSize';
		$v = '10';
		$connection->createCommand('INSERT IGNORE INTO `configuration` (`key`, value, created, updated) VALUES (:key, :value, NOW(), NOW())')
				 ->bindParam(':key', $key)->bindParam(':value', $v)->execute();
	}
}