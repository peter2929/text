<?php

include '../NaiveBayesClass.php';
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

//print "TEST";
//print $_POST['b'];

print rand(0, 1);

?>