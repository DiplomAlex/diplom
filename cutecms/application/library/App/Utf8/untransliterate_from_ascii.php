<?php  
/**
 * App_Utf8::transliterate_to_ascii
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007 Kohana Team
 * @copyright  (c) 2005 Harry Fuecks
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt
 */
function _untransliterate_from_ascii($str, $case = 0)
{
	static $UTF8_LOWER_DESCENTS = NULL;
	static $UTF8_UPPER_DESCENTS = NULL;

    if (is_numeric($str)) {
        $langCode = App_Model::factory('languages')->getDefaultCode();
        $arr = App_Utf8::${strtoupper($langCode) . '_ALPHABET'};
        return $arr[$str];
    }

	if ($case <= 0)
	{
		if ($UTF8_LOWER_DESCENTS === NULL)
		{
			$UTF8_LOWER_DESCENTS = array_flip(App_Utf8::$TO_ASCII_LOWER);
		}

		$str = str_replace(
			array_keys($UTF8_LOWER_DESCENTS),
			array_values($UTF8_LOWER_DESCENTS),
			$str
		);
	}

	if ($case >= 0)
	{
		if ($UTF8_UPPER_DESCENTS === NULL)
		{
            $UTF8_UPPER_DESCENTS = array_flip(App_Utf8::$TO_ASCII_UPPER);			
		}

		$str = str_replace(
			array_keys($UTF8_UPPER_DESCENTS),
			array_values($UTF8_UPPER_DESCENTS),
			$str
		);
	}

	return $str;
}