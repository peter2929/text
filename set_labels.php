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

//-----------------------------------------------

$content = file_get_contents('added_negative.txt');
$exploded_doc = explode('DELIMITER', $content);
for($i=0; $i<sizeof($exploded_doc)-1; $i++)
{
    $op->add($exploded_doc[$i], "negative");
    
}

$content = file_get_contents('added_neutral.txt');
$exploded_doc = explode('DELIMITER', $content);
for($i=0; $i<sizeof($exploded_doc)-1; $i++)
{
    $op->add($exploded_doc[$i], "neutral");
    
}

//-----------------------------------------------

$exp = explode('DELIMITER', $_POST['comments']);
$res = "";
for($i=0; $i<sizeof($exp)-1; $i++)
{
    $is_negative = $op->classify($exp[$i]);
    if($is_negative == "negative")
    {
        $res .= "negative ";
    }
    else if($is_negative == "neutral")
    {
        $res .= "neutral ";
    }
    else if($is_negative == "noncyrillic")
    {
        $res .= "noncyrillic ";
    }
}


if(isset($res)) print $res;
else print "No negative comments";





//print "TEST";
//print $_POST['b'];
//print "0";
//print rand(0, 1);

?>