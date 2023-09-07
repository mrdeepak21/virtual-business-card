<?php

require_once(plugin_dir_path( __FILE__ ) .'../vendor/autoload.php');

use Passbook\PassFactory;
use Passbook\Pass\Barcode;
use Passbook\Pass\Field;
use Passbook\Pass\Image;
use Passbook\Pass\Structure;
use Passbook\Type\Generic;

$outputDirectory =plugin_dir_path( __FILE__ ).'../apple-passes/';
if (!file_exists($outputDirectory)) {
    mkdir($outputDirectory, 0777, true);
}

// Set these constants with your values
define('P12_FILE', plugin_dir_path( __FILE__ ) . '../assets/cer.p12');
define('P12_PASSWORD', 'virtual-passes-1010');
define('WWDR_FILE',  plugin_dir_path( __FILE__ ) . '../assets/AppleWWDRCA.pem');
define('PASS_TYPE_IDENTIFIER', 'pass.com.heigh10.digitalcards');
define('TEAM_IDENTIFIER', 'PH23YH2YN9');
define('ORGANIZATION_NAME', 'Heigh10');
define('OUTPUT_PATH',  $outputDirectory);
define('ICON_FILE',  plugin_dir_path( __FILE__ ) .'../img/icon.png');
define('LOGO_FILE',  plugin_dir_path( __FILE__ ) .'../img/logo.png');

// Create an event ticket
$pass = new Generic("Username", "Username");
$pass->setBackgroundColor('rgb(60, 65, 76)');
$pass->setLogoText('Heigh10');

// Create pass structure
$structure = new Structure();

// Add primary field
$primary = new Field('name', 'Username');
$primary->setLabel('Name');
$structure->addPrimaryField($primary);

// // Add secondary field
$secondary = new Field('membership', 'Designation');
$secondary->setLabel('Designation');
$structure->addSecondaryField($secondary);

// // Add auxiliary field
// $auxiliary = new Field('datetime', '2013-04-15 @10:25');
// $auxiliary->setLabel('Date & Time');
// $structure->addAuxiliaryField($auxiliary);

// Add icon image
$icon = new Image(ICON_FILE, 'icon');
$pass->addImage($icon);
//add logo image
$logo = new Image( LOGO_FILE, 'logo' );
$pass->addImage( $logo );
//add thumb image
$thumb = new Image( ICON_FILE, 'thumbnail' );
$pass->addImage( $thumb );

// Set pass structure
$pass->setStructure($structure);

// Add barcode
$barcode = new Barcode(Barcode::TYPE_QR, site_url());
$pass->setBarcode($barcode);

// Create pass factory instance
$factory = new PassFactory(PASS_TYPE_IDENTIFIER, TEAM_IDENTIFIER, ORGANIZATION_NAME, P12_FILE, P12_PASSWORD, WWDR_FILE);
$factory->setOutputPath(OUTPUT_PATH);
$result =  $factory->package($pass,'user');


echo '<h1><a href="'.plugins_url('../apple-passes/',__FILE__).$result->getFilename().'" download="'.$result->getFilename().'">Download</a></h1>';