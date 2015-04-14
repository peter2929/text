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
<div class="container-fluid">    

<?php

//---------------------------------------------------------------------------------------------

$var = new sentiments();

$file_content = file_get_contents("training_data.txt");
$texts = explode('DELIMITER', $file_content);

for($i=0; $i<sizeof($texts); $i++)
{
    $var->add($texts[$i], "negative");
}

$file_content = file_get_contents("neutral_data.txt");
$texts = explode('DELIMITER', $file_content);

/*
for($i=0; $i<sizeof($texts); $i++)
{
    $var->add($texts[$i], "neutral");
}
*/

$var->learn_from();

/*
foreach($var->index['negative'] as $key => $value)
{
    //print $key."<br>";
}
*/
//$var->save_to();

//print "<table style='width:50%'>";
//arsort($total_index);
//print "</table><br>";

print "<b>Kopā</b>: ".$var->total_word_count."<br>";
print "<b>Slikti</b>: ".$var->word_count['negative']."<br>";
print "<b>Unikāli</b>: ".$var->unique_word_count."<br><hr>";

print "Хоть вера не позволяет матерится, но в этот раз скажу иди нахуй господин парашенко.<br>";
print $var->classify("Хоть вера не позволяет матерится, но в этот раз скажу иди нахуй господин парашенко.")."<br>";
print "<hr>";

print "Вашему вниманию очередное кино кинокомпании наливайченко-продакшн. Как говорил Станиславский: не верю!"."<br>";
print $var->classify("Вашему вниманию очередное кино кинокомпании наливайченко-продакшн. Как говорил Станиславский: не верю!")."<br>";
print "<hr>";

print ("рашка безнадежна. нормальным людям остаётся только уехать﻿")."<br>";
print $var->classify("рашка безнадежна. нормальным людям остаётся только уехать")."<br>";
print "<hr>";


print $var->classify("Слава США! Слава НАТО!
Можете вы колорадско навозные св-ньи хоть волками выть и усраться по уши а НАТОвским базочкам и ядерному щиточку в Латвии БЫТЬ!
Ядерные ракетки средненькой дальности действия должны размещаться исходя из расчетика - одна 150-мегатонная боеголовочка на 100 квадратных километриков вражеской територии!
Уж поверьте - колорадско навозные уб-удки будут сидеть в дрме в параше носы не высовывая!")."<br>";
print "<hr>";
        
/*
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

print("Лстить не надо. Киров 2, не смешите.");
classify("Лстить не надо. Киров 2, не смешите.");
*/


?>




</div>
</body>
</html>