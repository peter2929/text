<?php

error_reporting(E_ALL | E_STRICT);
include './phpmorphy/src/common.php';
//ini_set('default_charset', 'utf-8')
///mb_internal_encoding('UTF-8');
?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
    
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

$base_form = $morphy->getBaseForm("");
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
    global $morphy, $total_index, $bad_index, $word_count, $bad_word_count, $unique_word_count, $stopwords;
    $denominator = 0;
    $unwantedChars = array(',', '!', '?', '.', '(', ')', '=', '\n', '\r', '"', ':');
    $com3 = str_replace($unwantedChars, ' ', $document);

    $words = explode(' ', $com3);
    $prob = 0;
    $prob2 = 0;
    $neutral_word_count = $word_count - $bad_word_count;
    
    print "<table style='width:20%'>";
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
                        $denominator = ($count_total+1) / ($word_count+$unique_word_count);
                        ///$denominator++;
                        $numerator = ($count_bad+1) / ($bad_word_count+$unique_word_count);
                        $prob += log($numerator/$denominator);
                        ///$prob += log($numerator);
                        $neutral_occurrence = $count_total - $count_bad;

                        $numerator2 = ($neutral_occurrence+1) / ($neutral_word_count+$unique_word_count);
                        $prob2 += log($numerator2/$denominator);
                        ///$prob2 += log($numerator2);
                        print "<tr><td>".$word."</td><td>".log($numerator/$denominator)."</td><td>".log($numerator2/$denominator)."</td><td>".$count_bad."</td></tr>"; ////
                    //}
            }
            ////
    }
    
    $prob += log($bad_word_count / $word_count);
    $prob2 += log($neutral_word_count / $word_count);

print "</table><br>";

if($prob > $prob2) print "<b>Negativs!</b><br>";
else print "<b>Nav negativs!</b><br>";

print "Neg index: ".$prob." NON neg index ".$prob2."  ";
return "";

}


add("training_data.txt");

//print "<table style='width:50%'>";
//arsort($total_index);
//print "</table><br>";

print "<b>Kopā</b>: ".$word_count."<br>";
print "<b>Slikti</b>: ".$bad_word_count."<br>";
print "<b>Unikāli</b>: ".$unique_word_count."<br><br><br><hr>";

print "Хоть вера не позволяет матерится, но в этот раз скажу иди нахуй господин парашенко.<br>";
print classify("Хоть вера не позволяет матерится, но в этот раз скажу иди нахуй господин парашенко.")."<br>";
print "<hr>";

print "Вашему вниманию очередное кино кинокомпании наливайченко-продакшн. Как говорил Станиславский: не верю!"."<br>";
print classify("Вашему вниманию очередное кино кинокомпании наливайченко-продакшн. Как говорил Станиславский: не верю!")."<br>";
print "<hr>";

print ("рашка безнадежна. нормальным людям остаётся только уехать﻿")."<br>";
print classify("рашка безнадежна. нормальным людям остаётся только уехать")."<br>";
print "<hr>";

print "не будет никакой гааги, хотя потрошенко яйценюх торчок и компания заслуживают, сдохнешь и ты и я, и весь мир, вы же блять укропы этого хотели? хотели третьей мировой и конца света? началось... и знаешь мразь, мне нехуя вас людей не жалко, потому что вы нелюди, зверьё﻿"."<br>";
print classify("не будет никакой гааги, хотя потрошенко яйценюх торчок и компания заслуживают, сдохнешь и ты и я, и весь мир, вы же блять укропы этого хотели? хотели третьей мировой и конца света? началось... и знаешь мразь, мне нехуя вас людей не жалко, потому что вы нелюди, зверьё")."<br>";
print "<hr>";

print "придурок, да срать России и всему миру на твою укропию, если ты это не понял))) а так конечно, повторяй мантры скоро, весь мир за нас рассиюпабедим"."<br>";
print classify("придурок, да срать России и всему миру на твою укропию, если ты это не понял))) а так конечно, повторяй мантры скоро, весь мир за нас рассиюпабедим")."<br>";
print "<hr>";

