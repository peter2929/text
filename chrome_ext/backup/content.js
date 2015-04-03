var d = "";

function t(c)
{
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function()
	{
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
	    {
		f = xmlhttp.responseText.trim();
		f = f.split(' ');

		/*for(i=0; i<f.length; i++)
		{
			v = parseInt(f[i]);
			document.getElementsByClassName('message')[v].style.color = "red";
			document.getElementsByClassName('message')[i].innerHTML += "<br><input type=button value='dgsg'>";
		}*/

		var message_count = document.getElementsByClassName('message').length;
		var j = 0;
		for(i=0; i<message_count && j<f.length; i++)
		{
			v = parseInt(f[j]);
			if(i == v)
			{
				document.getElementsByClassName('message')[i].style.color = "red";
				//document.getElementsByClassName('message')[i].innerHTML += "<br><input type=button value='dgsg'>";
                                //document.getElementsByClassName('message')[i].innerHTML += "<br><input type=\"submit\" class='btn  btn-primary' value='Tomēr ir neitrāls'><br>";
				j++;
				
			}
			else
			{
				document.getElementsByClassName('message')[i].style.color = "green";
				//document.getElementsByClassName('message')[i].innerHTML += "<br><input type=button value='dgsg'>";
                                //document.getElementsByClassName('message')[i].innerHTML += "<br><input type=\"submit\" class='btn  btn-primary' value='Tomēr ir negatīvs'><br>";
			}
		}
	    }
        }
        xmlhttp.open("POST", "http://localhost/text/a.php", true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");


        xmlhttp.send("b="+c);
}



var s = document.getElementsByClassName('message').length;


for(i=0; i<s; i++)
{
	d += document.getElementsByClassName('message')[i].innerText;
	d += "DELIMITER";
}

if(s>0) t(d);

//alert("FINISHED");




