<?php

class Cii {
	
    public static function getVersion()
    {
        $data = json_decode(file_get_contents(Yii::getPathOfAlias('ext.cii').'/ciims.json'),true);
        return $data['version'];
    }

	/**
	 * Checks for the existance of an item at a given array index and returns that object if it exists
	 * @param array $array 	 The array to check
	 * @param mixed $item	 The indicie to check against
	 * @param mixed $default The default return value
	 * @return mixed array index or default
	 */
	public static function get($array=NULL, $item=NULL, $default=NULL)
	{
		if ($array === NULL)
			return isset($default) ? $default : $item;
		
		if (is_object($array) && isset($array->$item))
            return $array->$item; 
		
        if (is_array($array))
            return isset($array[$item]) && !empty($array) ? $array[$item] : $default;
        
		if (!is_array($array))
			return isset($array) ? $array : $item;
		
		if (isset($array[$item]) && !empty($array))
			return $array[$item];
        
		return $default;	
	}
	
    /**
     * Gets a configuration value from CiiMS::Configuration
     * @param  string $key     The key we want to retrieve from Configuration
     * @param  mixed  $default The default value to return if key is not found
     * @return mixed           The value from Config, or default
     */
    public static function getConfig($key, $default=NULL, $prefix='settings_')
    {
        $cache = Yii::app()->cache->get($prefix.$key);

        if ($cache === false)
        {
            $data = Yii::app()->db->createCommand('SELECT value FROM configuration AS t WHERE t.key = :key')
                                   ->bindParam(':key', $key)
                                   ->queryAll();

            if (!isset($data[0]['value']))
                $cache = $default;
            else
                $cache = $data[0]['value'];

            Yii::app()->cache->set($prefix.$key, $cache);
        }

        return $cache;
    }

    /**
     * Override control file
     * @return array
     */
    public static function getCiiConfig()
    {
        $config = __DIR__ . '/../../../config/ciiparams.php';
        if (file_exists($config))
            return require $config;

        return array();
    }

    public static function setApplicationLanguage()
    {
        $app = Yii::app();

        // Set the default language to whatever we have in the dahsboard
        $app->language = Cii::getConfig('defaultLanguage');

        // If the language is set via POST, accept it
        if (Cii::get($_POST, '_lang', false))
            $app->language = $app->session['_lang'] = $_POST['_lang'];
        else if ($app->session['_lang'] != NULL)
            $app->language = $app->session['_lang'];
        else
            $app->language = $app->session['_lang'] = Yii::app()->getRequest()->getPreferredLanguage();

        Yii::app()->language = $app->session['_lang'] = $app->language;

        return $app->language;
    }

    /**
     * Gets a configuration value from user_metadata
     * @param  string $key     The key we want to retrieve from Configuration
     * @param  mixed  $default The default value to return if key is not found
     * @return mixed           The value from Config, or default
     */
    public static function getUserConfig($key, $default=NULL)
    {
        $uid = Yii::app()->user->id;
        $data = Yii::app()->db->createCommand('SELECT value FROM user_metadata AS t WHERE t.key = :key AND t.user_id = :id')
                              ->bindParam(':id', $uid)
                              ->bindParam(':key', $key)
                              ->queryAll();

        if (!isset($data[0]['value']))
            return NULL;
        
        return $data[0]['value'];
    }

    /**
     * Consolodates the finding of retrievinv the bcrypt_Cost
     * @param  integer $default The default bcrypt cost
     * @return int              The bcrypt cost
     */
    public static function getBcryptCost($default = 13)
    {
        return Cii::getConfig('bcrypt_cost', $default);
    }

	/**
	 * Provides methods to format a date throughout a model
     * By forcing all components through this method, we can provide a comprehensive
     * and consistent date format for the entire site
     * @param  mxied  $date   Likely a string in date format (of some kind)
     * @param  string $format The format we want to FORCE the dts to be formatted to
     *                        If this isn't supplied, we'll pull it from Cii::getConfig()
     * @return Date
     */
	public static function formatDate($date, $format = NULL)
	{
        if ($format == NULL)
            $format = Cii::getConfig('dateFormat') . ' @ ' . Cii::getConfig('timeFormat');

        if ($format == ' @ ')
            $format = 'F jS, Y @ H:i';

		return date($format, strtotime($date));
	}

