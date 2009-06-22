<?php

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
                $pretty[] = str_repeat(' ', abs($indent)) . $el;
                $indent += $level;
            } else {
                if (preg_match('/^<\/.+>$/', $el)) {
                    // closing tag, decrease indent
                    $indent -= $level;
                }

                $pretty[] = str_repeat(' ', abs($indent)) . $el;
            }
        }

        return implode("\n", $pretty);	
}
function copyr($source, $dest,$exclude=null)
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
		
 		if ( $exclude && preg_match($exclude,$entry) ) {
 			continue;	
		}

        // Deep copy directories
        if ($dest !== "$source/$entry") {
            copyr("$source/$entry", "$dest/$entry",$exclude);
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