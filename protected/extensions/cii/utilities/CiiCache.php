
<?php
/**
 * CCache class file.
 **/
class CiiCache extends CCache
{
        /**
         * @param string $key a key identifying a value to be cached
         * @return sring a key generated from the provided key which ensures the uniqueness across applications
         */
        protected function generateUniqueKey($key)
        {
        	return md5(md5(Yii::getPathOfAlias('webroot')) . md5(Yii::app()->name) . md5($this->keyPrefix.$key));
        }
}