    /**
     * Automatically handles TimeAgo for UTC
     *
     * @param  mxied  $date   Likely a string in date format (of some kind)
     * @param  string $format The format we want to FORCE the dts to be formatted to
     *                        If this isn't supplied, we'll pull it from Cii::getConfig()
     * @return CHtml:tag span element
     */
    public static function timeago($date, $format = NULL)
    {
        Yii::app()->controller->widget('ext.timeago.JTimeAgo', array(
            'selector' => ' .timeago',
            'settings' => array(
                'refreshMillis' => 60000,
                'allowFuture' => true,
                  'strings' => array(
                    'prefixAgo' => null,
                    'prefixFromNow' => null,
                    'suffixAgo' => "ago",
                    'suffixFromNow' => "from now",
                    'seconds' => "less than a minute",
                    'minute' => "about a minute",
                    'minutes' => "%d minutes",
                    'hour' => "about an hour",
                    'hours' => "about %d hours",
                    'day' => "a day",
                    'days' => "%d days",
                    'month' => "about a month",
                    'months' => "%d months",
                    'year' => "about a year",
                    'years' => "%d years",
                    'wordSeparator' => " ",
                    'numbers' => array()
                  )
            )
        ));

       return CHtml::tag(
            'span',
            array(
                'class'=>"timeago",
                'style'=>'text-decoration:none; cursor: default', // Post processing class application
                'rel'=>'tooltip',
                'data-original-title'=>Cii::formatDate($date, $format),
                'title'=>CTimestamp::formatDate('c', strtotime($date))
            ),
            Cii::formatDate($date, $format)
        );
    }
	
    /**
     * Retrieves Analytics.js Providers
     * @return array $providors
     */
    public static function getAnalyticsProviders()
    {
        $providers = Yii::app()->cache->get('analyticsjs_providers');

        if ($providers === false)
        {
            // Import Analytics Settings so that we can appropriately determine the type of objects
            Yii::import('application.modules.dashboard.components.CiiSettingsModel');
            Yii::import('application.modules.dashboard.models.AnalyticsSettings');

            $analytics = new AnalyticsSettings;
            $rules = $analytics->rules();
            unset($analytics);

            // Init the array
            $providers = array();

            // Retrieve all the providors that are enabled
            $response = Yii::app()->db->createCommand('SELECT REPLACE(`key`, "analyticsjs_", "") AS `key`, value FROM `configuration` WHERE `key` LIKE "analyticsjs_%_enabled" AND value = 1')->queryAll();
            foreach ($response as $element)
            {
                $k = $element['key'];
                $provider = explode('_', str_replace("__", " " ,str_replace("___", ".", $k)));
                $provider = reset($provider);

                $sqlProvider = str_replace(" ", "__" ,str_replace(".", "___", $provider));
                $data = Yii::app()->db->createCommand('SELECT REPLACE(`key`, "analyticsjs_", "") AS `key`, value FROM `configuration` WHERE `key` LIKE "analyticsjs_' . $sqlProvider .'%" AND `key` != "analyticsjs_' . $sqlProvider .'_enabled"')->queryAll();

                $provider = str_replace('pwk', 'Piwik', $provider);

                foreach ($data as $el)
                {
                    $k = $el['key'];
                    $k = str_replace("pwk_", "Piwik_", $k);

                    $v = $el['value'];
                    $p = explode('_', str_replace("__", " " ,str_replace("___", ".", $k)));
                    if ($v !== "")
                    {
                        $thisRule = 'string';
                        foreach ($rules as $rule)
                        {
                            if (strpos($rule[0], 'analyticsjs_' . $k) !== false)
                                $thisRule = $rule[1];
                        }

                        if ($thisRule == 'boolean')
                        {
                            if ($v == "0")
                                $providers[$provider][$p[1]] = (bool)false;
                            else if ($v == "1")          
                                $providers[$provider][$p[1]] = (bool)true;
                            else
                                $providers[$provider][$p[1]] = 'null';
                        }
                        else
                        {
                            if ($v == "" || $v == null)
                                $providers[$provider][$p[1]] = 'null';
                            else
                                $providers[$provider][$p[1]] = $v;
                        }

                    }
                }
            }
            Yii::app()->cache->set('analyticsjs_providers', $providers);
        }

        $providers['CiiMS'] = array(
            'endpoint' => '/api'
        );
        return $providers;
    }

