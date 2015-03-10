<?php

error_reporting(E_ALL | E_STRICT);
include './phpmorphy/src/common.php';
//ini_set('default_charset', 'utf-8')
///mb_internal_encoding('UTF-8');
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

//$base_form = $morphy->getBaseForm("ПЕНДОСОВСКИЙ");
//print $base_form[0];
///print mb_detect_encoding("");
//print "<br><br>";

$stopwords = array();
$fh = fopen('stop-words.txt', 'r');
while($line = fgets($fh))
{
    $trimmed_line = trim($line);
    $trimmed_line = mb_strtoupper($trimmed_line, 'UTF-8');
    $base_form = $morphy->getBaseForm($trimmed_line);  /////
    ///$stopwords[$trimmed_line] = 1;
    $stopwords[$base_form[0]] = 1;
}



//---------------------------------------------------------------------------------------------

$total_index = array();
$bad_index = array();
$word_count = 0;
$bad_word_count = 0;
$unique_word_count = 0;
$bad_word_count = 0;

function add($file)
{
        global $morphy, $total_index, $bad_index, $word_count, $unique_word_count, $bad_word_count;
        $a = file_get_contents($file);
        $a .= file_get_contents("added_negative.txt");
        $texts = explode('DELIMITER', $a);

        for($i=0; $i<sizeof($texts); $i++)
        {    
                $unwantedChars = array(',', '!', '?', '.', '(', ')', '=', '\n', '\r', '"');
                $com3 = str_replace($unwantedChars, ' ', $texts[$i]);
                $words = explode(' ', $com3);
                for($m=0; $m<sizeof($words); $m++)
                {
                        $words[$m] = trim($words[$m]);
                        $words[$m] = mb_strtoupper($words[$m], 'UTF-8');

                        $base_form = $morphy->getBaseForm($words[$m]);

                        if($base_form[0]  && !isset($stopwords[$base_form[0]]))
                        {
                                $word = $base_form[0];
                                if(!isset($total_index[$word]))
                                {
                                    $total_index[$word] = 0;
                                    $bad_index[$word] = 0;
                                    $unique_word_count++;
                                }

                                $total_index[$word]++;
                                $bad_index[$word]++;
                                $word_count++;
                        }
                }
        }
        
        //---------------------------------------------------------------
        
        $b = file_get_contents("neutral_data.txt");
        $b .= file_get_contents("added_neutral.txt");
        $com3 = str_replace($unwantedChars, ' ', $b);
        $words = explode(' ', $com3);
        $bad_word_count = $word_count;
        
        for($m=0; $m<sizeof($words); $m++)
        {
            $words[$m] = trim($words[$m]);
            $words[$m] = mb_strtoupper($words[$m], 'UTF-8');

            $base_form = $morphy->getBaseForm($words[$m]);

            if($base_form[0]  && !isset($stopwords[$base_form[0]]))
            {
                    $word = $base_form[0];
                    if(!isset($total_index[$word]))
                    {
                        $total_index[$word] = 0;
                        $bad_index[$word] = 0;
                        $unique_word_count++;
                    }

                    $total_index[$word]++;
                    $word_count++;
            }
        }

}

function classify($document)
{
    global $morphy, $total_index, $bad_index, $word_count, $bad_word_count, $unique_word_count, $stopwords, $cn;
    $cn++;
    $denominator = 0;
    $unwantedChars = array(',', '!', '?', '.', '(', ')', '=', '\n', '\r', '"', ':');
    $com3 = str_replace($unwantedChars, ' ', $document);

    $words = explode(' ', $com3);
    $prob = 0;
    $prob2 = 0;
    $neutral_word_count = $word_count - $bad_word_count;
    print "<a name=".$cn."></a>";
    print $document;
    print "<table style='width:20%'  class='table table-bordered table-hover'>";
    print "<th>Vārds</th><th>Neg.</th><th>Neitrāls</th><th>Neg.</th><th>Neitrāls</th>\n";
    for($m=0; $m<sizeof($words); $m++)
    {
            $words[$m] = trim($words[$m]);
            $words[$m] = mb_strtoupper($words[$m], 'UTF-8');

            $base_form = $morphy->getBaseForm($words[$m]);

            if($base_form[0] && !isset($stopwords[$base_form[0]]))
            {
                    $word = $base_form[0];
                    if(!isset($total_index[$word]))
                    {
                        $count_total = 0;
                        $count_bad = 0;
                    }
                    else
                    {
                        $count_total = $total_index[$word];
                        if(!isset($bad_index[$word]))
                            $count_bad = 0;
                        else
                            $count_bad = $bad_index[$word];
                    }

                    //if($count > 0)
                    //{

                        $numerator = ($count_bad+1) / ($bad_word_count+$unique_word_count);
                        $prob += log($numerator);
                        $neutral_occurrence = $count_total - $count_bad;

                        $numerator2 = ($neutral_occurrence+1) / ($neutral_word_count+$unique_word_count);
                        $prob2 += log($numerator2);

                        print "<tr><td>".$word."</td><td>".log($numerator)."</td><td>".log($numerator2)."</td><td>".$count_bad."</td><td>".$neutral_occurrence."</td></tr>\n"; ////
                    //}
            }
            ////
    }
    
    $prob += log(0.2);
    $prob2 += log(1);

print "</table><br>";

print "<form action='change_class.php' method='post'>";
print "<input name=f value=\"".$document."\" type=hidden>";

if($prob > $prob2)
{
    print "<span style='color:#f00;'><b>Negatīvs!</b></span><br>";
    //print "<a href='#' class='btn  btn-primary' role='button'>Tomēr ir neitrāls</a><br><br>";
    print "<input name=change_to value=\"neutral\" type=hidden>";
    print "<input type=\"submit\" class='btn  btn-primary' value='Tomēr ir neitrāls'><br><br>";
}
else
{
    print "<span style='color:#0f0;'><b>Nav negatīvs!</span></b><br>";
    //print "<a href='#' class='btn  btn-primary' role='button'>Tomēr ir negatīvs</a><br><br>";
    print "<input name=change_to value=\"negative\" type=hidden>";
    print "<input type=\"submit\" class='btn  btn-primary' value='Tomēr ir negatīvs'><br><br>";
}

print "<input type=hidden name=cn value=".$cn."></a>";
print "<table style='width:300px;' class='table table-bordered table-hover'><tr><td>Neg prob: </td><td>".$prob."</td></tr><tr><td>NON neg prob </td><td>".$prob2."  </td></tr></table>";

print "</form>\n\n";
return "";

}


add("training_data.txt");

//print "<table style='width:50%'>";
//arsort($total_index);
//print "</table><br>";

print "<b>Kopā</b>: ".$word_count."<br>";
print "<b>Slikti</b>: ".$bad_word_count."<br>";
print "<b>Unikāli</b>: ".$unique_word_count."<br><br>";

print "<form action='change_class.php' method='post'>";
print "<input name=change_to value=\"reset\" type=hidden>";
print "<input type=\"submit\" class='btn  btn-primary' value='Reset'><br><hr>";
print "</form>\n\n";
?>


    
<?php
$cn = 0;
$xml = simplexml_load_file("top.xml") or die("Error!");
foreach($xml->children()->children() as $data)
{
    //print $data;
    classify($data);
    print "<hr>";
}
 


?>




</div>
</body>
</html>