print ("долбоеб.. из-за русни никто не будет вписываться.. какая нахуй третья мировая.. долбоеб вы с голода подохните )﻿")."<br>";
print classify("долбоеб.. из-за русни никто не будет вписываться.. какая нахуй третья мировая.. долбоеб вы с голода подохните )﻿")."<br>";
print "<hr>";

print ("типичный борець руцкого мира (укуренный быдло даун...) ставит мир раком от лица росии (самой чмошной и фашысткой стране в мире на сегодняшный день) где каждый журналист (ну вы сами поняли.... журналист крайне оптимистически звучит, скоро путинодрочер киселёв будет гафкать как сука из ГАФнонюс...) будет унижать ничтожную омерику! (ведь омерика ничё не сделает, подумаеш всегото отключит свифт, интернет... зато гавняшвяш, это все расеи на пользу, как завещал великое хуйло всея мордора хуйло педофило путин)))))﻿")."<br>";
print classify("типичный борець руцкого мира (укуренный быдло даун...) ставит мир раком от лица росии (самой чмошной и фашысткой стране в мире на сегодняшный день) где каждый журналист (ну вы сами поняли.... журналист крайне оптимистически звучит, скоро путинодрочер киселёв будет гафкать как сука из ГАФнонюс...) будет унижать ничтожную омерику! (ведь омерика ничё не сделает, подумаеш всегото отключит свифт, интернет... зато гавняшвяш, это все расеи на пользу, как завещал великое хуйло всея мордора хуйло педофило путин)))))﻿")."<br>";
print "<hr>";

print ("Оххх у хохлов как попка пригорает , хорошее зрелище )) А Дмитрий молодцом )) Его аналитические репортажи сочетают в себе хорошую аргументацию и внятную , логическую риторику , это хохлам как как палка поперек горла . Ничего противопоставить не могут , вот и начинается бессильная злоба . Мелкие людишки .")."<br>";
print classify("Оххх у хохлов как попка пригорает , хорошее зрелище )) А Дмитрий молодцом )) Его аналитические репортажи сочетают в себе хорошую аргументацию и внятную , логическую риторику , это хохлам как как палка поперек горла . Ничего противопоставить не могут , вот и начинается бессильная злоба . Мелкие людишки .")."<br>";
print "<hr>";

print ("люди, из-за этих блядей майданутых мы все умрем, укропы, что вы за мрази-то такие, сука я вас даже когда умру проклинать буду, вы уёбки со своей говяной майданутой революцией не только себе хуже сделали, вы половине мира поднасрали. скорей бы уж разъебали землю ядерными ракетами, просто противно жить на одной планете с уёбкаи﻿")."<br>";
print classify("люди, из-за этих блядей майданутых мы все умрем, укропы, что вы за мрази-то такие, сука я вас даже когда умру проклинать буду, вы уёбки со своей говяной майданутой революцией не только себе хуже сделали, вы половине мира поднасрали. скорей бы уж разъебали землю ядерными ракетами, просто противно жить на одной планете с уёбками")."<br>";
print "<hr>";

print("Я переживаю вместе и выражаю глубогое соболезнование всем жителям Донбасса, чьи дети и родители поколечены и убиты украинскими насильниками и фашистами при поддержке таких, как немцов.");
classify("Я переживаю вместе и выражаю глубогое соболезнование всем жителям Донбасса, чьи дети и родители поколечены и убиты украинскими насильниками и фашистами при поддержке таких, как немцов.");
print "<hr>";

print("жму руку, крепитесь и лечитесь");
classify("жму руку, крепитесь и лечитесь");
print "<hr>";

print("Твой комментарий великое благо не только для России, но и для Вселенной!");
classify("Твой комментарий великое благо не только для России, но и для Вселенной!");
print "<hr>";

print("Еще до начала церемонии у центра выстроилась большая очередь желающих проститься с убитым политиком, с цветами и зажженными свечами в руках, сообщает находящийся там журналист");
classify("Еще до начала церемонии у центра выстроилась большая очередь желающих проститься с убитым политиком, с цветами и зажженными свечами в руках, сообщает находящийся там журналист");

print "<hr>";

?>





</body>
</html>