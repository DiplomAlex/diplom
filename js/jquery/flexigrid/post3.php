<?

header("Expires: Mon, 26 Jul 1997 05:00:00 GMT" ); 
header("Last-Modified: " . gmdate( "D, d M Y H:i:s" ) . "GMT" ); 
header("Cache-Control: no-cache, must-revalidate" ); 
header("Pragma: no-cache" );
//header("Content-type: text/x-json");
$json = "";
$json .= "{\n";
$json .= "page: 1,\n";
$json .= "total: 0,\n";
$json .= "rows: [";

//$json .= "{id:'iso12312',cell:['123', '321', '213', '231', '312']}";

$json .= "]\n";
$json .= "}";
echo $json;
?>