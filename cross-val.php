<?php

include 'NaiveBayesClass.php';

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

$labeled_arr = array();
$test_arr = array();

$op = new sentiments();
//$op->add('training_data.txt');

$content = file_get_contents('training_data.txt');
$test_arr = explode('DELIMITER', $content);
$labeled_arr['neg'] = $test_arr;

$content = file_get_contents('neutral_data.txt');
$labeled_arr['neutral'] = explode('DELIMITER', $content);
$test_arr = array_merge($test_arr, explode('DELIMITER', $content));

shuffle($test_arr);
$max_boundary = sizeof($test_arr);
$k = 10;
$augment = $max_boundary / $k;

for($i=0; $i<$max_boundary; $i+=$augment) print $i." ".$labeled_arr['neutral'][$i]."<hr>";

//for($i=0; $i<sizeof($test_arr); $i++) print $i." ".$test_arr[$i]."<hr>";
//for($i=0; $i<sizeof($labeled_arr['neg']); $i++) print $i." ".$labeled_arr['neg'][$i]."<hr>";
//for($i=0; $i<sizeof($labeled_arr['neutral']); $i++) print $i." ".$labeled_arr['neutral'][$i]."<hr>";

print $op->doc_negative."<br>";
print $op->doc_neutral."<br>";

/*
$r1 = 0;
$r2 = 0;
$r3 = 0;
$r4 = 0;

//----------------------------
$xv = file_get_contents('a1.txt');
$xv .= file_get_contents('a2.txt');
$xv .= file_get_contents('a3.txt');
add($xv);

$zd = file_get_contents('a4.txt');

$texts = explode('DELIMITER', $zd);
for($i=0; $i<sizeof($texts); $i++)
{
    $r1 += classify($texts[$i]);
}

print $r1."<hr><hr><hr><hr><hr><hr>";

unset($total_index);
unset($bad_index);
$word_count = 0;
$unique_word_count = 0;
$bad_word_count = 0;

//-----------------------------------
$xv = file_get_contents('a2.txt');
$xv .= file_get_contents('a3.txt');
$xv .= file_get_contents('a4.txt');
add($xv);

$zd = file_get_contents('a1.txt');

$texts = explode('DELIMITER', $zd);
for($i=0; $i<sizeof($texts); $i++)
{
    $r2 += classify($texts[$i]);
}

print $r2."<hr><hr><hr><hr><hr><hr>";

unset($total_index);
unset($bad_index);
$word_count = 0;
$unique_word_count = 0;
$bad_word_count = 0;

//------------------------------
$xv = file_get_contents('a3.txt');
$xv .= file_get_contents('a4.txt');
$xv .= file_get_contents('a1.txt');
add($xv);

$zd = file_get_contents('a2.txt');

$texts = explode('DELIMITER', $zd);
for($i=0; $i<sizeof($texts); $i++)
{
    $r3 += classify($texts[$i]);
}

print $r3."<hr><hr><hr><hr><hr><hr>";

unset($total_index);
unset($bad_index);
$word_count = 0;
$unique_word_count = 0;
$bad_word_count = 0;

//--------------------------------------------
$xv = file_get_contents('a4.txt');
$xv .= file_get_contents('a1.txt');
$xv .= file_get_contents('a2.txt');
add($xv);

$zd = file_get_contents('a3.txt');

$texts = explode('DELIMITER', $zd);
for($i=0; $i<sizeof($texts); $i++)
{
    $r4 += classify($texts[$i]);
}

print $r4."<hr><hr><hr><hr><hr><hr>";

unset($total_index);
unset($bad_index);
$word_count = 0;
$unique_word_count = 0;
$bad_word_count = 0;



print "<hr><hr>";
print "Kļūdas: ".$r1;
print "<br>";
print $r1/21;
print "<br>";
print "Kļūdas: ".$r2;
print "<br>";
print $r2/21;
print "<br>";
print "Kļūdas: ".$r3;
print "<br>";
print $r3/21;
print "<br>";
print "Kļūdas: ".$r4;
print "<br>";
print $r4/21;
print "<br>";


//-----------------------------------

/*
print "<b>Kopā</b>: ".$word_count."<br>";
print "<b>Slikti</b>: ".$bad_word_count."<br>";
print "<b>Unikāli</b>: ".$unique_word_count."<br><br>";

 */

?>



</div>
</body>
</html>