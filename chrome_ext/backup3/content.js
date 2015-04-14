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

		var message_count = document.getElementsByClassName('message').length;
		for(i=0; i<message_count; i++)
		{  
                    if(f[i] == "negative")
                    {
                        document.getElementsByClassName('message')[i].style.color = "red";
                        document.getElementsByClassName('message')[i].innerHTML += "<br><input type=\"submit\" id='a_"+i+"' class='btn  btn-primary' value='Tomēr ir neitrāls'><br>";
                        document.getElementById('a_'+i).onclick = chl('a_'+i);
                        document.getElementsByClassName('message')[i].id = "a_"+i+"_b";
                    }
                    else if(f[i] == "neutral")
                    {
                        document.getElementsByClassName('message')[i].style.color = "green";
                        document.getElementsByClassName('message')[i].innerHTML += "<br><input type=\"submit\" id='a_"+i+"' class='btn  btn-primary' value='Tomēr ir negatīvs'><br>";
                        document.getElementById('a_'+i).onclick = chl('a_'+i);
                        document.getElementsByClassName('message')[i].id = "a_"+i+"_b";
                    }
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
//alert(xmlhttp2.responseText);
            }
        }
        xmlhttp2.open("POST", "http://localhost/text/b.php", true);
	xmlhttp2.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        var change_label_to;
        if(document.getElementById(h).value == 'Tomēr ir neitrāls') change_label_to = "neutral";
        else if(document.getElementById(h).value == 'Tomēr ir negatīvs') change_label_to = "negative";
        //alert(change_label_to);
        xmlhttp2.send("change_label_to="+change_label_to+"&b="+h);

alert(document.getElementById(h+'_b').innerHTML);
///if(typeof document.getElementById('a_0').value != 'undefined') document.getElementsByClassName('message')[0].innerHTML += "<br><input type=\"submit\" id='a_0' class='btn  btn-primary' value='Tomēr ir neitrāls'><br>";
        //if(document.getElementById(h).value == 'Tomēr ir neitrāls')
        //document.getElementById(h).value = 'afdfsfs';
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




