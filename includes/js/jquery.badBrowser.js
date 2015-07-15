function badBrowser(){
	if($.browser.msie && parseInt($.browser.version) <= 8){ return true;}
	
	return false;
}

function getBadBrowser(c_name)
{
	if (document.cookie.length>0)
	{
	c_start=document.cookie.indexOf(c_name + "=");
	if (c_start!=-1)
		{ 
		c_start=c_start + c_name.length+1; 
		c_end=document.cookie.indexOf(";",c_start);
		if (c_end==-1) c_end=document.cookie.length;
		return unescape(document.cookie.substring(c_start,c_end));
		} 
	}
	return "";
}	

function setBadBrowser(c_name,value,expiredays)
{
	var exdate=new Date();
	exdate.setDate(exdate.getDate()+expiredays);
	document.cookie=c_name+ "=" +escape(value) + ((expiredays==null) ? "" : ";expires="+exdate.toGMTString());
}
if(badBrowser()){
	window.location =  "http://www.yakimawa.gov/contact/yakback/";
}
/*
if(badBrowser()){
	$(function(){
		var text_en = [
			"We're sorry. Yak Back will not work with this version of Internet Explorer.",
			"For a better browsing experience, you can install <a href='http://www.mozilla.org' style='color:white'>Firefox</a>, <a href='http://www.google.com/chrome' style='color:white'>Chrome</a>, or <a href='http://www.apple.com/safari/' style='color:white'>Safari</a>.",
			"Please use the <a href='/contact/yakback/' style='color:white'>Yak Back Simple Form</a> to contact the City of Yakima",
			"" ];

	var text = text_en;

	var base = "";
	
	function create_warning() {
				var markup = "<div style='margin: 10% 20%;'><h1 style='color:red;'>" + text[0] + "</h1><p style='color:white;'>" + text[1] + "</p><p style='color:white;'>" + text[2] + "</p><div style='text-align: center'>";
		

				var div = document.createElement("div");
				div.setAttribute("id", "old-browser-warning");
				//Ouch... setAttribute("style", ...) does not work in IE < 8 
				div.style.top = div.style.left = div.style.margin = div.style.right = div.style.bottom = "0";
				div.style.backgroundImage = "url('" + base + "/images/opacity80.png')";
				div.style.position = "fixed";
				div.style.overflow = "auto";
				div.style.fontFamily="Helvetica, Arial";
				div.style.zIndex = "2147483647";
				
				if (/MSIE 6/i.test(navigator.userAgent)) { //Ouch... fixed position does not work in IE < 7
					div.style.position = "absolute";
					div.style.width = "100%";
					function max(a,b) { return a > b ? a : b; }
					div.style.height = max(document.body.scrollHeight, document.body.clientHeight) + "px";
				}
				
				div.innerHTML = markup;
				document.body.appendChild(div);
			}
		
			var old_onload = window.onload;
			window.onload = typeof old_onload === 'function' ? function() {
				create_warning();
				old_onload();
			} : create_warning;
	});	
}
*/