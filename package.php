<?php

require_once 'PEAR/PackageFileManager2.php';

$version = '0.1.5';
$notes = <<<EOT
see ChangeLog
EOT;

$description =<<<EOT
Rawk!
EOT;

$package = new PEAR_PackageFileManager2();
PEAR::setErrorHandling(PEAR_ERROR_DIE);

$result = $package->setOptions(
	array(
		'filelistgenerator' => 'svn',
		'simpleoutput'      => true,
		'baseinstalldir'    => '/',
		'packagedirectory'  => './',
		'dir_roles'         => array(
			'HotDate'      => 'php',
			'locale'       => 'data',
			'www'          => 'data',
			'dependencies' => 'data',
		),
	)
);

$package->setPackage('HotDate');
$package->setSummary('Hot Date!');
$package->setDescription($description);
$package->setChannel('pear.silverorange.com');
$package->setPackageType('php');
$package->setLicense('private', 'http://www.silverorange.com/');

$package->setReleaseVersion($version);
$package->setReleaseStability('alpha');
$package->setAPIVersion('0.1.1');
$package->setAPIStability('alpha');
$package->setNotes($notes);

$package->addIgnore('package.php');

$package->addMaintainer('lead', 'gauthierm', 'Mike Gauthier', 'mike@silverorange.com');

$package->setPhpDep('5.2.0');
$package->setPearinstallerDep('1.4.0');
$package->generateContents();

if (isset($_GET['make']) || (isset($_SERVER['argv']) && @$_SERVER['argv'][1] == 'make')) {
	$package->writePackageFile();
} else {
	$package->debugPackageFile();
}

?>