    /**
     * Returns an array of HybridAuth providers to be used by HybridAuth and other parts of CiiMS
     * @return array of HA Providers prettily formatted
     */
    public static function getHybridAuthProviders()
    {
        $providers = Yii::app()->cache->get('hybridauth_providers');

        if ($providers === false)
        {
            // Init the array
            $providers = array();

            // Query for the providors rather than using Cii::getConfig(). The SQL performance SHOULD be faster
            $response = Yii::app()->db->createCommand('SELECT REPLACE(`key`, "ha_", "") AS `key`, value FROM `configuration` WHERE `key` LIKE "ha_%"')->queryAll();
            foreach ($response as $element)
            {
                $k = $element['key'];
                $v = $element['value'];
                $data = explode('_', $k);
                $provider = $data[0];
                $key = $data[1];
                if ($provider == 'linkedin')
                    $provider = 'LinkedIn';

                $provider = ucwords($provider);

                if (!in_array($key, array('id', 'key', 'secret')))
                    $providers[$provider][$key] = $v;
                else
                    $providers[$provider]['keys'][$key] = $v;
            }
            
            Yii::app()->cache->set('hybridauth_providers', $providers);
        }
        
        return $providers;
    }

    /**
     * Provides a _very_ simple encryption method that we can user to encrypt things like passwords.
     * This way, if the database is exposed AND the encryptionKey is not exposed, important stuff like
     * SMTP Passwords and what not aren't publicly exposed.
     *
     * Since often times these passwords are the same password used to access more critical seems, _I_
     * think it is imnportant that they aren't stored in plain text, but with some form of reversible encryption
     * so that the user doesn't have to decrypt it on their own.
     *
     * The purpose of this is to _assist_ in hardened security, and is in no means a substitude for a more comprehensive
     * security strategy. This _WILL NOT_ help you if you encryptionKey is taken as well - but it might buy you some time.
     * 
     * @param  string $field The data we want to ecrnypt
     * @return string        encrypted data
     */
    
    public static function encrypt($field, $key = NULL)
    {
        if ($key == NULL)
            $key = Yii::app()->params['encryptionKey'];

        return base64_encode(
            mcrypt_encrypt(
                MCRYPT_RIJNDAEL_256, 
                md5($key), 
                $field,
                MCRYPT_MODE_CBC, 
                md5(md5($key))
                )
            );
    }

    /**
     * Acts are a counterpart to Cii::encrypt().
     * @see  Cii::encrypt()
     * @param  string $field encrypted text
     * @return string        unencrypted text
     */
    public static function decrypt($field, $key = NULL)
    {
        if ($key == NULL)
            $key = Yii::app()->params['encryptionKey'];
        
        return rtrim(
            mcrypt_decrypt(
                MCRYPT_RIJNDAEL_256, 
                md5($key),
                base64_decode($field), 
                MCRYPT_MODE_CBC, 
                md5(md5($key))
            ), 
        "\0");
    }

	/**
	 * Beause doing this all over the place is stupid...
     * @param  mixed $data     The data we want to debug
     * @param  bool $full_dump Whether or not we want the data outputted with var_dump or not
     */
	public static function debug($data, $full_dump = false)
	{
		echo '<pre class="cii-debug">';
		if ($full_dump)
			var_dump($data);
		else
			print_r($data);	
		echo '</pre>';
		
		return;
	}
	
	/**************************  Inflector Data **************************/
	
	/**
	 * Inflector for pluralize and singularize English nouns.
	 *
	 * This Inflector is a port of Ruby on Rails Inflector.
	 *
	 * It can be really helpful for developers that want to
	 * create frameworks based on naming conventions rather than
	 * configurations.
	 *
	 * It was ported to PHP for the Akelos Framework
	 *
	 * @author Bermi Ferrer Martinez 
	 * @copyright Copyright (c) 2002-2006, Akelos Media, S.L. http://www.akelos.org
	 * @license GNU Lesser General Public License 
	 * @since 0.1
	 * @version $Revision 0.1 $
	 */

