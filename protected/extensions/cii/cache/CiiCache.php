
<?php
/**
 * CCache class file.
 **/
class CiiCache extends CCache
{
	public function generateUniqueIdentifier()
	{
	       return 'ciims_' . md5(Yii::app()->params['encryptionKey']) . '_';
	}

        /**
         * Overloaded method to generate a truely unique id that we can intelligently flush without dumping our entire cache
         * 
         * @param string $key a key identifying a value to be cached
         * @return string a key generated from the provided key which ensures the uniqueness across applications
         */
        protected function generateUniqueKey($key)
        {
	       return $this->generateUniqueIdentifier() . md5(md5(Yii::getPathOfAlias('webroot')) . Yii::app()->params['encryptionKey'] . md5($this->keyPrefix.$key));
        }
}