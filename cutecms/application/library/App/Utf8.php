<?php  
/**
 * A port of phputf8 to a unified file/class. Checks PHP status to ensure that
 * UTF-8 support is available and normalize global variables to UTF-8. It also
 * provides multi-byte aware replacement string functions.
 *
 * This file is licensed differently from the rest of Kohana. As a port of
 * phputf8, which is LGPL software, this file is released under the LGPL.
 *
 * PCRE needs to be compiled with UTF-8 support (--enable-utf8).
 * Support for Unicode properties is highly recommended (--enable-unicode-properties).
 * @see http://php.net/manual/reference.pcre.pattern.modifiers.php
 *
 * UTF-8 conversion will be much more reliable if the iconv extension is loaded.
 * @see http://php.net/iconv
 *
 * The mbstring extension is highly recommended, but must not be overloading
 * string functions.
 * @see http://php.net/mbstring
 *
 * $Id: utf8.php 3769 2008-12-15 00:48:56Z zombor $
 * 
 * Modified to use with Zend framework 2009-03-13 ddv
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007 Kohana Team
 * @copyright  (c) 2005 Harry Fuecks
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt
 */


final class App_Utf8 {
    
    public static $RU_ALPHABET = array(
        '1'  => 'а', '2'  => 'б', '3'  => 'в', '4'  => 'г', '5'  => 'д', '6'  => 'е', 
        '7'  => 'ё', '8'  => 'ж', '9'  => 'з', '10' => 'и', '11' => 'й', '12' => 'к', 
        '13' => 'л', '14' => 'м', '15' => 'н', '16' => 'о', '17' => 'п', '18' => 'р', 
        '19' => 'с', '20' => 'т', '21' => 'у', '22' => 'ф', '23' => 'х', '24' => 'ц', 
        '25' => 'ч', '26' => 'ш', '27' => 'щ', '28' => 'ъ', '29' => 'ы', '30' => 'ь', 
        '31' => 'э', '32' => 'ю', '33' => 'я', 
    );
    
    public static $TO_ASCII_UPPER = array(
        'À' => 'A',  'Ô' => 'O',  'Ď' => 'D',  'Ḟ' => 'F',  
        'Ë' => 'E',  'Š' => 'S',  'Ơ' => 'O',
        'Ă' => 'A',  'Ř' => 'R',  'Ț' => 'T',  'Ň' => 'N',  
        'Ā' => 'A',  'Ķ' => 'K',  'Ĕ' => 'E',
        'Ŝ' => 'S',  'Ỳ' => 'Y',  'Ņ' => 'N',  'Ĺ' => 'L',  
        'Ħ' => 'H',  'Ṗ' => 'P',  'Ó' => 'O',
        'Ú' => 'U',  'Ě' => 'E',  'É' => 'E',  'Ç' => 'C',  
        'Ẁ' => 'W',  'Ċ' => 'C',  'Õ' => 'O',
        'Ṡ' => 'S',  'Ø' => 'O',  'Ģ' => 'G',  'Ŧ' => 'T',  
        'Ș' => 'S',  'Ė' => 'E',  'Ĉ' => 'C',
        'Ś' => 'S',  'Î' => 'I',  'Ű' => 'U',  'Ć' => 'C',  
        'Ę' => 'E',  'Ŵ' => 'W',  'Ṫ' => 'T',
        'Ū' => 'U',  'Č' => 'C',  'Ö' => 'O',  'È' => 'E',  
        'Ŷ' => 'Y',  'Ą' => 'A',  'Ł' => 'L',
        'Ų' => 'U',  'Ů' => 'U',  'Ş' => 'S',  'Ğ' => 'G',  
        'Ļ' => 'L',  'Ƒ' => 'F',  'Ž' => 'Z',
        'Ẃ' => 'W',  'Ḃ' => 'B',  'Å' => 'A',  'Ì' => 'I',  
        'Ï' => 'I',  'Ḋ' => 'D',  'Ť' => 'T',
        'Ŗ' => 'R',  'Ä' => 'A',  'Í' => 'I',  'Ŕ' => 'R',  
        'Ê' => 'E',  'Ü' => 'U',  'Ò' => 'O',
        'Ē' => 'E',  'Ñ' => 'N',  'Ń' => 'N',  'Ĥ' => 'H',  
        'Ĝ' => 'G',  'Đ' => 'D',  'Ĵ' => 'J',
        'Ÿ' => 'Y',  'Ũ' => 'U',  'Ŭ' => 'U',  'Ư' => 'U',  
        'Ţ' => 'T',  'Ý' => 'Y',  'Ő' => 'O',
        'Â' => 'A',  'Ľ' => 'L',  'Ẅ' => 'W',  'Ż' => 'Z',  
        'Ī' => 'I',  'Ã' => 'A',  'Ġ' => 'G',
        'Ṁ' => 'M',  'Ō' => 'O',  'Ĩ' => 'I',  'Ù' => 'U',  
        'Į' => 'I',  'Ź' => 'Z',  'Á' => 'A',
        'Û' => 'U',  'Þ' => 'Th', 'Ð' => 'Dh', 'Æ' => 'Ae',
        
        /**
         * russian+ukrainian 
         */
         'КС' => 'X', 
         'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 
         'Ё' => 'E', 'Ж' => 'ZH', 'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 
         'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O', 'П' => 'p', 'Р' => 'R', 
         'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C', 
         'Ч' => 'CH', 'Ш' => 'SH', 'Щ' => 'SCH', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => "'", 
         'Э' => 'E', 'Ю' => 'JU', 'Я' => 'JA', 'Ї' => 'JI', 'Є' => 'IE',   'І' => 'I',
    );

