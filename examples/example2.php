<?php

require_once __DIR__.'/../bootstrap.php';

use TheFox\MachO\Binary;

chdir(__DIR__);
$binary = new Binary('example2');
$binary->setExpectedFileSize(8496); // Optional
$binary->setExpectedMd5sum('ac0ebae5df127191a003b09a700390bc'); // Optional

try{
	$binary->analyze();
}
catch(Exception $e){
	print 'ERROR: '.$e->getMessage().PHP_EOL;
	exit(1);
}
print 'check OK'.PHP_EOL;

// Example 2
// We want to change the text "a second line" from the second line
// of code into "2nd line". The length of the new string can't be
// longer as the original string. Consider to close the string
// with a "\0" null char.
$sectionBaseAddress = 0x0000000100000f82;
$cmdAddress = 0x0000000100000f8e;
$offset = $cmdAddress - $sectionBaseAddress;
$len = 12;
$binary->write('__TEXT', '__cstring', $offset, '2nd line'.chr(0));
