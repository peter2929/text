<?php
error_reporting(E_ALL | E_STRICT);
include './phpmorphy/src/common.php';

 

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

$file = "top.xml";
$fp = fopen($file, "rb");
$str = fread($fp, filesize($file));

$xml = new DOMDocument();
$xml->formatOutput = true;
$xml->preserveWhiteSpace = false;
$xml->loadXML($str) or die("Error");

// get document element
$root   = $xml->documentElement;
$fnode  = $root->firstChild;

//$fnode->appendChild($id);
//echo "<xmp>NEW:\n". $xml->saveXML() ."</xmp>";
//$xml->save("test.xml") or die("Error");


?>


<?xml version="1.0" encoding="utf-8"?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru" lang="ru" prefix="og: http://ogp.me/ns">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<style>

hr {
    display: block;
    height: 1px;
    border: 0;
    border-top: 1px solid #ccc;
    margin: 1em 0;
    padding: 0;
}

#page-wrap {
     width: 800px;
     margin: 0 auto;
}

html { 
	margin:0; 
	padding:0; 
	border:0; 
}

body
{
  margin: 0;
  padding: 0;
  border: 0;
  font-size: 100%;
  vertical-align: baseline;

  line-height: 1.5;
  background: white; 
}





</style>

</head>
<body>

<div id="page-wrap">

<?php


$file_content = file_get_contents("http://rus.tvnet.lv/top");

$parts = explode("<h3>", $file_content);

for($i=1; $i<15; $i++)
{
    $parts[$i] = substr($parts[$i], 9);
    $parts[$i] = strstr($parts[$i], '"', true);
    
    $url = $parts[$i]."/comments";
    //-------------------------------------------------------------------------------------------------------------------------
        $comments = retrieve_comments($url);

        $counter = 1;
        foreach($comments as $com)
        {
            $unwantedChars = array(',', '!', '?', '.', '(', ')', '=', '\n', '\r', '"'); ///////////////////////////
            $com3 = str_replace($unwantedChars, ' ', $com); //////////////
            $exp = explode(' ', $com3); //////// used to be $com

            $latin_words = 0;
            for($m=0; $m<sizeof($exp); $m++)
            {
                $exp[$m] = mb_strtoupper($exp[$m], 'UTF-8');
                $base_form = $morphy->getBaseForm($exp[$m]);
                //-------------------
                if(preg_match('/^[A-Za-zĀāĒēŪūĪīĢģĶķĻļČčŅņŠš]+/', $exp[$m]))
                {
                    $latin_words++;
                }
                //-------------------

                //var_dump($base_form);
                if($base_form[0])
                {
                    ///////////////print $base_form[0]."<br>";
                }
            }

            $latin_ratio = $latin_words/sizeof($exp);

            if($latin_ratio < 0.3)
            {
                ///print $counter.". ".$com."\n<hr>\n";
$new_el = $xml->createElement("comment");
$el_text = $xml->createTextNode($com);
$new_el->appendChild($el_text);

$fnode->appendChild($new_el);
$xml->save("top.xml") or die("Error");

                $counter++;
            }
        }
    
    //-------------------------------------------------------------------------------------------------------------------------
    
    //print $parts[$i]."<br>_____________________________________________\n\n";
}

/*

for($i=5; $i<sizeof($p); $i++)
{

$p[$i] = strstr($p[$i], "</p>", true);

	if($p[$i])
	{
		$pos = strpos($p[$i], ">") +1;
		$p[$i] = substr($p[$i], $pos);
                $p[$i] = strip_tags($p[$i]);
		print "<p>".$p[$i]."</p>\n\n";
                fwrite($handle, "<p>".$p[$i]."</p>\r\n");
	}

}


//----------------------------------------COMMENTS---------------------------------------------------
add("training_data.txt");
$res = array();
print "<b style='font-size:125%'>Komentāri: </b><br><br>";

$url = $_POST['url']."/comments";
$comments = retrieve_comments($url);

$counter = 1;
foreach($comments as $com)
{
    $unwantedChars = array(',', '!', '?', '.', '(', ')', '=', '\n', '\r', '"'); ///////////////////////////
    $com3 = str_replace($unwantedChars, ' ', $com); //////////////
    $exp = explode(' ', $com3); //////// used to be $com

    $latin_words = 0;
    for($m=0; $m<sizeof($exp); $m++)
    {
        $exp[$m] = mb_strtoupper($exp[$m], 'UTF-8');
        $base_form = $morphy->getBaseForm($exp[$m]);
        //-------------------
        if(preg_match('/^[A-Za-zĀāĒēŪūĪīĢģĶķĻļČčŅņŠš]+/', $exp[$m]))
        {
            $latin_words++;
        }
        //-------------------

        //var_dump($base_form);
        if($base_form[0])
        {
            ///////////////print $base_form[0]."<br>";
        }
    }

    $latin_ratio = $latin_words/sizeof($exp);
    
    if($latin_ratio < 0.3)
    {
        fwrite($handle, $counter.". ".$com."\n<hr>\r\n");
       ////////////////// print $counter.". ".$com."\n<hr>\n";
        $counter++;
        $h = classify($com);
        $res[$h] = $com;
    }
}


$i = 2;
$flag = true;
while($flag)
{
    $comments2 = retrieve_comments($url."/page/".$i);

    if($comments === $comments2)
    {
        $flag = false;
    }
    else
    {
        print "<br><b style='font-size:125%'>".$i.". lappuse</b><br><br>";
        $comments = $comments2;
        foreach($comments as $com)
        {
    //------------------------------------------------
    $exp = explode(' ', $com);
    $latin_words = 0; //______________________________________
    for($m=0; $m<sizeof($exp); $m++)
    {
        $exp[$m] = mb_strtoupper($exp[$m], 'UTF-8');
        $base_form = $morphy->getBaseForm($exp[$m]);
        
        //-------------------
        if(preg_match('/^[A-Za-zĀāĒēŪūĪīĢģĶķĻļČčŅņŠš]+/', $exp[$m]))
        {
            $latin_words++;
        }
        //-------------------

        if($base_form[0])
        {
           ////////////////// print $base_form[0]."<br>";
        }
    }
    
        $latin_ratio = $latin_words/sizeof($exp);
        if($latin_ratio < 0.3)
        {
            fwrite($handle, $counter.". ".$com."\n<hr>\r\n");    
            ////////////////////////print $counter.". ".$com."\n<hr>\n";
            $counter++;
            $h = classify($com);
            $res[(string)$h] = $com;
        }
        
        }
        $i++;
    }
}

ksort($res);
foreach($res as $key => $b)
{
    print $key." ".$b."<hr>";
}

*/

function retrieve_comments($url)
{
    $a = file_get_contents($url);
    $a = strstr($a, "<ol class=\"commentary\">");
    $b = explode("</ol>", $a);
    
    $c = explode("<p class=\"message\">", $b[0]);
    $result = '';
    $d = array();
    for($i=0; $i<sizeof($c); $i++)
    {
        $c[$i] = strstr($c[$i], "</p>", true);

       	if($c[$i])
	{
                $d[$i] = strip_tags($c[$i]);
	}
    }

    return $d;
}

?>