    public static $TO_ASCII_LOWER = array(
        'à' => 'a',  'ô' => 'o',  'ď' => 'd',  'ḟ' => 'f',  
        'ë' => 'e',  'š' => 's',  'ơ' => 'o',
        'ß' => 'ss', 'ă' => 'a',  'ř' => 'r',  'ț' => 't',  
        'ň' => 'n',  'ā' => 'a',  'ķ' => 'k',
        'ŝ' => 's',  'ỳ' => 'y',  'ņ' => 'n',  'ĺ' => 'l',  
        'ħ' => 'h',  'ṗ' => 'p',  'ó' => 'o',
        'ú' => 'u',  'ě' => 'e',  'é' => 'e',  'ç' => 'c',  
        'ẁ' => 'w',  'ċ' => 'c',  'õ' => 'o',
        'ṡ' => 's',  'ø' => 'o',  'ģ' => 'g',  'ŧ' => 't',  
        'ș' => 's',  'ė' => 'e',  'ĉ' => 'c',
        'ś' => 's',  'î' => 'i',  'ű' => 'u',  'ć' => 'c',  
        'ę' => 'e',  'ŵ' => 'w',  'ṫ' => 't',
        'ū' => 'u',  'č' => 'c',  'ö' => 'o',  'è' => 'e',  
        'ŷ' => 'y',  'ą' => 'a',  'ł' => 'l',
        'ų' => 'u',  'ů' => 'u',  'ş' => 's',  'ğ' => 'g',  
        'ļ' => 'l',  'ƒ' => 'f',  'ž' => 'z',
        'ẃ' => 'w',  'ḃ' => 'b',  'å' => 'a',  'ì' => 'i',  
        'ï' => 'i',  'ḋ' => 'd',  'ť' => 't',
        'ŗ' => 'r',  'ä' => 'a',  'í' => 'i',  'ŕ' => 'r',  
        'ê' => 'e',  'ü' => 'u',  'ò' => 'o',
        'ē' => 'e',  'ñ' => 'n',  'ń' => 'n',  'ĥ' => 'h',  
        'ĝ' => 'g',  'đ' => 'd',  'ĵ' => 'j',
        'ÿ' => 'y',  'ũ' => 'u',  'ŭ' => 'u',  'ư' => 'u',  
        'ţ' => 't',  'ý' => 'y',  'ő' => 'o',
        'â' => 'a',  'ľ' => 'l',  'ẅ' => 'w',  'ż' => 'z',  
        'ī' => 'i',  'ã' => 'a',  'ġ' => 'g',
        'ṁ' => 'm',  'ō' => 'o',  'ĩ' => 'i',  'ù' => 'u',  
        'į' => 'i',  'ź' => 'z',  'á' => 'a',
        'û' => 'u',  'þ' => 'th', 'ð' => 'dh', 'æ' => 'ae', 
        'µ' => 'u',  'ĕ' => 'e',

        /**
         * russian+ukrainian 
         */

         'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 
         'ё' => 'e', 'ж' => 'zh', 'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 
         'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r', 
         'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 
         'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ъ' => '', 'ы' => 'y', 'ь' => "'", 
         'э' => 'e', 'ю' => 'ju', 'я' => 'ja', 'і' => 'i','ї' => 'ji', 'є' => 'ie',
         'кс' => 'x', 
    );

