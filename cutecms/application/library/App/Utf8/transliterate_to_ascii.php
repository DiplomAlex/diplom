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
function _transliterate_to_ascii($str, $case = 0)
{
	static $UTF8_LOWER_ACCENTS = NULL;
	static $UTF8_UPPER_ACCENTS = NULL;

	if ($case <= 0)
	{
		if ($UTF8_LOWER_ACCENTS === NULL)
		{
			$UTF8_LOWER_ACCENTS = App_Utf8::$TO_ASCII_LOWER;
		}

		$str = str_replace(
			array_keys($UTF8_LOWER_ACCENTS),
			array_values($UTF8_LOWER_ACCENTS),
			$str
		);
	}

	if ($case >= 0)
	{
		if ($UTF8_UPPER_ACCENTS === NULL)
		{
            $UTF8_UPPER_ACCENTS = App_Utf8::$TO_ASCII_UPPER;			
		}

		$str = str_replace(
			array_keys($UTF8_UPPER_ACCENTS),
			array_values($UTF8_UPPER_ACCENTS),
			$str
		);
	}

	return $str;
}