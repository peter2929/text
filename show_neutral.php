<?php
session_start();

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

include 'top_menu.php';

print "<hr>";

$content = file_get_contents('neutral_data.txt');
$exploded_doc = explode('DELIMITER', $content);

for($i=0; $i<sizeof($exploded_doc); $i++)
{
        //print "<table style='width:20%' class='table table-bordered table-hover'>";
        //print "<th>Vārds</th><th>Neg.</th><th>Neitrals</th><th>Neg.</th><th>Neitrāls</th>";
    print $exploded_doc[$i]."<hr>";
}


?>
    
</div>
</body>
</html>