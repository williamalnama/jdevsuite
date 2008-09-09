<?php
defined('_JEXEC') or die('Restricted access');
function JText($str)
{
	return JText::_($str);	
}

function pick()
{
	$args = func_get_args();	
	foreach($args as $arg)
		if (!is_null($arg))
			return $arg;
}

function urlFor($url=array())
{		
	if (is_array($url)) {

		global $option;
		
		$url['option'] = pick(@$url['option'],$option);
		$url['controller'] = pick(@$url['controller'],JRequest::getVar('controller'));		
				
		foreach($url as $k=>$v)
			$segments[]  = "{$k}={$v}";
			
		$query = 'index.php?'.implode("&",$segments);
		
	} else 
	
		$query = $url;
		
	return JRoute::_($query,false);
	
}
function pretifyXML($xml,$level=4)
{
	
      $xml = explode("\n", preg_replace('/>\s*</', ">\n<", $xml->asXML()));

        // hold current indentation level
        $indent = 0;

        // hold the XML segments
        $pretty = array();

        // shift off opening XML tag if present
        if (count($xml) && preg_match('/^<\?\s*xml/', $xml[0])) {
            $pretty[] = array_shift($xml);
        }

        foreach ($xml as $el) {
            if (preg_match('/^<([\w])+[^>\/]*>$/U', $el)) {
                // opening tag, increase indent
                $pretty[] = str_repeat(' ', $indent) . $el;
                $indent += $level;
            } else {
                if (preg_match('/^<\/.+>$/', $el)) {
                    // closing tag, decrease indent
                    $indent -= $level;
                }
                $pretty[] = str_repeat(' ', $indent) . $el;
            }
        }

        return implode("\n", $pretty);	
}
function copyr($source, $dest)
{
    // Simple copy for a file
    if (is_file($source)) {
        return copy($source, $dest);
    }
 
    // Make destination directory
    if (!is_dir($dest)) {
        mkdir($dest);
    }
    
    // If the source is a symlink
    if (is_link($source)) {
        $link_dest = readlink($source);
        return symlink($link_dest, $dest);
    }
 
    // Loop through the folder
    $dir = dir($source);
    while (false !== $entry = $dir->read()) {
        // Skip pointers
        if ($entry == '.' || $entry == '..') {
            continue;
        }
 
        // Deep copy directories
        if ($dest !== "$source/$entry") {
            copyr("$source/$entry", "$dest/$entry");
        }
    }
 
    // Clean up
    $dir->close();
    return true;
}
function str_classifiy($string)
{
	$string = preg_replace('/_/',' ',$string);
	return str_replace(' ','',str_titleCase($string));	
}
function  str_titleCase($string)  
{ 
		$string = preg_replace('/_/',' ',$string);
	    $len=strlen($string); 
        $i=0; 
        $last= ""; 
        $new= ""; 
        $string=strtoupper($string); 
        while  ($i<$len): 
                $char=substr($string,$i,1); 
                if  (ereg( "[A-Z]",$last)): 
                        $new.=strtolower($char); 
                else: 
                        $new.=strtoupper($char); 
                endif; 
                $last=$char; 
                $i++; 
        endwhile; 
        return($new); 
}; 