<?php
/**
 * This file has been taken from Koowa framework. All rights reserved
 * KInflector to pluralize and singularize English nouns.
 * 
 * @version		$Id:inflector.php 46 2008-03-01 18:39:32Z mjaz $
 * @category	Koowa
 * @package		Koowa_Inflector
 * @copyright	Copyright (C) 2007 - 2008 Joomlatools. All rights reserved.
 * @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
 * @link     	http://www.koowa.org

 * 
 *
 * @author		Johan Janssens <johan@joomlatools.org>
 * @author		Mathias Verraes <mathias@joomlatools.org>
*/ 

class Inflector
{
   	/**
	 * Rules for pluralizing and singularizing of nouns.
	 *
	 * @var array
     */
	protected static $_rules = array
	(
		'pluralization' => array(
			'/move$/i' 					=> 'moves',
			'/sex$/i' 					=> 'sexes',
			'/child$/i' 				=> 'children',
			'/man$/i' 					=> 'men',
			'/foot$/i' 					=> 'feet',
			'/person$/i' 				=> 'people',
			'/(quiz)$/i' 				=> '$1zes',
			'/^(ox)$/i' 				=> '$1en',
			'/(m|l)ouse$/i' 			=> '$1ice',
			'/(matr|vert|ind|suff)ix|ex$/i'=> '$1ices',
			'/(x|ch|ss|sh)$/i' 			=> '$1es',
			'/([^aeiouy]|qu)ies$/i' 	=> '$1y',
			'/([^aeiouy]|qu)y$/i' 		=> '$1ies',
			'/(?:([^f])fe|([lr])f)$/i' 	=> '$1$2ves',
			'/sis$/i' 					=> 'ses',
			'/([ti]|addend)um$/i' 		=> '$1a',
            '/(alumn|formul)a$/i'       => '$1ae',
			'/(buffal|tomat|her)o$/i' 	=> '$1oes',
			'/(bu)s$/i' 				=> '$1ses',
			'/(alias|status)$/i' 		=> '$1es',
			'/(octop|vir)us$/i' 		=> '$1i',
            '/(gen)us$/i'               => '$1era',
			'/(ax|test)is$/i'	 		=> '$1es',
			'/s$/i' 					=> 's',
			'/$/' 						=> 's',
		),

		'singularization' => array(
			'/cookies$/i' 			=> 'cookie',
			'/moves$/i' 			=> 'move',
			'/sexes$/i' 			=> 'sex',
			'/children$/i' 			=> 'child',
			'/men$/i' 				=> 'man',
			'/feet$/i' 				=> 'foot',
			'/people$/i' 			=> 'person',
			'/databases$/i'			=> 'database',
			'/(quiz)zes$/i' 		=> '\1',
			'/(matr|suff)ices$/i' 	=> '\1ix',
			'/(vert|ind)ices$/i'    => '\1ex',
			'/^(ox)en/i' 			=> '\1',
			'/(alias|status)es$/i' 	=> '\1',
            '/(tomato|hero|buffalo)es$/i'  => '\1',
			'/([octop|vir])i$/i' 	=> '\1us',
            '/(gen)era$/i'          => '\1us',
			'/(cris|ax|test)es$/i' 	=> '\1is',
			'/(shoe)s$/i' 			=> '\1',
			'/(o)es$/i' 			=> '\1',
			'/(bus)es$/i' 			=> '\1',
			'/([m|l])ice$/i' 		=> '\1ouse',
			'/(x|ch|ss|sh)es$/i' 	=> '\1',
			'/(m)ovies$/i' 			=> '\1ovie',
			'/(s)eries$/i' 			=> '\1eries',
			'/([^aeiouy]|qu)ies$/i' => '\1y',
			'/([lr])ves$/i' 		=> '\1f',
			'/(tive)s$/i' 			=> '\1',
			'/(hive)s$/i' 			=> '\1',
			'/([^f])ves$/i' 		=> '\1fe',
			'/(^analy)ses$/i' 		=> '\1sis',
			'/((a)naly|(b)a|(d)iagno|(p)arenthe|(p)rogno|(s)ynop|(t)he)ses$/i' => '\1\2sis',
			'/([ti]|addend)a$/i' 	=> '\1um',
            '/(alumn|formul)ae$/i'  => '$1a',
			'/(n)ews$/i' 			=> '\1ews',
			'/(.*)s$/i' 			=> '\1',
		),

		'countable' => array(
			'aircraft',
			'cannon',
			'deer',
			'equipment',
			'fish',
			'information',
			'money',
			'moose',
			'rice',
			'series',
			'sheep',
			'species',
			'swine',
		)
	);

