<?php

include 'NaiveBayesClass.php';
set_time_limit(300);

class test_docs
{
    public $text = '';
    public $label = '';
}

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
$objects = array();


$content = file_get_contents('training_data.txt');
$exploded_doc = explode('DELIMITER', $content);
$test_arr = $exploded_doc;
$labeled_arr['negative'] = $exploded_doc;
for($i=0; $i<sizeof($exploded_doc); $i++)
{
    //$op->add($exploded_doc[$i], "negative");
    $objects[$i] = new test_docs();
    $objects[$i]->text = $exploded_doc[$i];
    $objects[$i]->label = "negative";
}

$j = $i;

$content = file_get_contents('neutral_data.txt');
$exploded_doc = explode('DELIMITER', $content);
$labeled_arr['neutral'] = $exploded_doc;
$test_arr = array_merge($test_arr, $exploded_doc);
for($i=0; $i<sizeof($exploded_doc); $i++, $j++)
{
    //$op->add($exploded_doc[$i], "neutral");
    $objects[$j] = new test_docs();
    $objects[$j]->text = $exploded_doc[$i];
    $objects[$j]->label = "neutral";
}

$max_boundary = sizeof($objects);
shuffle($test_arr);
shuffle($objects);


//----------------------FIRST LET'S DIVIDE THE DATA IN HALF---------------------
/*
for($i=0; $i<100; $i++)
{
    $op->add($objects[$i]->text, $objects[$i]->label);
}

$correct_count = 0;
$incorrect_count = 0;
for($i=100; $i<200; $i++)
{
    print $objects[$i]->text."<br>";
    print "<hr>".$objects[$i]->label."<hr>";
    $test_result = $op->classify($objects[$i]->text);
    if($test_result == $objects[$i]->label)
    {
        print "<b>Correct!</b>";
        $correct_count++;
    }
    else
    {
        print "<b>INcorrect!</b>";
        $incorrect_count++;
    }
    print "<hr>";
}

print "<hr><hr>";
print "Correct: ".$correct_count;
print "<br>";
print "Incorrect: ".$incorrect_count;
*/
//-----------------------NOW LET'S PICK 20 RANDOM DOCUMENTS-------------------------------------------------------

$k = 10;

$correct_count = 0;
$incorrect_count = 0;
$tp_negative = 0;
$fp_negative = 0;
$fn_negative = 0;
$tn_negative = 0;

$tp_neutral = 0;
$fp_neutral = 0;
$fn_neutral = 0;
$tn_neutral = 0;

for($i=0; $i<10; $i++) //10
{
    //$correct_count = 0;
    //$incorrect_count = 0;
    $op->reset();
    shuffle($objects);

    for($j=0; $j<180; $j++)
    {
        $op->add($objects[$j]->text, $objects[$j]->label);
    }

    for($j=180; $j<200; $j++)
    {
        print $objects[$j]->text."<br>";
        print "<br>".$objects[$j]->label."<hr>";

        $test_result = $op->classify($objects[$j]->text);
        
        //----------------------------------
        if($objects[$j]->label == "negative")
        {
            if($test_result == "negative")
            {
                $tp_negative++;
            }
            else
            {
                $fn_negative++;
            }
        }
        else
        {
            if($test_result == "negative")
            {
                $fp_negative++;
            }
            else
            {
                $tn_negative++;
            }
        }
        //-----------
        if($objects[$j]->label == "neutral")
        {
            if($test_result == "neutral")
            {
                $tp_neutral++;
            }
            else
            {
                $fn_neutral++;
            }
        }
        else
        {
            if($test_result == "neutral")
            {
                $fp_neutral++;
            }
            else
            {
                $tn_neutral++;
            }
        }
        //----------------------------------
        
        if($test_result == $objects[$j]->label)
        {
            print "<br><h3>Korekti izvēlēts!</h3><br><br>";
            $correct_count++;
        }
        else
        {
            print "<br><h3>Nav korekti izvēlēts!</h3><br><br>";
            $incorrect_count++;
        }
        print "<hr>";
    }
    
$op->reset();
}