    /**
     * should be called while starting application
     * (as early as possible)
     */
    public static function init()
    {
        if ( ! preg_match('/^.$/u', 'ñ'))
        {
            trigger_error
            (
                '<a href="http://php.net/pcre">PCRE</a> has not been compiled with UTF-8 support. '.
                'See <a href="http://php.net/manual/reference.pcre.pattern.modifiers.php">PCRE Pattern Modifiers</a> '.
                'for more information. This application cannot be run without UTF-8 support.',
                E_USER_ERROR
            );
        }
        
        if ( ! extension_loaded('iconv'))
        {
            trigger_error
            (
                'The <a href="http://php.net/iconv">iconv</a> extension is not loaded. '.
                'Without iconv, strings cannot be properly translated to UTF-8 from user input. '.
                'This application cannot be run without UTF-8 support.',
                E_USER_ERROR
            );
        }
        
        if (extension_loaded('mbstring') AND (ini_get('mbstring.func_overload') & MB_OVERLOAD_STRING))
        {
            trigger_error
            (
                'The <a href="http://php.net/mbstring">mbstring</a> extension is overloading PHP\'s native string functions. '.
                'Disable this by setting mbstring.func_overload to 0, 1, 4 or 5 in php.ini or a .htaccess file.'.
                'This application cannot be run without UTF-8 support.',
                E_USER_ERROR
            );
        }
        
        // Check PCRE support for Unicode properties such as \p and \X.
        $ER = error_reporting(0);
        define('PCRE_UNICODE_PROPERTIES', (bool) preg_match('/^\pL$/u', 'ñ'));
        error_reporting($ER);
        
        // SERVER_UTF8 ? use mb_* functions : use non-native functions
        if (extension_loaded('mbstring'))
        {
            mb_internal_encoding('UTF-8');
            define('SERVER_UTF8', TRUE);
        }
        else
        {
            define('SERVER_UTF8', FALSE);
        }
        

        // Convert all global variables to UTF-8.
        $_GET    = self::clean($_GET);
        $_POST   = self::clean($_POST);
        $_COOKIE = self::clean($_COOKIE);
        $_SERVER = self::clean($_SERVER);
    }
    
    
    protected static function _prepare($func)
    {
        /*
        if ( ! isset(self::$called[$func]))
        {
        */
            require_once dirname(__FILE__).'/Utf8/'.$func.'.php';

        /*
            // Function has been called
            self::$called[$func] = TRUE;
        }
        */
        
    }
    
    
    public static function urlClean($str, $noTranslit = NULL)
    {
        $str = self::clean($str);

        $str = preg_replace('/\s/', '-', $str);

        if ( ! $noTranslit) {
            $str = self::transliterate_to_ascii($str);
            $str = preg_replace('/[^a-zA-Z0-9\-\.\_]/', '', $str);
        }
        else {
            $str = preg_replace('/[\/\\\]\[\}\{\)\(\=\+\*\&\^\%\$\#\@\!\`\~\,\?\"\'\'\:\;\|\<\>]/', '', $str);
        }
        $str = preg_replace('/\-+/', '-', $str);
        $str = preg_replace('/\.+/', '.', $str);
        $str = strtolower($str);
        return $str;
    }