   	/**
 	 * Cache of pluralired and singularized nouns.
	 *
	 * @var array
     */
	protected static $_cache = array(
		'singularized' => array(),
		'pluralized'   => array()
	);
	
	/**
	 * Constructor
	 * 
	 * Prevent creating instances of this class by making the contrucgtor private
	 */
	private function __construct() { }
	
	/**
	 * Add a word to the cache, useful to make exceptions or to add words in 
	 * other languages
	 *
	 * @param	string	Singular word
	 * @param 	string	Plural word
	 */
	public static function addWord($singular, $plural)
	{
		self::$_cache['pluralized'][$singular]	= $plural;
		self::$_cache['singularized'][$plural] 	= $singular;
	}
	
   	/**
	 * Singular English word to plural.
	 *
	 * @param 	string Word to pluralize
	 * @return 	string Plural noun
	 */
	public static function pluralize($word)
	{
		//Get the cached noun of it exists
 	   	if(isset(self::$_cache['pluralized'][$word])) {
			return self::$_cache['pluralized'][$word];
 	   	}

		//Create the plural noun
		if (in_array($word, self::$_rules['countable'])) {
			$_cache['pluralized'][$word] = $word;
			return $word;
		}

		foreach (self::$_rules['pluralization'] as $regexp => $replacement)
		{
			$matches = null;
			$plural = preg_replace($regexp, $replacement, $word, -1, $matches);
			if ($matches > 0) {
				$_cache['pluralized'][$word] = $plural;
				return $plural;
			}
		}

		return $word;
	}

   	/**
	 * Plural English word to singular.
	 *
	 * @param 	string Word to singularize.
	 * @return 	string Singular noun
	 */
	public static function singularize($word)
	{
		//Get the cached noun of it exists
 	   	if(isset(self::$_cache['singularized'][$word])) {
			return self::$_cache['singularized'][$word];
 	   	}

		//Create the singular noun
		if (in_array($word, self::$_rules['countable'])) {
			$_cache['singularized'][$word] = $word;
			return $word;
		}


		foreach (self::$_rules['singularization'] as $regexp => $replacement)
		{
			$matches = null;
			$singular = preg_replace($regexp, $replacement, $word, -1, $matches);
			if ($matches > 0) {
				$_cache['singularized'][$word] = $singular;
				return $singular;
			}
		}

 	   return $word;
	}

   	/**
	 * Returns given word as CamelCased
	 *
	 * Converts a word like "send_email" to "SendEmail". It
	 * will remove non alphanumeric character from the word, so
	 * "who's online" will be converted to "WhoSOnline"
	 *
	 * @param    string 	$word    Word to convert to camel case
	 * @return 	string	UpperCamelCasedWord
	 * @see variablize
	 */
	public static function camelize($word)
	{
		$result = str_replace(' ', '', ucwords(str_replace('_', ' ', $word)));
		return $result;
	}

   	/**
	 * Converts a word "into_it_s_underscored_version"
	 *
	 * Convert any "CamelCased" or "ordinary Word" into an "underscored_word".
	 *
	 * @param    string    $word    Word to underscore
	 * @return string Underscored word
	 */
	public static function underscore($word)
	{
		$result = strtolower(preg_replace('/(?<=\\w)([A-Z])/', '_\\1', $word));
		return $result;
	}