$precision_negative = $tp_negative / ($tp_negative + $fp_negative);
$recall_negative = $tp_negative / ($tp_negative + $fn_negative);
$f1_negative = 2 * $precision_negative * $recall_negative / ($precision_negative + $recall_negative);


$precision_neutral = $tp_neutral / ($tp_neutral + $fp_neutral);
$recall_neutral = $tp_neutral / ($tp_neutral + $fn_neutral);
$f1_neutral = 2 * $precision_neutral * $recall_neutral / ($precision_neutral + $recall_neutral);

print "<table style='width:40%' class='table table-bordered table-hover'>";

print "<hr><hr>";
print "<tr><td><b>Korekti: </b></td><td>".$correct_count * 100 /($correct_count+$incorrect_count)."%</td></tr>\n";
print "<tr><td><b>Nekorekti: </b></td><td>".$incorrect_count * 100 /($correct_count+$incorrect_count)."%</td></tr>\n";

print "</table><br>";

print "<center><b>Negatīviem komentāriem</b></center>";

print "<table style='width:40%' class='table table-bordered table-hover'>\n";
print "<tr><td>&nbsp;</td><td><b>Ir</b></td><td><b>nav</b></td></tr>\n";
print "<tr><td><b>Izvēlēts</b></td><td>TP: ".$tp_negative."</td><td>FP: ".$fp_negative."</td></tr>\n";
print "<tr><td><b>Nav izvēlēts</b></td><td>FN: ".$fn_negative."</td><td>TN: ".$tn_negative."</td></tr>\n";
print "</table><br>";

print "<table style='width:40%' class='table table-bordered table-hover'>";
print "<tr><td><b>Precision: </b></td><td>".$precision_negative."</td></tr>\n";
print "<tr><td><b>Recall: </b></td><td>".$recall_negative."</td></tr>\n";
print "<tr><td><b>F1: </b></td><td>".$f1_negative."</td></tr>\n";

print "</table><br>";

/*
print "For negative class <br>";
print "TP: ".$tp_negative."<br>";
print "FP: ".$fp_negative."<br>";
print "FN: ".$fn_negative."<br>";
print "TN: ".$tn_negative."<br>";
print "Precision: ".$precision_negative."<br>";
print "Recall: ".$recall_negative."<br>";
print "F1: ".$f1_negative."<br>";
*/

print "<center><b>Neitrāliem komentāriem</b></center>";

print "<table style='width:40%' class='table table-bordered table-hover'>\n";
print "<tr><td>&nbsp;</td><td><b>Ir</b></td><td><b>nav</b></td></tr>\n";
print "<tr><td><b>Izvēlēts</b></td><td>TP: ".$tp_neutral."</td><td>FP: ".$fp_neutral."</td></tr>\n";
print "<tr><td><b>Nav izvēlēts</b></td><td>FN: ".$fn_neutral."</td><td>TN: ".$tn_neutral."</td></tr>\n";
print "</table><br><br>";

print "<table style='width:40%' class='table table-bordered table-hover'>";
print "<tr><td><b>Precision: </b></td><td>".$precision_neutral."</td></tr>\n";
print "<tr><td><b>Recall: </b></td><td>".$recall_neutral."</td></tr>\n";
print "<tr><td><b>F1: </b></td><td>".$f1_neutral."</td></tr>\n";

print "</table><br>";

/*
print "<hr>";
print "For neutral class <br>";
print "TP: ".$tp_neutral."<br>";
print "FP: ".$fp_neutral."<br>";
print "FN: ".$fn_neutral."<br>";
print "TN: ".$tn_neutral."<br>";
print "Precision: ".$precision_neutral."<br>";
print "Recall: ".$recall_neutral."<br>";
print "F1: ".$f1_neutral."<br>";
*/

//------------------------------------------------------------------------------
$k = 10;
$augment = $max_boundary / $k;

$start_test_sample = 0;
$end_test_sample = 0;

/*
for($i=0; $i<sizeof($objects); $i++)
{
    print $i." ".$objects[$i]->text."<hr>";
    print $objects[$i]->label."<hr>";
}
*/

/*
for($i=0; $i<$max_boundary; $i+=$augment)
{
    for($j=$i-1; $j>=0; $j--)
    {
        
    }
}
*/

//------------------------------------------------------------------------------


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