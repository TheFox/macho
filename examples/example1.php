<?php

require_once __DIR__.'/../bootstrap.php';

use TheFox\MachO\Binary;

chdir(__DIR__);
$binary = new Binary('example1');
$binary->setExpectedFileSize(8496); // Optional
$binary->setExpectedMd5sum('ac0ebae5df127191a003b09a700390bc'); // Optional


$check = false;
try{
	$binary->analyze();
	$check = true;
}
catch(Exception $e){
	print 'ERROR: '.$e->getMessage().PHP_EOL;
}

if($check){
	print 'check OK'.PHP_EOL;
	
	// Example 1
	// In this example we want to overwrite the frist line of
	// the original source c code: puts("hello world");
	// In this case we want to overwrite two instructions:
	// 		leaq   0x37(%rip), %rdi
	// 		callq  0x100000f60
	// This two instructions are 12 byte long.
	$sectionBaseAddress = 0x0000000100000f40;
	$cmdAddress = 0x0000000100000f44;
	$offset = $cmdAddress - $sectionBaseAddress;
	$len = 12;
	$binary->write('__TEXT', '__text', $offset, pack('H*', str_repeat('90', $len)));
}
