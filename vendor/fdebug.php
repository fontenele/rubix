<?php
error_reporting(E_ALL);
ini_set('display_errors', true);
function x( $args )
{
	$args = func_get_args( );
	$dbt = debug_backtrace( );

	$line   = $dbt[ 0 ][ 'line' ];
	$file 	= $dbt[ 0 ][ 'file' ];
	$funct	= $dbt[ 0 ][ 'function' ];

	$string = "<div style='display : table; width : 99%; background : khaki;' align='left' style='text-align: left;'><span style='display : table; padding : 3px; width : 100%; background : gold; color : black;' align='center'><b>File:</b> {$file}<br><b>Function:</b> {$funct}( )<br><b>Line:</b> {$line}</span>";
	$string.= "<pre style='padding-left: 2px;'>";

	foreach( $args as $idx => $arg )
	{
		$string.= "<span style='font-size : 10pt; font-style : italic; display : table-cell; border : 1px solid steelblue;'>&nbsp;#<b>{$idx}</b>&nbsp;</span><br>";
		$string.= print_r( $arg, true );
		$string.= "<br><br>";
	}

	$string.= "</pre></div>";

	echo $string;

}

function xd( $args )
{
	$args 	= func_get_args( );
	$dbt 	= debug_backtrace( );

	$line   = $dbt[ 0 ][ 'line' ];
	$file 	= $dbt[ 0 ][ 'file' ];
	$funct	= $dbt[ 0 ][ 'function' ];

	$string = "<div style='display : table; width : 99%; background : lightgray;' align='left'><span style='display : table; padding : 3px; width : 100%; background : gray; color : white;' align='center'><b>File:</b> {$file}<br><b>Function:</b> {$funct}( )<br><b>Line:</b> {$line}</span>";
	$string.= "<pre style='padding-left: 2px;'>";

	foreach( $args as $idx => $arg )
	{
		//if( ( $args[ 0 ] === 'debug' && $idx > 0 ) || $args[ 0 ] != 'debug' )
		//{
			$string.= "<span style='font-size : 10pt; font-style : italic; display : table-cell; border : 1px solid steelblue;'>&nbsp;#<b>{$idx}</b>&nbsp;</span><br>";
			$string.= print_r( $arg, true );
			$string.= "<br><br>";
		//}
	}

	$string.= "</pre></div>";

	/*if( $args[ 0 ] === 'debug' )
	{
		USession::addSysValue( "arPopup", $string );
		echo "<script>i=window.open('popup.php?tpl=Infra/debug.tpl','debugWindow', 'fullscreen=yes,menubar=yes,scrollbars=yes');</script>";
		die( "Please wait while debug window is loading..." );
	}
	else*/ die( $string );
}

?>
