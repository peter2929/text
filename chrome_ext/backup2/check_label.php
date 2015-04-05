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

$op = new sentiments();

$file_content = file_get_contents("training_data.txt");
$texts = explode('DELIMITER', $file_content);

for($i=0; $i<sizeof($texts); $i++)
{
    $op->add($texts[$i], "negative");
}

$file_content = file_get_contents("neutral_data.txt");
$texts = explode('DELIMITER', $file_content);


for($i=0; $i<sizeof($texts); $i++)
{
    $op->add($texts[$i], "neutral");
}

print "<b>Kopā</b>: ".$op->total_word_count."<br>";
print "<b>Slikti</b>: ".$op->word_count['negative']."<br>";
print "<b>Neitrāli</b>: ".$op->word_count['neutral']."<br>";
print "<b>Unikāli</b>: ".$op->unique_word_count."<br><hr>";


if(isset($_POST['comment']))
{
    print $_POST['comment']."<br>";
    print $op->classify($_POST['comment'])."<br>";
    print "<hr>";
}

print "Хоть вера не позволяет матерится, но в этот раз скажу иди нахуй господин парашенко.<br>";
print $op->classify("Хоть вера не позволяет матерится, но в этот раз скажу иди нахуй господин парашенко.")."<br>";
print "<hr>";

print "Вашему вниманию очередное кино кинокомпании наливайченко-продакшн. Как говорил Станиславский: не верю!"."<br>";
print $op->classify("Вашему вниманию очередное кино кинокомпании наливайченко-продакшн. Как говорил Станиславский: не верю!")."<br>";
print "<hr>";     

?>

<form method="post" action="check_label.php">
<input name=comment size=120>
<input type=submit>
</form>

</div>
</body>
</html>