<?php

error_reporting(E_ALL | E_STRICT);
include './phpmorphy/src/common.php';

$opts = array(
	'storage' => PHPMORPHY_STORAGE_FILE,
	'with_gramtab' => false,
	'predict_by_suffix' => true, 
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

///print $morphy->getBaseForm("НАТО")[0];

//---------------------------------------END OF PHPMORPHY--------------------------------------------------------

$stopwords = array();
$fh = fopen('stop-words.txt', 'r');
while($line = fgets($fh))
{
    $trimmed_line = trim($line);
    $trimmed_line = mb_strtoupper($trimmed_line, 'UTF-8');
    $base_form = $morphy->getBaseForm($trimmed_line);  /////
    $stopwords[$base_form[0]] = 1;
}

//---------------------------------------END OF STOPWORDS--------------------------------------------------------

class sentiments
{
    public $total_index = array();
    public $bad_index = array();
    public $word_count = 0;
    public $bad_word_count = 0;
    public $unique_word_count = 0;
    public $doc_negative = 0;
    public $doc_neutral = 0;

    public function add($file)
    {
            global $morphy, $stopwords;
            $file_content = file_get_contents($file);
            $texts = explode('DELIMITER', $file_content);
            $this->doc_negative = sizeof($texts);

            for($i=0; $i<sizeof($texts); $i++)
            {
               /// print "<hr>"; ////
                    $words = $this->tokenize($texts[$i]);
                    for($m=0; $m<sizeof($words); $m++)
                    {
                            $words[$m] = trim($words[$m]);
                            $words[$m] = mb_strtoupper($words[$m], 'UTF-8');
                            $base_form = $morphy->getBaseForm($words[$m]);

                            if($base_form[0])
                            {
                              ///  print $base_form[0]."---"; ////

                                    $word = $base_form[0];
                                    if(!isset($this->total_index[$word]))
                                    {
                                        $this->total_index[$word] = 0;
                                        $this->bad_index[$word] = 0;
                                        $this->unique_word_count++;
                                    }

                                    $this->total_index[$word]++;
                                    $this->bad_index[$word]++;
                                    $this->word_count++;
                            }
                    }
            }

            $this->bad_word_count = $this->word_count;

            //---------------------------------------------------------------
            $file_content = file_get_contents("neutral_data.txt");
            $texts = explode('DELIMITER', $file_content);
            $this->doc_neutral = sizeof($texts);
            ////////////print sizeof($texts)."<hr>";
            for($i=0; $i<sizeof($texts); $i++)
            {
                $words = $this->tokenize($texts[$i]);

                for($m=0; $m<sizeof($words); $m++)
                {
                    $words[$m] = trim($words[$m]);
                    $words[$m] = mb_strtoupper($words[$m], 'UTF-8');
                    $base_form = $morphy->getBaseForm($words[$m]);

                    if($base_form[0])
                    {
                            $word = $base_form[0];
                            if(!isset($this->total_index[$word]))
                            {
                                $this->total_index[$word] = 0;
                                $this->bad_index[$word] = 0;
                                $this->unique_word_count++;
                            }

                            $this->total_index[$word]++;
                            $this->word_count++;
                    }
                }
            }

    }


    public function classify($document)
    {
        global $morphy, $stopwords;
        $words = $this->tokenize($document);
        $prob_negative = 0;
        $prob_neutral = 0;
        $neutral_word_count = $this->word_count - $this->bad_word_count;

        print "<table style='width:20%'  class='table table-bordered table-hover'>";
        print "<th>Vards</th><th>Neg.</th><th>Neitrals</th><th>Neg.</th><th>Neitrals</th>";
        for($m=0; $m<sizeof($words); $m++)
        {
                $words[$m] = trim($words[$m]);
                $words[$m] = mb_strtoupper($words[$m], 'UTF-8');
                $base_form = $morphy->getBaseForm($words[$m]);

                if($base_form[0] && !isset($stopwords[$base_form[0]]))
                {
                        $word = $base_form[0];
                        if(!isset($this->total_index[$word]))
                        {
                            $count_total = 0;
                            $count_bad = 0;
                        }
                        else
                        {
                            $count_total = $this->total_index[$word];
                            if(!isset($this->bad_index[$word]))
                                $count_bad = 0;
                            else
                                $count_bad = $this->bad_index[$word];
                        }


                        $numerator_negative = ($count_bad+1) / ($this->bad_word_count + $this->unique_word_count);
                        $prob_negative += log($numerator_negative);

                        $count_neutral = $count_total - $count_bad;
                        $numerator_neutral = ($count_neutral+1) / ($neutral_word_count + $this->unique_word_count);
                        $prob_neutral += log($numerator_neutral);

                        print "<tr><td>".$word."</td><td>".log($numerator_negative)."</td><td>".log($numerator_neutral)."</td><td>".$count_bad."</td><td>".$count_neutral."</td></tr>";
                }
        }

        //-------- THE WORDS HAVE BEEN ANALYZED--------------------------------------------
        
        $prob_negative += log(0.2);
        $prob_neutral += log(1);

        print "</table><br>";

        if($prob_negative >= $prob_neutral)
        {
            print "<span style='color:#f00;'><b>Negatīvs!</b></span><br>";
            print "<a href='#' class='btn  btn-primary' role='button'>Tomer ir neitrals</a><br>";
        }
        else
        {
            print "<span style='color:#0f0;'><b>Nav negatīvs!</span></b><br>";
            print "<a href='#' class='btn  btn-primary' role='button'>Tomer ir negativs</a><br>";
        }

        print "Neg prob: ".$prob_negative." NON neg prob ".$prob_neutral."  ";
        //return "";

    }
    
    public function tokenize($text)
    {
        $unwantedChars = array(',', '!', '?', '.', '(', ')', '=', '\n', '\r', '"', ':');
        $text = str_replace($unwantedChars, ' ', $text);
        $words = explode(' ', $text);
        return $words;
    }
}


?>