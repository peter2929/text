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

//$a = file_get_contents('http://rus.tvnet.lv/novosti/obschjestvo/282082-eksprjezidjent_latvii_stal_pisatjeljem');
//$a = file_get_contents('http://rus.tvnet.lv/novosti/kriminal_i_chp/282052-gruppovoje_iznasilovanije_na_paromje_amorella_novije_podrobnosti');

//$a = file_get_contents('http://rus.tvnet.lv/novosti/za_rubjezhom/282120-mjedvjedjev_anonsiroval_skachok_cjen_na_ljekarstva_jeschje_na_20_procjentov');
$a = file_get_contents($_POST['url']);

$a = strstr($a, "<h1 class=\"title\">");
$b = explode("</h1>", $a);
$header = $b[0];
$header = substr($header, 18);
$header = trim($header);

print "<b style='font-size:125%'>Virsraksts</b>: ".$header;
print "<hr>";

$b[1] = strstr($b[1],  "<ul id=\"options_bottom\" class=\"addClear\">", true); //------------

$p = explode("<p", $b[1]);
$excerpt = $p[4];
////print sizeof($p)."__________________________";

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
	}

}


//----------------------------------------COMMENTS---------------------------------------------------

print "<b style='font-size:125%'>Komentāri: </b><br>";

$url = $_POST['url']."/comments";
$comments = retrieve_comments($url);
print $comments;

$i = 2;
$flag = true;
$counter = 1;
while($flag)
{
    $comments2 = retrieve_comments($url."/page/".$i);
    if($comments === $comments2)
    {
        $flag = false;
    }
    else
    {
        print "<br><b style='font-size:125%'>".$i.". lappuse</b>";
        print $comments2;
        $comments = $comments2;
        $i++;
    }
}



}

function retrieve_comments($url)
{
    //global $counter;
    $a = file_get_contents($url);
    $a = strstr($a, "<ol class=\"commentary\">");
    $b = explode("</ol>", $a);
    
    $c = explode("<p class=\"message\">", $b[0]);
    $result = '';
    for($i=0; $i<sizeof($c); $i++)
    {
        $c[$i] = strstr($c[$i], "</p>", true);

       	if($c[$i])
	{
		//$pos = strpos($c[$i], ">") +1;
		//$c[$i] = substr($c[$i], $pos);
                $c[$i] = strip_tags($c[$i]);
		$result .= "<p>".$i.". ".$c[$i]."</p>\n<hr>\n";
                //$counter++;
	}
    }
    
    return $result;
}


?>