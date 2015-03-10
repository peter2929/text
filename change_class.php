<?php
/*
error_reporting(E_ALL | E_STRICT);
include './phpmorphy/src/common.php';
//ini_set('default_charset', 'utf-8')
///mb_internal_encoding('UTF-8');
?>

<html>
<head  lang="en">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="http://maxcdn.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

</head>
<body>
<center>
<div class="container">    
<?php

// set some options
$opts = array(
	// storage type, follow types supported
	// PHPMORPHY_STORAGE_FILE - use file operations(fread, fseek) for dictionary access, this is very slow...
	// PHPMORPHY_STORAGE_SHM - load dictionary in shared memory(using shmop php extension), this is preferred mode
	// PHPMORPHY_STORAGE_MEM - load dict to memory each time when phpMorphy intialized, this useful when shmop ext. not activated. Speed same as for PHPMORPHY_STORAGE_SHM type
	'storage' => PHPMORPHY_STORAGE_FILE,
	// Extend graminfo for getAllFormsWithGramInfo method call
	'with_gramtab' => false,
	// Enable prediction by suffix
	'predict_by_suffix' => true, 
	// Enable prediction by prefix
	'predict_by_db' => true
);

// Path to directory where dictionaries located
$dir = dirname(__FILE__) . '\phpmorphy\dicts';

// Create descriptor for dictionary located in $dir directory with russian language
$dict_bundle = new phpMorphy_FilesBundle($dir, 'rus');

// Create phpMorphy instance
try {
	$morphy = new phpMorphy($dict_bundle, $opts);
} catch(phpMorphy_Exception $e) {
	die('Error occured while creating phpMorphy instance: ' . $e->getMessage());
}


print $_POST['f'];
print "<br>";

*/


if($_POST['change_to'] == "neutral")
{
    //$handle = fopen("neutral_data.txt", "a+");
    $handle = fopen("added_neutral.txt", "a+");
    fwrite($handle, $_POST['f']);
    header("Location: analyze_top.php#".$_POST['cn']);
}
else if($_POST['change_to'] == "negative")
{
    //$handle = fopen("training_data.txt", "a+");
    $handle = fopen("added_negative.txt", "a+");
    fwrite($handle, $_POST['f']);
    header("Location: analyze_top.php#".$_POST['cn']);
}
else if($_POST['change_to'] == "reset")
{
    $handle = fopen("added_neutral.txt", "w");
    $handle2 = fopen("added_negative.txt", "w");
    header("Location: analyze_top.php");
}