	/**
	 * Convert any "CamelCased" word into an array of strings
	 *
	 * Returns an array of strings ach of which is a substring of string formed
	 * by splitting it at the camelcased letters.
	 *
	 * @param    string    $word    Word to explode
	 * @return array Array of strings
	 */
	public static function explode($word)
	{
		$result = explode('_', self::underscore($word));
		return $result;
	}
	
	/**
	 * Convert  an array of strings into a "CamelCased" word
	 *
	 * @param  array    $words   Array to implode
	 * @return string  UpperCamelCasedWord
	 */
	public static function implode($words)
	{
		$result = self::camelize(implode('_', $words));
		return $result;
	}

   	/**
	 * Returns a human-readable string from $word
	 *
	 * Returns a human-readable string from $word, by replacing
	 * underscores with a space, and by upper-casing the initial
	 * character by default.
	 *
	 * @param    string    $word    String to "humanize"
	 * @return string Human-readable word
     */
	public static function humanize($word)
	{
		$result = ucwords(str_replace("_", " ", $word));
		return $result;
	}

   	/**
	 * Converts a class name to its table name according to Koowa
	 * naming conventions.
	 *
	 * Converts "Person" to "people"
	 *
	 * @param  string    $className    Class name for getting related table_name.
	 * @return string plural_table_name
	 * @see classify
	 */
	public static function tableize($className)
	{
		$result = self::pluralize(self::underscore($className));
		return $result;
	}
	/**
	 * Titlecase a string
	 * @return 
	 * @param $string Object
	 */
	public static function titlize($word)
	{
		$parts = self::explode(self::camelize($word));

		foreach($parts as &$part)
			$part = ucfirst($part);
			
		return implode(' ',$parts);	
	
	}
   	/**
	 * Converts a table name to its class name according to Koowa
	 * naming conventions.
	 *
	 * Converts "people" to "Person"
	 *
	 * @see tableize
	 * @param    string    $table_name    Table name for getting related ClassName.
	 * @return string SingularClassName
	 */
	public static function classify($tableName)
	{
		$result = self::camelize(self::singularize($tableName));
		return $result;
	}

	/**
	 * Returns camelBacked version of a string.
	 *
	 * Same as camelize but first char is lowercased
	 *
	 * @param string $string
	 * @return string
	 * @see camelize
	 */
	public static function variablize($string)
	{
		$string   = self::camelize(self::underscore($string));
		$result  = strtolower(substr($string, 0, 1));
		$variable = preg_replace('/\\w/', $result, $string, 1);
		return $variable;
	}

	/**
	 * Check to see if an English word is singular
	 *
	 * @param string $string The word to check
	 * @return boolean
	 */
	public static function isSingular($string) {
		return self::singularize(self::pluralize($string)) == $string;
	}

	/**
	 * Check to see if an Enlish word is plural
	 *
	 * @param string $string
	 * @return boolean
	 */
	public static function isPlural($plural) {
		return self::pluralize(self::singularize($plural)) == $plural;
	}

    /**
     * Gets a part of a CamelCased word by index
     *
     * Use a negative index to start at the last part of the word (-1 is the
     * last part)
     *
     * @param	string	Word
     * @param	integer	Index of the part
     * @param	string	Default value
     */
    public static function getPart($string, $index, $default = null)
    {
    	$parts = self::explode($string);

        if($index < 0) {
            $index = count($parts) + $index;
        }

        return isset($parts[$index]) ? $parts[$index] : $default;
    }

    /**
     * Splits a string using a separator
     *
     * @param	string	Separator
     * @param	string	Subject
     * @return	array	Variablized prefix, base, and suffix
     */
    public static function split($separator, $string )
    {
        $matches    = null;
        $result     = array(
                'prefix'=> '',
                'base'  => self::variablize($separator),
                'suffix'=> '',
        );

        if ( preg_match( "/(.*?)$separator(.*)/i", $string, $matches ) ) {
            $result['prefix'] = self::variablize($matches[1]);
            $result['suffix'] = self::variablize($matches[2]);
        }

        return $result;
    }
}
