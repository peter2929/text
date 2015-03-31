
//var res = "";

function t(c, num)
{
        var xmlhttp = new XMLHttpRequest();
        xmlhttp.onreadystatechange = function()
	{
            if (xmlhttp.readyState == 4 && xmlhttp.status == 200)
	    {
		/////alert(xmlhttp.responseText);
		///document.getElementsByClassName('message')[num].style.color = "red";

		if(xmlhttp.responseText == "0") document.getElementsByClassName('message')[num].style.color = "red";
	    }
        }
        xmlhttp.open("POST", "http://localhost/text/chrome_ext/a.php", true);
	xmlhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");


        xmlhttp.send("b="+c);
}



alert(document.getElementsByClassName('message').length);
var s = document.getElementsByClassName('message').length;



for(i=0; i<s; i++)
{
	//a = document.getElementsByClassName('message')[i].innerText;

	t(document.getElementsByClassName('message')[i].innerText, i);
	//if(res != "") document.getElementsByClassName('message')[i].style.color = "red";
	//alert(res);
	//document.getElementsByClassName('message')[i].innerText = "<a href=cnn.com>afsdfsdffdg</a>";
	//document.getElementsByClassName('message')[i].style.color = "red";

	//alert(a);
}





