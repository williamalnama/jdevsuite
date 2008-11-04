<?php 
$path = dirname(__FILE__).'/components/com_jdeveloper/admin/config.default.xml';
$xml  = simplexml_load_file($path);
$version = $xml->version;

print `ant build -Dversion={$version}`;
$file    = "com_jdeveloper_{$version}.zip";

print `svn add packages/releases/{$file}`;
print `svn ci -m ''`;