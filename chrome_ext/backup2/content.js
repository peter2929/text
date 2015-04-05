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

		//for(i=0; i<f.length; i++)
		//{
		//	v = parseInt(f[i]);
		//	document.getElementsByClassName('message')[v].style.color = "red";
		//	document.getElementsByClassName('message')[i].innerHTML += "<br><input type=button value='dgsg'>";
		//}

		var message_count = document.getElementsByClassName('message').length;
		var j = 0;
		for(i=0; i<message_count; i++)
		{
                    if(j<f.length)
                    {
			v = parseInt(f[j]);
			if(i == v)
			{
                            document.getElementsByClassName('message')[i].style.color = "red";
                            //document.getElementsByClassName('message')[i].innerHTML += "<br><input type=button value='dgsg'>";
                            document.getElementsByClassName('message')[i].innerHTML += "<br><input type=\"submit\" id='a_"+i+"' class='btn  btn-primary' value='Tomēr ir neitrāls'><br>";
                            document.getElementById('a_'+i).onclick = chl('a_'+i);
                            j++;
                            continue;
			}
                    }
                    
                    document.getElementsByClassName('message')[i].style.color = "green";
                    //document.getElementsByClassName('message')[i].innerHTML += "<br><input type=button value='dgsg'>";
                    document.getElementsByClassName('message')[i].innerHTML += "<br><input type=\"submit\" id='a_"+i+"' class='btn  btn-primary' value='Tomēr ir negatīvs'><br>";
                    document.getElementById('a_'+i).onclick = chl('a_'+i); ///
		}
	    }
        }
        xmlhttp.open("POST", "http://localhost/text/a.php", true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp.send("b="+c);
}

function chl(h)
{
    return function()
    {
        var xmlhttp2 = new XMLHttpRequest();
        xmlhttp2.onreadystatechange = function()
        {
            if(xmlhttp2.readyState == 4 && xmlhttp2.status == 200)
            {
alert(xmlhttp2.responseText);
            }
        }
        xmlhttp2.open("POST", "http://localhost/text/b.php", true);
	xmlhttp2.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xmlhttp2.send("b="+h);

        if(document.getElementById(h).value == 'Tomēr ir neitrāls')
        document.getElementById(h).value = 'afdfsfs';
    }
}

var s = document.getElementsByClassName('message').length;


for(i=0; i<s; i++)
{
	d += document.getElementsByClassName('message')[i].innerText;
	d += "DELIMITER";
}

if(s>0) t(d);

//alert("FINISHED");




