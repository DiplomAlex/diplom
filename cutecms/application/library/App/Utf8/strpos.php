<?php  
/**
 * App_Utf8::strpos
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007 Kohana Team
 * @copyright  (c) 2005 Harry Fuecks
 * @license    http://www.gnu.org/licenses/old-licenses/lgpl-2.1.txt
 */
function _strpos($str, $search, $offset = 0)
{
	$offset = (int) $offset;

	if (SERVER_UTF8)
		return mb_strpos($str, $search, $offset);

	if (App_Utf8::is_ascii($str) AND App_Utf8::is_ascii($search))
		return strpos($str, $search, $offset);

	if ($offset == 0)
	{
		$array = explode($search, $str, 2);
		return isset($array[1]) ? App_Utf8::strlen($array[0]) : FALSE;
	}

	$str = App_Utf8::substr($str, $offset);
	$pos = App_Utf8::strpos($str, $search);
	return ($pos === FALSE) ? FALSE : $pos + $offset;
}