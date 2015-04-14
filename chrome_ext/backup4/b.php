<?php

include 'NaiveBayesClass_forjs.php';
set_time_limit(300);


$op = new sentiments();

$comment = strip_tags($_POST['b']);
$comment .= "DELIMITER";


if($_POST['change_label_to'] == "neutral")
{
    $handle = fopen("added_neutral.txt", "a+");
    fwrite($handle, $comment);
    fclose($handle);
}
else if($_POST['change_label_to'] == "negative")
{
    $handle = fopen("added_negative.txt", "a+");
    fwrite($handle, $comment);
    fclose($handle);
}




//print strip_tags($_POST['b']);
//print $_POST['change_label_to'];

?>