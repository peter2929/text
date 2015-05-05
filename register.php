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

if(isset($_POST['submit']))
{
   define('DB_HOST', 'localhost');
   define('DB_NAME', 'text');
   define('DB_USER','root');
   define('DB_PASSWORD','112233');   

    $username = $_POST['username'];
    $password = $_POST['password'];
 
    $con = mysql_connect(DB_HOST,DB_USER,DB_PASSWORD) or die("Failed to connect to MySQL: " . mysql_error());
    $db = mysql_select_db(DB_NAME,$con) or die("Failed to connect to MySQL: " . mysql_error());
    
    $query = "INSERT INTO users(name, password) VALUES('".$username."', '".$password."')";
    $data = mysql_query($query) or die(mysql_error());
    //mkdir("./users/....");
    
    if($data)
    {
        session_start();
        $_SESSION['logged_in'] = "1";
        $_SESSION['username'] = $username;
        print "Paldies par reģistrāciju!<br>";
        print "<a href=main.php>Uz galveno lapu</a>";
    }
}
else
{

?>
    

<form class="form-horizontal" method="post" action="register.php">
<fieldset>

<!-- Form Name -->
<legend>Jauna lietotāja reģistrācija</legend>

<!-- Text input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="textinput">Lietotājvārds</label>  
  <div class="col-md-4">
  <input id="textinput" name="username" type="text" class="form-control input-md">
  <span class="help-block"></span>  
  </div>
</div>

<!-- Password input-->
<div class="form-group">
  <label class="col-md-4 control-label" for="passwordinput">Parole</label>
  <div class="col-md-4">
    <input id="passwordinput" name="password" type="password" class="form-control input-md">
    <span class="help-block"></span>
  </div>
</div>

<!-- Button -->
<div class="form-group">
  <label class="col-md-4 control-label" for="singlebutton"></label>
  <div class="col-md-4">
    <!---<button id="singlebutton" name="singlebutton" class="btn btn-primary">Sūtīt</button> -->
    <input type="submit" id="singlebutton" name="submit" class="btn btn-primary" value="Sūtīt">
  </div>
</div>

</fieldset>
</form>

<?php

}

?>
  
  
</div>
</body>
</html>