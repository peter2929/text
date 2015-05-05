<?php

if(isset($_SESSION['logged_in']))
{
    print "<h1> Sveiki, ".$_SESSION['username']."!</h1><br>";
    print "<a href=show_negative.php>Apskatīt negatīvos komentārus</a> <br>";
    print "<a href=show_neutral.php>Apskatīt neitrālos komentārus</a> <br>";
    print "<a href=cross-val.php>Apskatīt šķērsvalidācijas rezultātus</a> <br>";
    print "<a href=logout.php>Iziet</a> <br>";
}


?>