	/**
	 * Recursively cleans arrays, objects, and strings. Removes ASCII control
	 * codes and converts to UTF-8 while silently discarding incompatible
	 * UTF-8 characters.
	 *
	 * @param   string  string to clean
	 * @return  string
	 */
	public static function clean($str)
	{
		if (is_array($str) OR is_object($str))
		{
			foreach ($str as $key => $val)
			{
				// Recursion!
				$str[self::clean($key)] = self::clean($val);
			}
		}
		elseif (is_string($str) AND $str !== '')
		{
			// Remove control characters
			$str = self::strip_ascii_ctrl($str);

			if ( ! self::is_ascii($str))
			{
				// Disable notices
				$ER = error_reporting(~E_NOTICE);

				// iconv is expensive, so it is only used when needed
				$str = iconv('UTF-8', 'UTF-8//IGNORE', $str);

				// Turn notices back on
				error_reporting($ER);
			}
		}

		return $str;
	}

	/**
	 * Tests whether a string contains only 7bit ASCII bytes. This is used to
	 * determine when to use native functions or UTF-8 functions.
	 *
	 * @param   string  string to check
	 * @return  bool
	 */
	public static function is_ascii($str)
	{
		return ! preg_match('/[^\x00-\x7F]/S', $str);
	}

	/**
	 * Strips out device control codes in the ASCII range.
	 *
	 * @param   string  string to clean
	 * @return  string
	 */
	public static function strip_ascii_ctrl($str)
	{
		return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $str);
	}

	/**
	 * Strips out all non-7bit ASCII bytes.
	 *
	 * @param   string  string to clean
	 * @return  string
	 */
	public static function strip_non_ascii($str)
	{
		return preg_replace('/[^\x00-\x7F]+/S', '', $str);
	}

	/**
	 * Replaces special/accented UTF-8 characters by ASCII-7 'equivalents'.
	 *
	 * @author  Andreas Gohr <andi@splitbrain.org>
	 *
	 * @param   string   string to transliterate
	 * @param   integer  -1 lowercase only, +1 uppercase only, 0 both cases
	 * @return  string
	 */
	public static function transliterate_to_ascii($str, $case = 0)
	{
        self::_prepare(__FUNCTION__);
		return _transliterate_to_ascii($str, $case);
	}


    /**
     * Replaces ASCII-7 'equivalents' by UTF-8 characters.
     *
     * @author  Andreas Gohr <andi@splitbrain.org>
     *
     * @param   string   string to transliterate
     * @param   integer  -1 lowercase only, +1 uppercase only, 0 both cases
     * @return  string
     */
    public static function untransliterate_from_ascii($str, $case = 0)
    {
        self::_prepare(__FUNCTION__);
        return _untransliterate_from_ascii($str, $case);
    }



	/**
	 * Returns the length of the given string.
	 * @see http://php.net/strlen
	 *
	 * @param   string   string being measured for length
	 * @return  integer
	 */
	public static function strlen($str)
	{
        self::_prepare(__FUNCTION__);
		return _strlen($str);
	}

	/**
	 * Finds position of first occurrence of a UTF-8 string.
	 * @see http://php.net/strlen
	 *
	 * @author  Harry Fuecks <hfuecks@gmail.com>
	 *
	 * @param   string   haystack
	 * @param   string   needle
	 * @param   integer  offset from which character in haystack to start searching
	 * @return  integer  position of needle
	 * @return  boolean  FALSE if the needle is not found
	 */
	public static function strpos($str, $search, $offset = 0)
	{
        self::_prepare(__FUNCTION__);
		return _strpos($str, $search, $offset);
	}

	/**
	 * Finds position of last occurrence of a char in a UTF-8 string.
	 * @see http://php.net/strrpos
	 *
	 * @author  Harry Fuecks <hfuecks@gmail.com>
	 *
	 * @param   string   haystack
	 * @param   string   needle
	 * @param   integer  offset from which character in haystack to start searching
	 * @return  integer  position of needle
	 * @return  boolean  FALSE if the needle is not found
	 */
	public static function strrpos($str, $search, $offset = 0)
	{
        self::_prepare(__FUNCTION__);
		return _strrpos($str, $search, $offset);
	}

	/**
	 * Returns part of a UTF-8 string.
	 * @see http://php.net/substr
	 *
	 * @author  Chris Smith <chris@jalakai.co.uk>
	 *
	 * @param   string   input string
	 * @param   integer  offset
	 * @param   integer  length limit
	 * @return  string
	 */
	public static function substr($str, $offset, $length = NULL)
	{
        self::_prepare(__FUNCTION__);
		return _substr($str, $offset, $length);
	}

	/**
	 * Replaces text within a portion of a UTF-8 string.
	 * @see http://php.net/substr_replace
	 *
	 * @author  Harry Fuecks <hfuecks@gmail.com>
	 *
	 * @param   string   input string
	 * @param   string   replacement string
	 * @param   integer  offset
	 * @return  string
	 */
	public static function substr_replace($str, $replacement, $offset, $length = NULL)
	{
        self::_prepare(__FUNCTION__);
		return _substr_replace($str, $replacement, $offset, $length);
	}

	/**
	 * Makes a UTF-8 string lowercase.
	 * @see http://php.net/strtolower
	 *
	 * @author  Andreas Gohr <andi@splitbrain.org>
	 *
	 * @param   string   mixed case string
	 * @return  string
	 */
	public static function strtolower($str)
	{
        self::_prepare(__FUNCTION__);
		return _strtolower($str);
	}

	/**
	 * Makes a UTF-8 string uppercase.
	 * @see http://php.net/strtoupper
	 *
	 * @author  Andreas Gohr <andi@splitbrain.org>
	 *
	 * @param   string   mixed case string
	 * @return  string
	 */
	public static function strtoupper($str)
	{
        self::_prepare(__FUNCTION__);
		return _strtoupper($str);
	}

	/**
	 * Makes a UTF-8 string's first character uppercase.
	 * @see http://php.net/ucfirst
	 *
	 * @author  Harry Fuecks <hfuecks@gmail.com>
	 *
	 * @param   string   mixed case string
	 * @return  string
	 */
	public static function ucfirst($str)
	{
        self::_prepare(__FUNCTION__);
		return _ucfirst($str);
	}

	/**
	 * Makes the first character of every word in a UTF-8 string uppercase.
	 * @see http://php.net/ucwords
	 *
	 * @author  Harry Fuecks <hfuecks@gmail.com>
	 *
	 * @param   string   mixed case string
	 * @return  string
	 */
	public static function ucwords($str)
	{
        self::_prepare(__FUNCTION__);
		return _ucwords($str);
	}

	/**
	 * Case-insensitive UTF-8 string comparison.
	 * @see http://php.net/strcasecmp
	 *
	 * @author  Harry Fuecks <hfuecks@gmail.com>
	 *
	 * @param   string   string to compare
	 * @param   string   string to compare
	 * @return  integer  less than 0 if str1 is less than str2
	 * @return  integer  greater than 0 if str1 is greater than str2
	 * @return  integer  0 if they are equal
	 */
	public static function strcasecmp($str1, $str2)
	{
        self::_prepare(__FUNCTION__);
		return _strcasecmp($str1, $str2);
	}

	/**
	 * Returns a string or an array with all occurrences of search in subject (ignoring case).
	 * replaced with the given replace value.
	 * @see     http://php.net/str_ireplace
	 *
	 * @note    It's not fast and gets slower if $search and/or $replace are arrays.
	 * @author  Harry Fuecks <hfuecks@gmail.com
	 *
	 * @param   string|array  text to replace
	 * @param   string|array  replacement text
	 * @param   string|array  subject text
	 * @param   integer       number of matched and replaced needles will be returned via this parameter which is passed by reference
	 * @return  string        if the input was a string
	 * @return  array         if the input was an array
	 */
	public static function str_ireplace($search, $replace, $str, & $count = NULL)
	{
        self::_prepare(__FUNCTION__);
		return _str_ireplace($search, $replace, $str, $count);
	}

	/**
	 * Case-insenstive UTF-8 version of strstr. Returns all of input string
	 * from the first occurrence of needle to the end.
	 * @see http://php.net/stristr
	 *
	 * @author Harry Fuecks <hfuecks@gmail.com>
	 *
	 * @param   string   input string
	 * @param   string   needle
	 * @return  string   matched substring if found
	 * @return  boolean  FALSE if the substring was not found
	 */
	public static function stristr($str, $search)
	{
        self::_prepare(__FUNCTION__);
		return _stristr($str, $search);
	}

	/**
	 * Finds the length of the initial segment matching mask.
	 * @see http://php.net/strspn
	 *
	 * @author Harry Fuecks <hfuecks@gmail.com>
	 *
	 * @param   string   input string
	 * @param   string   mask for search
	 * @param   integer  start position of the string to examine
	 * @param   integer  length of the string to examine
	 * @return  integer  length of the initial segment that contains characters in the mask
	 */
	public static function strspn($str, $mask, $offset = NULL, $length = NULL)
	{
        self::_prepare(__FUNCTION__);
		return _strspn($str, $mask, $offset, $length);
	}

	/**
	 * Finds the length of the initial segment not matching mask.
	 * @see http://php.net/strcspn
	 *
	 * @author  Harry Fuecks <hfuecks@gmail.com>
	 *
	 * @param   string   input string
	 * @param   string   mask for search
	 * @param   integer  start position of the string to examine
	 * @param   integer  length of the string to examine
	 * @return  integer  length of the initial segment that contains characters not in the mask
	 */
	public static function strcspn($str, $mask, $offset = NULL, $length = NULL)
	{
        self::_prepare(__FUNCTION__);
		return _strcspn($str, $mask, $offset, $length);
	}

	/**
	 * Pads a UTF-8 string to a certain length with another string.
	 * @see http://php.net/str_pad
	 *
	 * @author  Harry Fuecks <hfuecks@gmail.com>
	 *
	 * @param   string   input string
	 * @param   integer  desired string length after padding
	 * @param   string   string to use as padding
	 * @param   string   padding type: STR_PAD_RIGHT, STR_PAD_LEFT, or STR_PAD_BOTH
	 * @return  string
	 */
	public static function str_pad($str, $final_str_length, $pad_str = ' ', $pad_type = STR_PAD_RIGHT)
	{
        self::_prepare(__FUNCTION__);
		return _str_pad($str, $final_str_length, $pad_str, $pad_type);
	}

	/**
	 * Converts a UTF-8 string to an array.
	 * @see http://php.net/str_split
	 *
	 * @author  Harry Fuecks <hfuecks@gmail.com>
	 *
	 * @param   string   input string
	 * @param   integer  maximum length of each chunk
	 * @return  array
	 */
	public static function str_split($str, $split_length = 1)
	{
        self::_prepare(__FUNCTION__);
		return _str_split($str, $split_length);
	}

	/**
	 * Reverses a UTF-8 string.
	 * @see http://php.net/strrev
	 *
	 * @author  Harry Fuecks <hfuecks@gmail.com>
	 *
	 * @param   string   string to be reversed
	 * @return  string
	 */
	public static function strrev($str)
	{
        self::_prepare(__FUNCTION__);
		return _strrev($str);
	}

	/**
	 * Strips whitespace (or other UTF-8 characters) from the beginning and
	 * end of a string.
	 * @see http://php.net/trim
	 *
	 * @author  Andreas Gohr <andi@splitbrain.org>
	 *
	 * @param   string   input string
	 * @param   string   string of characters to remove
	 * @return  string
	 */
	public static function trim($str, $charlist = NULL)
	{
        self::_prepare(__FUNCTION__);
		return _trim($str, $charlist);
	}

	/**
	 * Strips whitespace (or other UTF-8 characters) from the beginning of a string.
	 * @see http://php.net/ltrim
	 *
	 * @author  Andreas Gohr <andi@splitbrain.org>
	 *
	 * @param   string   input string
	 * @param   string   string of characters to remove
	 * @return  string
	 */
	public static function ltrim($str, $charlist = NULL)
	{
        self::_prepare(__FUNCTION__);
		return _ltrim($str, $charlist);
	}

	/**
	 * Strips whitespace (or other UTF-8 characters) from the end of a string.
	 * @see http://php.net/rtrim
	 *
	 * @author  Andreas Gohr <andi@splitbrain.org>
	 *
	 * @param   string   input string
	 * @param   string   string of characters to remove
	 * @return  string
	 */
	public static function rtrim($str, $charlist = NULL)
	{
        self::_prepare(__FUNCTION__);
		return _rtrim($str, $charlist);
	}

	/**
	 * Returns the unicode ordinal for a character.
	 * @see http://php.net/ord
	 *
	 * @author Harry Fuecks <hfuecks@gmail.com>
	 *
	 * @param   string   UTF-8 encoded character
	 * @return  integer
	 */
	public static function ord($chr)
	{
        self::_prepare(__FUNCTION__);
		return _ord($chr);
	}

	/**
	 * Takes an UTF-8 string and returns an array of ints representing the Unicode characters.
	 * Astral planes are supported i.e. the ints in the output can be > 0xFFFF.
	 * Occurrances of the BOM are ignored. Surrogates are not allowed.
	 *
	 * The Original Code is Mozilla Communicator client code.
	 * The Initial Developer of the Original Code is Netscape Communications Corporation.
	 * Portions created by the Initial Developer are Copyright (C) 1998 the Initial Developer.
	 * Ported to PHP by Henri Sivonen <hsivonen@iki.fi>, see http://hsivonen.iki.fi/php-utf8/.
	 * Slight modifications to fit with phputf8 library by Harry Fuecks <hfuecks@gmail.com>.
	 *
	 * @param   string   UTF-8 encoded string
	 * @return  array    unicode code points
	 * @return  boolean  FALSE if the string is invalid
	 */
	public static function to_unicode($str)
	{
        self::_prepare(__FUNCTION__);
		return _to_unicode($str);
	}

	/**
	 * Takes an array of ints representing the Unicode characters and returns a UTF-8 string.
	 * Astral planes are supported i.e. the ints in the input can be > 0xFFFF.
	 * Occurrances of the BOM are ignored. Surrogates are not allowed.
	 *
	 * The Original Code is Mozilla Communicator client code.
	 * The Initial Developer of the Original Code is Netscape Communications Corporation.
	 * Portions created by the Initial Developer are Copyright (C) 1998 the Initial Developer.
	 * Ported to PHP by Henri Sivonen <hsivonen@iki.fi>, see http://hsivonen.iki.fi/php-utf8/.
	 * Slight modifications to fit with phputf8 library by Harry Fuecks <hfuecks@gmail.com>.
	 *
	 * @param   array    unicode code points representing a string
	 * @return  string   utf8 string of characters
	 * @return  boolean  FALSE if a code point cannot be found
	 */
	public static function from_unicode($arr)
	{
        self::_prepare(__FUNCTION__);
		return _from_unicode($arr);
	}
    
    
    public static function normalize($str)
    {
        $norma = '';
        $norma = App_Utf8::clean($str);
        $norma = App_Utf8::strtolower($norma);
        $norma = preg_replace('/[\ \|\'\"\.\,\/\-\\\;\:\*\?\!\@\~\$\%\^\(\)\_\=\+\<\>\`\~\n\r]/', '', $norma);
        return $norma;
    }

} // End utf8