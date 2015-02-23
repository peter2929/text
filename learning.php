<?php

error_reporting(E_ALL | E_STRICT);
include './phpmorphy/src/common.php';

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

</head>
<body>
    
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

//$base_form = $morphy->getBaseForm("");
//print $base_form[0];
//print "<br><br>";

//---------------------------------------------------------------------------------------------

$index = array();

function add($file)
{
        $word_count = 0;
        global $morphy, $index;
        $a = file_get_contents($file);
        $texts = explode('DELIMITER', $a);

        for($i=0; $i<sizeof($texts); $i++)
        {    
                $unwantedChars = array(',', '!', '?', '.', '(', ')', '=', '\n', '\r');
                $com3 = str_replace($unwantedChars, ' ', $texts[$i]);
                $words = explode(' ', $com3);

                for($m=0; $m<sizeof($words); $m++)
                {
                        $words[$m] = trim($words[$m]);
                        $words[$m] = mb_strtoupper($words[$m], 'UTF-8');

                        $base_form = $morphy->getBaseForm($words[$m]);

                        if($base_form[0])
                        {
                                $word = $base_form[0];
                                if(!isset($index[$word]))
                                {
                                    $index[$word] = 0;
                                }
                                $index[$word]++;
                                $word_count++;
                        }
                }
        }
}



add("training_data.txt");
arsort($index);
foreach($index as $key => $b)
{
    print $key." ".$b."<br>";
}



?>





</body>
</html>