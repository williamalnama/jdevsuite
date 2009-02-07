<?php 
$path = dirname(__FILE__).'/components/com_jdeveloper/admin/config.default.xml';
$xml  = simplexml_load_file($path);
$version = $xml->version;


run("ant release -Dversion={$version}");
$file    = "com_jdeveloper_{$version}.zip";
run("svn add packages/releases/{$file}");
run("svn ci -m ''");
 
function run($cmd)
{
		print $cmd."\n";
		
		print `{$cmd}`;
}