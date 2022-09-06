<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Utils {

	public static function prettyPrint( $json, $header, $options = array() ) {
		//$json = json_encode($json , JSON_PRETTY_PRINT);
		$json = json_encode($json , JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
		//JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES

		$result = '';
		$level = 0;
		$in_quotes = false;
		$in_escape = false;
		$ends_line_level = NULL;
		$json_length = strlen( $json );

		for( $i = 0; $i < $json_length; $i++ ) {
		    $char = $json[$i];
		    $new_line_level = NULL;
		    $post = "";
		    if( $ends_line_level !== NULL ) {
		        $new_line_level = $ends_line_level;
		        $ends_line_level = NULL;
		    }
		    if ( $in_escape ) {
		        $in_escape = false;
		    } else if( $char === '"' ) {
		        $in_quotes = !$in_quotes;
		    } else if( ! $in_quotes ) {
		        switch( $char ) {
		            case '}': case ']':
		                $level--;
		                $ends_line_level = NULL;
		                $new_line_level = $level;
		                break;

		            case '{': case '[':
		                $level++;
		            case ',':
		                $ends_line_level = $level;
		                break;

		            case ':':
		                $post = " ";
		                break;

		            case " ": case "\t": case "\n": case "\r":
		                $char = "";
		                $ends_line_level = $new_line_level;
		                $new_line_level = NULL;
		                break;
		        }
		    } else if ( $char === '\\' ) {
		        $in_escape = true;
		    }
		    if( $new_line_level !== NULL ) {
		        $result .= "\n".str_repeat( "\t", $new_line_level );
		    }
		    $result .= $char.$post;
		}

		if ($options['htmlspecialchars'] === true) $result = htmlspecialchars($result);
		$result = preg_replace("/\n/", '<br>', $result);
		$result = preg_replace("/\t/", '&nbsp;&nbsp;&nbsp;', $result);
		$result = str_replace('\\/', '\\',  $result);
		$result = '<code>'.$result.'</code>';
		if (isset( $header )) $result = '<h3>'.$header.'</h3>'.$result;
		//$result = html_entity_decode($result);
		return $result;
	} // prettyPrint

	/**********************************************************************
	  config render function
	**********************************************************************/
	
  	public static function convert_config( $str, $date_from = null, $date_to = null) {
		if ($date_from != null) $str = str_replace('@date_from', $date_from, $str);
		if ($date_to != null) $str = str_replace('@date_to', $date_to, $str);
		return $str;
	} // convert_config
	
	public static function render_config( $config, $date_from = null, $date_to = null) {
		foreach ($config as $key => $value) {
			if ( gettype($value) == 'string' ) 
				$config [ $key ] = self::convert_config($config [ $key ], $date_from,  $date_to);
		} // foreach
		return $config;
	} // render_config

} // Utils