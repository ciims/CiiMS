<?php

class m130712_220749_settings extends CDbMigration
{
	// Use safeUp/safeDown to do migration with transaction
	public function safeUp()
	{
		$this->generalSettings();
		$this->emailSettings();
		$this->socialSettings();

		// Because we're dealing with EVERY config value, we should flush the cache so that new values get loaded in
		Yii::app()->cache->flush();
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
	private function socialSettings()
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
							Yii::app()->db->createCommand('INSERT IGNORE INTO `configuration` (`key`, value, created, updated) VALUES (:key, :value, NOW(), NOW())')
				 	  			 ->bindParam(':key', $key)->bindParam(':value', $n)->execute();
						}
					}
					else
					{
						$value = $l;
						$key = 'ha_'.strtolower($k) . '_' .  $j;
						Yii::app()->db->createCommand('INSERT IGNORE INTO `configuration` (`key`, value, created, updated) VALUES (:key, :value, NOW(), NOW())')
				 	  		 ->bindParam(':key', $key)->bindParam(':value', $value)->execute();
					}
				}
			}
		}
	}

	/**
	 * Sets the notifyName
	 */
	private function emailSettings()
	{
		$v = 'CiiMS No Reply';
		$key = 'notifyName';
		Yii::app()->db->createCommand('INSERT IGNORE INTO `configuration` (`key`, value, created, updated) VALUES (:key, :value, NOW(), NOW())')
				 ->bindParam(':key', $key)->bindParam(':value', $v)->execute();
	}

	/**
	 * Sets general settings where possible
	 */
	private function generalSettings()
	{
		if (Yii::app()->name !== 'CiiMS Installer' && Yii::app()->name !== NULL)
		{
			$name = Yii::app()->name;
			$key = 'name';
			Yii::app()->db->createCommand('INSERT IGNORE INTO `configuration` (`key`, value, created, updated) VALUES (:key, :value, NOW(), NOW())')
				 ->bindParam(':key', $key)->bindParam(':value', $name)->execute();
		}

		$key = 'dateFormat';
		$v = 'F jS, Y';
		Yii::app()->db->createCommand('INSERT IGNORE INTO `configuration` (`key`, value, created, updated) VALUES (:key, :value, NOW(), NOW())')
				 ->bindParam(':key', $key)->bindParam(':value', $v)->execute();

		$key = 'timeFormat';
		$v = 'H:i';
		Yii::app()->db->createCommand('INSERT IGNORE INTO `configuration` (`key`, value, created, updated) VALUES (:key, :value, NOW(), NOW())')
				 ->bindParam(':key', $key)->bindParam(':value', $v)->execute();

		$key = 'timezone';
		$v = date('e');
		Yii::app()->db->createCommand('INSERT IGNORE INTO `configuration` (`key`, value, created, updated) VALUES (:key, :value, NOW(), NOW())')
				 ->bindParam(':key', $key)->bindParam(':value', $v)->execute();

		$key = 'defaultLanguage';
		$v = 'en_US';
		Yii::app()->db->createCommand('INSERT IGNORE INTO `configuration` (`key`, value, created, updated) VALUES (:key, :value, NOW(), NOW())')
				 ->bindParam(':key', $key)->bindParam(':value', $v)->execute();

		$key = 'menu';
		$v = 'admin|blog';
		Yii::app()->db->createCommand('INSERT IGNORE INTO `configuration` (`key`, value, created, updated) VALUES (:key, :value, NOW(), NOW())')
				 ->bindParam(':key', $key)->bindParam(':value', $v)->execute();

		$key = 'offline';
		$v = '0';
		Yii::app()->db->createCommand('INSERT IGNORE INTO `configuration` (`key`, value, created, updated) VALUES (:key, :value, NOW(), NOW())')
				 ->bindParam(':key', $key)->bindParam(':value', $v)->execute();

		$key = 'preferMarkdown';
		$v = '1';
		Yii::app()->db->createCommand('INSERT IGNORE INTO `configuration` (`key`, value, created, updated) VALUES (:key, :value, NOW(), NOW())')
				 ->bindParam(':key',$key)->bindParam(':value', $v)->execute();

		$key = 'bcrypt_cost';
		$v = '13';
		Yii::app()->db->createCommand('INSERT IGNORE INTO `configuration` (`key`, value, created, updated) VALUES (:key, :value, NOW(), NOW())')
				 ->bindParam(':key', $key)->bindParam(':value', $v)->execute();

		$key = 'searchPaginationSize';
		$v = '10';
		Yii::app()->db->createCommand('INSERT IGNORE INTO `configuration` (`key`, value, created, updated) VALUES (:key, :value, NOW(), NOW())')
				 ->bindParam(':key', $key)->bindParam(':value', $v)->execute();

		$key = 'categoryPaginationSize';
		$v = '10';
		Yii::app()->db->createCommand('INSERT IGNORE INTO `configuration` (`key`, value, created, updated) VALUES (:key, :value, NOW(), NOW())')
				 ->bindParam(':key', $key)->bindParam(':value', $v)->execute();

		$key = 'contentPaginationSize';
		$v = '10';
		Yii::app()->db->createCommand('INSERT IGNORE INTO `configuration` (`key`, value, created, updated) VALUES (:key, :value, NOW(), NOW())')
				 ->bindParam(':key', $key)->bindParam(':value', $v)->execute();
	}
}