	/**
     * Pluralizes English nouns.
     *
     * @access public
     * @static
     * @param    string    $word    English noun to pluralize
     * @return string Plural noun
     */
    public static function pluralize( $string ) 
    {

        $plural = array(
            array('/(quiz)$/i',               "$1zes"   ),
	        array('/^(ox)$/i',                "$1en"    ),
	        array('/([m|l])ouse$/i',          "$1ice"   ),
	        array('/(matr|vert|ind)ix|ex$/i', "$1ices"  ),
	        array('/(x|ch|ss|sh)$/i',         "$1es"    ),
	        array('/([^aeiouy]|qu)y$/i',      "$1ies"   ),
	        array('/([^aeiouy]|qu)ies$/i',    "$1y"     ),
            array('/(hive)$/i',               "$1s"     ),
            array('/(?:([^f])fe|([lr])f)$/i', "$1$2ves" ),
            array('/sis$/i',                  "ses"     ),
            array('/([ti])um$/i',             "$1a"     ),
            array('/(buffal|tomat)o$/i',      "$1oes"   ),
            array('/(bu)s$/i',                "$1ses"   ),
            array('/(alias|status)$/i',       "$1es"    ),
            array('/(octop|vir)us$/i',        "$1i"     ),
            array('/(ax|test)is$/i',          "$1es"    ),
            array('/s$/i',                    "s"       ),
            array('/$/',                      "s"       )
        );

        $irregular = array(
	        array('move',   'moves'    ),
	        array('sex',    'sexes'    ),
	        array('child',  'children' ),
	        array('man',    'men'      ),
	        array('person', 'people'   )
        );

        $uncountable = array( 'sheep', 'fish','series', 'species',' money', 'rice', 'information', 'equipment');

        // save some time in the case that singular and plural are the same
        if (in_array( strtolower( $string ), $uncountable ))
        	return $string;

        // check for irregular singular forms
        foreach ($irregular as $noun)
        {
	        if (strtolower( $string ) == $noun[0])
	            return $noun[1];
        }

        // check for matches using regular expressions
        foreach ($plural as $pattern)
        {
	        if (preg_match($pattern[0], $string))
	            return preg_replace($pattern[0], $pattern[1], $string);
        }
        return $string;
    }


    // }}}
    // {{{ singularize()

    /**
     * Singularizes English nouns.
     *
     * @access public
     * @static
     * @param    string    $word    English noun to singularize
     * @return string Singular noun.
     */
    public static function singularize($word)
    {
        $singular = array (
        '/(quiz)zes$/i' => '\1',
        '/(matr)ices$/i' => '\1ix',
        '/(vert|ind)ices$/i' => '\1ex',
        '/^(ox)en/i' => '\1',
        '/(alias|status)es$/i' => '\1',
        '/([octop|vir])i$/i' => '\1us',
        '/(cris|ax|test)es$/i' => '\1is',
        '/(shoe)s$/i' => '\1',
        '/(o)es$/i' => '\1',
        '/(bus)es$/i' => '\1',
        '/([m|l])ice$/i' => '\1ouse',
        '/(x|ch|ss|sh)es$/i' => '\1',
        '/(m)ovies$/i' => '\1ovie',
        '/(s)eries$/i' => '\1eries',
        '/([^aeiouy]|qu)ies$/i' => '\1y',
        '/([lr])ves$/i' => '\1f',
        '/(tive)s$/i' => '\1',
        '/(hive)s$/i' => '\1',
        '/([^f])ves$/i' => '\1fe',
        '/(^analy)ses$/i' => '\1sis',
        '/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
        '/([ti])a$/i' => '\1um',
        '/(n)ews$/i' => '\1ews',
        '/s$/i' => '',
        );

        $uncountable = array('equipment', 'information', 'rice', 'money', 'species', 'series', 'fish', 'sheep');

        $irregular = array(
        'person' => 'people',
        'man' => 'men',
        'child' => 'children',
        'sex' => 'sexes',
        'move' => 'moves');

        $lowercased_word = strtolower($word);
        foreach ($uncountable as $_uncountable){
            if(substr($lowercased_word,(-1*strlen($_uncountable))) == $_uncountable){
                return $word;
            }
        }

        foreach ($irregular as $_plural=> $_singular){
            if (preg_match('/('.$_singular.')$/i', $word, $arr)) {
                return preg_replace('/('.$_singular.')$/i', substr($arr[0],0,1).substr($_plural,1), $word);
            }
        }

        foreach ($singular as $rule => $replacement) {
            if (preg_match($rule, $word)) {
                return preg_replace($rule, $replacement, $word);
            }
        }

        return $word;
    }

    // }}}
    // {{{ titleize()

    /**
     * Converts an underscored or CamelCase word into a English
     * sentence.
     *
     * The titleize function converts text like "WelcomePage",
     * "welcome_page" or  "welcome page" to this "Welcome
     * Page".
     * If second parameter is set to 'first' it will only
     * capitalize the first character of the title.
     *
     * @access public
     * @static
     * @param    string    $word    Word to format as tile
      * @param    string    $uppercase    If set to 'first' it will only uppercase the
     * first character. Otherwise it will uppercase all
     * the words in the title.
     * @return string Text formatted as title
     */
    public static function titleize($word, $uppercase = '')
    {
        $uppercase = $uppercase == 'first' ? 'ucfirst' : 'ucwords';
        return $uppercase(Cii::humanize(Cii::underscore($word)));
    }

