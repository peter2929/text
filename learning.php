<?php

error_reporting(E_ALL | E_STRICT);
include './phpmorphy/src/common.php';

?>

<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">

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

//$base_form = $morphy->getBaseForm("");
//print $base_form[0];
//print "<br><br>";

//---------------------------------------------------------------------------------------------

$index = array();
$word_count = 0;
$unique_word_count = 0;

function add($file)
{
        global $morphy, $index, $word_count, $unique_word_count;
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

                        //if($base_form[0] && (mb_strlen($base_form[0], 'UTF-8') > 3))
                        if($base_form[0])
                        {
                                $word = $base_form[0];
                                if(!isset($index[$word]))
                                {
                                    $index[$word] = 0;
                                    $unique_word_count++;
                                }
                                $index[$word]++;
                                $word_count++;
                        }
                }
        }
}

function classify($document)
{
    global $morphy, $index, $word_count, $unique_word_count;
    $unwantedChars = array(',', '!', '?', '.', '(', ')', '=', '\n', '\r', '"');
    $com3 = str_replace($unwantedChars, ' ', $document);

    $words = explode(' ', $com3);
    $prob = 1;
print "<table style='width:20%'>";
    for($m=0; $m<sizeof($words); $m++)
    {
            $words[$m] = trim($words[$m]);
            $words[$m] = mb_strtoupper($words[$m], 'UTF-8');

            $base_form = $morphy->getBaseForm($words[$m]);

            if($base_form[0])
            {
                    $word = $base_form[0];
                    if(!isset($index[$word]))
                    {
                        $count = 0;
                        /////print $word."------------------------<br>";
                    }
                    else $count = $index[$word];

                    //$prob += log(($count + 1) / ($word_count + $word_count));
                    //print "<tr><td>".$word."</td><td>".log(($count + 1) / ($word_count + $word_count))."</td></tr>";
                    
                    if($count > 0)
                    {
                        $prob += log($count / $word_count); ////
                        print "<tr><td>".$word."</td><td>".log($count / $word_count)."</td></tr>"; ////
                    }
            }
    }
print "</table><br>";
    return $prob;
    
}


print "<table style='width:50%'>";

add("training_data.txt");
arsort($index);
foreach($index as $key => $b)
{
    print "<tr><td style='width:10%'>".$key."</td><td>".$b."</td><td>".log($b/$word_count)."</td></tr>";
}

print "</table><br>";
print "<b>Kopā</b>: ".$word_count."<br>";
print "<b>Unikāli</b>: ".$unique_word_count."<br><br><br><hr>";

print "Хоть вера не позволяет матерится, но в этот раз скажу иди нахуй господин парашенко.<br>";
print classify("Хоть вера не позволяет матерится, но в этот раз скажу иди HАXУЙ господин парашенко.")."<br>";
print "<hr>";

print "Вашему вниманию очередное кино кинокомпании наливайченко-продакшн. Как говорил Станиславский: не верю!"."<br>";
print classify("Вашему вниманию очередное кино кинокомпании наливайченко-продакшн. Как говорил Станиславский: не верю!")."<br>";
print "<hr>";

print ("рашка безнадежна. нормальным людям остаётся только уехать﻿")."<br>";
print classify("рашка безнадежна. нормальным людям остаётся только уехать﻿")."<br>";
print "<hr>";

print "не будет никакой гааги, хотя потрошенко яйценюх торчок и компания заслуживают, сдохнешь и ты и я, и весь мир, вы же блять укропы этого хотели? хотели третьей мировой и конца света? началось... и знаешь мразь, мне нехуя вас людей не жалко, потому что вы нелюди, зверьё﻿"."<br>";
print classify("не будет никакой гааги, хотя потрошенко яйценюх торчок и компания заслуживают, сдохнешь и ты и я, и весь мир, вы же блять укропы этого хотели? хотели третьей мировой и конца света? началось... и знаешь мразь, мне нехуя вас людей не жалко, потому что вы нелюди, зверьё﻿")."<br>";
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
print classify("люди, из-за этих блядей майданутых мы все умрем, укропы, что вы за мрази-то такие, сука я вас даже когда умру проклинать буду, вы уёбки со своей говяной майданутой революцией не только себе хуже сделали, вы половине мира поднасрали. скорей бы уж разъебали землю ядерными ракетами, просто противно жить на одной планете с уёбкаи﻿")."<br>";


?>





</body>
</html>