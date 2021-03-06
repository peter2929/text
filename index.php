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

///$base_form = $morphy->getBaseForm("ТЕСТ)КОНЕЦ");
///print $base_form[0];

$handle = fopen("resource.txt", "a+");

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

<form method=post action=index.php>
<input name=url size=120>
<input type=submit>
</form>

<br><br>

<?php

if(isset($_POST['url']))
{

$file_content = file_get_contents($_POST['url']);

$file_content = strstr($file_content, "<h1 class=\"title\">");
$b = explode("</h1>", $file_content);
$header = $b[0];
$header = substr($header, 18);
$header = trim($header);

print "<b style='font-size:125%'>Virsraksts</b>: ".$header;
print "<hr>";
fwrite($handle, "<b style='font-size:125%'>Virsraksts</b>: ".$header."<hr>");

$b[1] = strstr($b[1],  "<ul id=\"options_bottom\" class=\"addClear\">", true); //------------

$p = explode("<p", $b[1]);
$excerpt = $p[4];


$excerpt = strstr($excerpt, "</p>", true);
$pos = strpos($excerpt, ">") +1;
$excerpt = substr($excerpt, $pos);
print "<p><b>".$excerpt."</b></p>\n\n";

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

}

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

//-------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------
//-------------------------------------------------------------------------------------------

$index = array();
$word_count = 0;
$unique_word_count = 0;

function add($file)
{
        global $morphy, $index, $word_count, $unique_word_count;
        $a = file_get_contents($file);
        $texts = explode('DELIMITER', $a);

        for($i=0; $i<sizeof($texts); $i++)
        {    
                //$words = tokenize($texts[$i]);
                $unwantedChars = array(',', '!', '?', '.', '(', ')', '=', '\n', '\r', '"');
                $com3 = str_replace($unwantedChars, ' ', $texts[$i]);
                $words = explode(' ', $com3);
                for($m=0; $m<sizeof($words); $m++)
                {
                        $words[$m] = trim($words[$m]);
                        $words[$m] = mb_strtoupper($words[$m], 'UTF-8');

                        $base_form = $morphy->getBaseForm($words[$m]);

                        //if($base_form[0] && (mb_strlen($base_form[0], 'UTF-8') > 3))
                        if($base_form[0])
                        {
                                $word = $base_form[0];
                                if(!isset($index[$word]))
                                {
                                    $index[$word] = 0;
                                    $unique_word_count++;
                                }
                                $index[$word]++;
                                $word_count++;
                        }
                }
        }
}

function classify($document)
{
    global $morphy, $index, $word_count, $unique_word_count;
    $unwantedChars = array(',', '!', '?', '.', '(', ')', '=', '\n', '\r', '"');
    $com3 = str_replace($unwantedChars, ' ', $document);

    $words = explode(' ', $com3);
    $prob = 1;

    for($m=0; $m<sizeof($words); $m++)
    {
            $words[$m] = trim($words[$m]);
            $words[$m] = mb_strtoupper($words[$m], 'UTF-8');

            $base_form = $morphy->getBaseForm($words[$m]);

            //if($base_form[0] && (mb_strlen($base_form[0], 'UTF-8') > 3))
            if($base_form[0])
            {
                    $word = $base_form[0];
                    if(!isset($index[$word]))
                    {
                        $count = 0;
                    }
                    else $count = $index[$word];

                    ///$prob *= ($count + 1) / ($word_count + $word_count);
                    $prob += log(($count + 1) / ($word_count + $unique_word_count));
                    /////
                    //print $word." ".$count."-------------------<br>";
            }
    }

    //print "<hr>";
    //$prob = exp($prob);
    return $prob;
}


?>