    public static function underscoretowords($word)
    {
        return ucwords(str_replace("_", " ", Cii::underscore($word)));
    }
    // }}}
    // {{{ camelize()

    /**
     * Returns given word as CamelCased
     *
     * Converts a word like "send_email" to "SendEmail". It
     * will remove non alphanumeric character from the word, so
     * "who's online" will be converted to "WhoSOnline"
     *
     * @access public
     * @static
     * @see variablize
     * @param    string    $word    Word to convert to camel case
     * @return string UpperCamelCasedWord
     */
    public static function camelize($word)
    {
        return str_replace(' ','',ucwords(preg_replace('/[^A-Z^a-z^0-9]+/',' ',$word)));
    }

    // }}}
    // {{{ underscore()

    /**
    * Converts a word "into_it_s_underscored_version"
    *
    * Convert any "CamelCased" or "ordinary Word" into an
    * "underscored_word".
    *
    * This can be really useful for creating friendly URLs.
    *
    * @access public
    * @static
    * @param    string    $word    Word to underscore
    * @return string Underscored word
    */
   public static function underscore($word)
    {
        return  strtolower(preg_replace('/[^A-Z^a-z^0-9]+/','_',
        preg_replace('/([a-zd])([A-Z])/','$1_$2',
        preg_replace('/([A-Z]+)([A-Z][a-z])/','$1_$2',$word))));
    }

    // }}}
    // {{{ humanize()

    /**
     * Returns a human-readable string from $word
     *
     * Returns a human-readable string from $word, by replacing
     * underscores with a space, and by upper-casing the initial
     * character by default.
     *
     * If you need to uppercase all the words you just have to
     * pass 'all' as a second parameter.
     *
     * @access public
     * @static
     * @param    string    $word    String to "humanize"
     * @param    string    $uppercase    If set to 'all' it will uppercase all the words
     * instead of just the first one.
     * @return string Human-readable word
     */
    public static function humanize($word, $uppercase = '')
    {
        $uppercase = $uppercase == 'all' ? 'ucwords' : 'ucfirst';
        return $uppercase(str_replace('_',' ',preg_replace('/_id$/', '',$word)));
    }

    // }}}
    // {{{ variablize()

    /**
     * Same as camelize but first char is underscored
     *
     * Converts a word like "send_email" to "sendEmail". It
     * will remove non alphanumeric character from the word, so
     * "who's online" will be converted to "whoSOnline"
     *
     * @access public
     * @static
     * @see camelize
     * @param    string    $word    Word to lowerCamelCase
     * @return string Returns a lowerCamelCasedWord
    */
    public static function variablize($word)
    {
        $word = Cii::camelize($word);
        return strtolower($word[0]).substr($word,1);
    }

    // }}}
    // {{{ tableize()

    /**
     * Converts a class name to its table name according to rails
     * naming conventions.
     *
     * Converts "Person" to "people"
     *
     * @access public
     * @static
     * @see classify
     * @param    string    $class_name    Class name for getting related table_name.
     * @return string plural_table_name
     */
    public static function tableize($class_name)
    {
        return Cii::pluralize(Cii::underscore($class_name));
    }

    // }}}
    // {{{ classify()

    /**
     * Converts a table name to its class name according to rails
     * naming conventions.
     *
     * Converts "people" to "Person"
     *
     * @access public
     * @static
     * @see tableize
     * @param    string    $table_name    Table name for getting related ClassName.
     * @return string SingularClassName
     */
    public static function classify($table_name)
    {
        return Cii::camelize(Cii::singularize($table_name));
    }

    // }}}
    // {{{ ordinalize()

    /**
     * Converts number to its ordinal English form.
     *
     * This method converts 13 to 13th, 2 to 2nd ...
     *
     * @access public
     * @static
     * @param    integer    $number    Number to get its ordinal value
     * @return string Ordinal representation of given string.
     */
    public static function ordinalize($number)
    {
        if (in_array(($number % 100),range(11,13))){
            return $number.'th';
        }else{
            switch (($number % 10)) {
                case 1:
                return $number.'st';
                break;
                case 2:
                return $number.'nd';
                break;
                case 3:
                return $number.'rd';
                default:
                return $number.'th';
                break;
            }
        }
    }
}
