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
    
    $query = "SELECT * FROM users WHERE name='".$username."' AND password='".$password."'";
    $result = mysql_query($query) or die(mysql_error());
    
    $num_rows = mysql_num_rows($result);
    
    if($num_rows > 0)
    {
        session_start();
        $_SESSION['logged_in'] = "1";
        $_SESSION['username'] = $username;
        header ("Location: main.php");
    }
    else
    {
        print "Error!";
    }
}
else
{

?>
    

<form class="form-horizontal" method="post" action="signin.php">
<fieldset>

<!-- Form Name -->
<legend>Autorizācija</legend>

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
    
    <br>
    <a href=register.php>Reģistrēties</a>
  
  
</div>
</body>
</html>