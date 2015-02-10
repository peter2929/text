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


/*
hr {
  border: 0 solid #ccc;
  border-top-width: 1px;
  clear: both;
  height: 0;
}
*/


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

//$a = file_get_contents('http://rus.tvnet.lv/novosti/obschjestvo/282082-eksprjezidjent_latvii_stal_pisatjeljem');
//$a = file_get_contents('http://rus.tvnet.lv/novosti/kriminal_i_chp/282052-gruppovoje_iznasilovanije_na_paromje_amorella_novije_podrobnosti');

//$a = file_get_contents('http://rus.tvnet.lv/novosti/za_rubjezhom/282120-mjedvjedjev_anonsiroval_skachok_cjen_na_ljekarstva_jeschje_na_20_procjentov');
$a = file_get_contents($_POST['url']);

$a = strstr($a, "<h1 class=\"title\">");
$b = explode("</h1>", $a);
$header = $b[0];
$header = substr($header, 18);
$header = trim($header);

print "Header: ".$header;
print "<hr>";

$p = explode("<p", $b[1]);
$excerpt = $p[4];
////print sizeof($p)."__________________________";
for($i=5; $i<sizeof($p)-9; $i++)
{

$p[$i] = strstr($p[$i], "</p>", true);

	if($p[$i])
	{
		$pos = strpos($p[$i], ">") +1;
		$p[$i] = substr($p[$i], $pos);
		print "<p>".$p[$i]."</p>\n<hr>\n";
	}

}

// <span class="boxes_titles">�������</span>


//----------------------------------------COMMENTS---------------------------------------------------

/* ////////////////////////////////////////////////////
$a = file_get_contents($_POST['url']."/comments");


$a = strstr($a, "<ol class=\"commentary\">");
$b = explode("</ol>", $a);

print $b[0];
*/

//<p class="message">
//pagination
// let's say there are 2 pages. 3rd will be identical with the 2nd

retrieve_comments($_POST['url']);

}

function retrieve_comments($url)
{
    $a = file_get_contents($url."/comments");
    $a = strstr($a, "<ol class=\"commentary\">");
    $b = explode("</ol>", $a);
    
    $c = explode("<p class=\"message\">", $b[0]);
    
    for($i=0; $i<sizeof($c); $i++)
    {
        $c[$i] = strstr($c[$i], "</p>", true);

       	if($c[$i])
	{
		//$pos = strpos($c[$i], ">") +1;
		//$c[$i] = substr($c[$i], $pos);
		print "<p>".$c[$i]."</p>\n<hr>\n";
	}
    }
}


?>