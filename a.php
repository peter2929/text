<?php

include 'NaiveBayesClass_forjs.php';
set_time_limit(300);


$op = new sentiments();

$content = file_get_contents('training_data.txt');
$exploded_doc = explode('DELIMITER', $content);
for($i=0; $i<sizeof($exploded_doc); $i++)
{
    $op->add($exploded_doc[$i], "negative");
    
}

$content = file_get_contents('neutral_data.txt');
$exploded_doc = explode('DELIMITER', $content);
for($i=0; $i<sizeof($exploded_doc); $i++)
{
    $op->add($exploded_doc[$i], "neutral");
    
}
/*
$res = $op->classify($_POST['b']);
if($res == "negative") print "0";
else print "1";
*/

$exp = explode('DELIMITER', $_POST['b']);
$arr = array();
$j = 0;
for($i=0; $i<sizeof($exp)-1; $i++)
{
    //print $exp[$i];
    $is_negative = $op->classify($exp[$i]);
    if($is_negative == "negative")
    {
        $arr[$j] = $i;
        $j++;
    }
}

$res = "";
for($i=0; $i<sizeof($arr); $i++)
$res .= $arr[$i]." ";

if(isset($res)) print $res;
else print "No negative comments";

//print "TEST";
//print $_POST['b'];
//print "0";
//print rand(0, 1);

?>