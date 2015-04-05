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
    public $index = array();
    //public $index['negative'] = array();//
    //public $index['neutral'] = array(); //
    public $word_count = array('negative' => 0, 'neutral' => 0);
    public $docs = array('negative' => 0, 'neutral' => 0);
    public $total_word_count = 0;
    public $unique_word_count = 0;


    public function add($document, $class)
    {
            global $morphy, $stopwords;
            $this->docs[$class]++;

            $words = $this->tokenize($document);
            for($m=0; $m<sizeof($words); $m++)
            {
                    $words[$m] = trim($words[$m]);
                    $words[$m] = mb_strtoupper($words[$m], 'UTF-8');
                    $base_form = $morphy->getBaseForm($words[$m]);

                    if($base_form[0] && !isset($stopwords[$base_form[0]]))  //  && !isset($stopwords[$base_form[0]])
                    {
                  //print $base_form[0]."---"; ////

                        $word = $base_form[0];
                        if(!isset($this->index[$class][$word]))
                        {
                            $this->index[$class][$word] = 0;
                            $this->unique_word_count++;
                        }

                        $this->index[$class][$word]++;
                        $this->word_count[$class]++;
                        $this->total_word_count++;
                    }
            }
    }


    public function classify($document)
    {
        global $morphy, $stopwords;
        $words = $this->tokenize($document);
        $prob_negative = 0;
        $prob_neutral = 0;

        ////print "<table style='width:20%' class='table table-bordered table-hover'>";
        ////print "<th>Vārds</th><th>Neg.</th><th>Neitrals</th><th>Neg.</th><th>Neitrāls</th>";
        for($m=0; $m<sizeof($words); $m++)
        {
                $words[$m] = trim($words[$m]);
                $words[$m] = mb_strtoupper($words[$m], 'UTF-8');
                $base_form = $morphy->getBaseForm($words[$m]);

                if($base_form[0] && !isset($stopwords[$base_form[0]]))
                {
                        $word = $base_form[0];
                        if(!isset($this->index['negative'][$word]))
                        {
                            $this->index['negative'][$word] = 0;
                        }
                        if(!isset($this->index['neutral'][$word]))
                        {
                            $this->index['neutral'][$word] = 0;
                        }

                        $numerator_negative = ($this->index['negative'][$word]+1) / ($this->word_count['negative'] + $this->unique_word_count);
                        $prob_negative += log($numerator_negative);

                        ////$count_neutral = $count_total - $count_bad;
                        $numerator_neutral = ($this->index['neutral'][$word]+1) / ($this->word_count['neutral'] + $this->unique_word_count);
                        $prob_neutral += log($numerator_neutral);

                        /////print "<tr><td>".$word."</td><td>".log($numerator_negative)."</td><td>".log($numerator_neutral)."</td><td>".$this->index['negative'][$word]."</td><td>".$this->index['neutral'][$word]."</td></tr>\n";
                }       /// count_bad    count_neutral
        }

        //-------- THE WORDS HAVE BEEN ANALYZED--------------------------------------------
        
        $prob_negative += log(0.2);
        $prob_neutral += log(0.8);

        ////print "</table><br>";

        if($prob_negative >= $prob_neutral)
        {
            ////print "<span style='color:#f00;'><b>Negatīvs!</b></span><br>";
           //// print "<a href='#' class='btn  btn-primary' role='button'>Tomer ir neitrals</a><br>";
           //// print "Neg prob: ".$prob_negative." NON neg prob ".$prob_neutral."  ";
            return "negative";
        }
        else
        {
            ////print "<span style='color:#0f0;'><b>Nav negatīvs!</span></b><br>";
           //// print "<a href='#' class='btn  btn-primary' role='button'>Tomer ir negativs</a><br>";
           //// print "Neg prob: ".$prob_negative." NON neg prob ".$prob_neutral."  ";
            return "neutral";
        }
    }
    
    public function tokenize($text)
    {
        $unwantedChars = array(',', '!', '?', '.', '(', ')', '=', '\n', '\r', '"', ':');
        $text = str_replace($unwantedChars, ' ', $text);
        $words = explode(' ', $text);
        return $words;
    }
    
    public function reset()
    {
        unset($this->index['negative']);
        unset($this->index['neutral']);
        $this->word_count['negative'] = 0;
        $this->word_count['neutral'] = 0;
        $this->docs['negative'] = 0;
        $this->docs['neutral'] = 0;
        $total_word_count = 0;
        $this->unique_word_count = 0;   
    }
}


?>