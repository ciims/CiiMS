<?php

/**
 * This class is a standing for an actually Yii application, and servers no other purpose OTHER than to provide us
 * With access to Yii::t() for message translations purposes. It also enables ciimessaages to generates translations for this file
 */
class Yii
{
    /**
     * Emulation of YiiBase::getPreferredLanguage
     * @return string
     */
    public static function getPreferredLanguage()
    {
        $preferredLanguages = Yii::getPreferredLanguages();
        return isset($preferredLanguages[0]) ? Yii::getCanonicalID($preferredLanguages[0]) : 'en_US';
    }
    
    /**
     * Emulation of YiiBase::getPreferredLanguages
     * @return array
     */
    public static function getPreferredLanguages()
    {
        $sortedLanguages=array();
        if(isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) && $n=preg_match_all('/([\w\-_]+)(?:\s*;\s*q\s*=\s*(\d*\.?\d*))?/',$_SERVER['HTTP_ACCEPT_LANGUAGE'],$matches))
        {
            $languages=array();

            for($i=0;$i<$n;++$i)
            {
                $q=$matches[2][$i];
                if($q==='')
                    $q=1;
                if($q)
                    $languages[]=array((float)$q,$matches[1][$i]);
            }

            usort($languages,create_function('$a,$b','if($a[0]==$b[0]) {return 0;} return ($a[0]<$b[0]) ? 1 : -1;'));
            foreach($languages as $language)
                $sortedLanguages[]=$language[1];
        }
	
	return $sortedLanguages;
    }

    /**
     * This method is a total lie. All that is does is fetch the appropriate translation from the appropriate file if it exists
     * @return string
     */
    public static function t($category, $message=NULL, $params=array(), $source=NULL, $language=NULL)
    {
        $category = explode('.', $category);
        $lang = Yii::getPreferredLanguage();

        $file = dirname(__FILE__) . DIRECTORY_SEPARATOR . 'messages' . DIRECTORY_SEPARATOR . $lang . DIRECTORY_SEPARATOR . $category[1] . '.php';

        if (file_exists($file))
        {
            $data = include($file);
	
            if (isset($data[$message]) && $data[$message] != "")
                $message = $data[$message];
        }

        return Yii::translateMessage($message, $params);
    }

    /**
     * Translates a particular message as an actualy translations
     * @param  string $message
     * @param  array $params
     * @return string
     */
    public static function translateMessage($message, $params = array())
    {
	foreach ($params as $k=>$v)
            $message = str_replace($k, $v, $message);

        return $message;
    }

    /**
     * Emulation of YiiBase::getCanonicalID($id)
     * @param  string $id
     * @return string CanonicalId
     */
    public static function getCanonicalID($id)
    {
        return strtolower(str_replace('-','_',$id));
    }

    /**
     * Emulation of CHtml::tag() for the non yii installer
     * @param  string $tag
     * @param  array $params
     * @param  string $text
     * @return DOM node
     */
    public static function tag($tag, $params = array(), $text)
    {
        $attributes = '';
        foreach ($params as $k=>$v)
            $attributes .= ' ' . $k .'=' . $v;

        return "<" . $tag . ' ' . $attributes . ">" . $text . "</" . $tag . ">";
    }
}
