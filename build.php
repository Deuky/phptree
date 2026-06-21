<?php
$phar = new Phar('phptree.phar', 0, 'phptree.phar');
$phar->startBuffering();

$phar->buildFromDirectory('.', '#^(?!.*(\.git|tests|build\.php)).*\.php$#');
$phar->addFile('bin/phptree');
$phar->addEmptyDir('vendor/symfony/console/Resources');

$stub = "#!/usr/bin/env php\n" . $phar->createDefaultStub('bin/phptree');
$phar->setStub($stub);
$phar->compressFiles(Phar::GZ);
$phar->stopBuffering();