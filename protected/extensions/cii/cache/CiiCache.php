<?php
/**
 * CCache class file.
 **/
class CiiCache extends CCache
{  
    /**
     * Converts a string (the encryption key) to an integer we can use with HashIds
     * @param string $n
     * @return integer
     */
    private function base62toDec($n)
    {
        $n = preg_replace("/[^A-Za-z0-9 ]/", '', $n);
        $vals = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $vals = array_flip(str_split($vals));
        $out = 0;
        $len = strlen($n);
        for ($i = 0; $i < $len; $i++)
        {
            $c = $n[$len - ($i + 1)];
            $out += $vals[$c] * pow(62, $i);
        }

        return (int)$out;
    }

    /**
     * Converts the encryption key into an integer via base64todec, then transforms that into a unique ID via HashIds
     * @return string
     */
    public function getBaseHash()
    {
        $hashids = new Hashids\Hashids(Yii::app()->params['encryptionKey'], 8);
        $id = $hashids->encode($this->base62toDec(Yii::app()->params['encryptionKey']));
        return $id;
    }

    /**
     * Generates a unique identified
     * @return string
     */
	public function generateUniqueIdentifier()
	{
        $id = $this->getBaseHash();
        return 'ciims.'.$id.'.';
	}

    /**
     * Overloaded method to generate a truely unique id that we can intelligently flush without dumping our entire cache
     * @param string $key a key identifying a value to be cached
     * @return string a key generated from the provided key which ensures the uniqueness across applications
     */
    protected function generateUniqueKey($key)
    {
        return $this->generateUniqueIdentifier().$this->keyPrefix.'.'.$key;
    }
}
