//Hunter v. 2.0 fixed for round 60 by fusor, magnusnet@gmail.com original work by: unknown op dev guy.
//Since there where comments when I got the code, my personal comments as of v 2.0 are prefixed by 'MAGNUS:'
//This version is BETA and SHOULD NOT BE USED FOR S2, database will be corrupted if it's used for S2.
_skhunterVersion=20;

var xmlhttp;
var targetDoc;
var UI;
var data;


//there is no tbody ?? rightnow
function getSectorTable(documentBody,trIndex,tdIndex,TextFlagRegex){
	var tables=documentBody.getElementsByTagName("table");
        var tryTable = null;
        var sectorTableNotFound = true;
        var j = 0;
        while(j<tables.length && sectorTableNotFound) {
           if(tables[j].getElementsByTagName("tbody")[0]){
             tryTable = tables[j].getElementsByTagName("tbody")[0];
           } else {
	     tryTable = tables[j];
 	   }
           var tryRow = tryTable.getElementsByTagName("tr")[trIndex];
	   if(tryRow){
	     var tryTD = tryRow.getElementsByTagName("td")[tdIndex];
             if(tryTD && tryTD.innerHTML.match(TextFlagRegex)){
              sectorTableNotFound = false;
             }
	   }
           j++;
        }
        return tryTable;
}

function getSectorTable_(documentBody,trIndex,tdIndex,TextFlagRegex){
	var tables=documentBody.getElementsByTagName("table");

        var tryTable = null;
        var sectorTableNotFound = true;
        var j = 0;
        while(j<tables.length && sectorTableNotFound) {
			if(tables[j].getElementsByTagName("tbody")[0]){
				tryTable = tables[j].getElementsByTagName("tbody")[0];
			} else {
				tryTable = tables[j];
			}
			var tryRow = tryTable.getElementsByTagName("tr")[trIndex];
			if(tryRow){
				var tryTD = tryRow.getElementsByTagName("td")[tdIndex];
				if(tryTD && tryTD.innerHTML.match(TextFlagRegex)){
					sectorTableNotFound = false;
				}
			}
			j++;
        }
		if (sectorTableNotFound == true) return null;
        return tryTable;
}


 function makeReport(event){
	
	//var data = self.data;
	var timeIndex = UI.value;
	
	eval(data);
	var ITarget = event.originalTarget.parentNode.parentNode.parentNode.parentNode;

	//documentB=event.originalTarget.getElementsByTagName("body")[0];
	
      //var ITarget = getSectorTable_(documentB,1,0,/^Kingdom Name/);
	  
	var slotsTarget=ITarget.getElementsByTagName("tr");	
	//alert(slotsTarget[3].innerHTML);
	for(i=0;i<slotsNR;i++){
		var tds=slotsTarget[i+2].getElementsByTagName("td");
	if ((0 <= timeIndex) && (timeIndex < times.length)){

		Land = ""
		if (landchange[timeIndex][i] != 0){
			if ( landchange[timeIndex][i] > 0) {
				Land += " +"+landchange[timeIndex][i];
				tds[3].style.color="#22FF22";
			} else {
				Land += landchange[timeIndex][i];
				tds[3].style.color="#FF2222";
			}
			tds[3].lastChild.nodeValue = Land;
		} else {
			tds[3].lastChild.nodeValue = "";
		}
		NW = ""
		if (nwchange[timeIndex][i] != 0){
			if ( nwchange[timeIndex][i] > 0) {
				NW += " +"+nwchange[timeIndex][i];
				tds[5].style.color="#22FF22";
			} else {
				NW += nwchange[timeIndex][i];
				tds[5].style.color="#FF2222";
			}
			tds[5].lastChild.nodeValue = NW;
		} else {
			tds[5].lastChild.nodeValue = "";
		}

		Honour = ""
		if (hchange[timeIndex][i] != 0){
			if ( hchange[timeIndex][i] > 0) {
				Honour += " +"+hchange[timeIndex][i];
				tds[7].style.color="#22FF22";
			} else {
				Honour += hchange[timeIndex][i];
				tds[7].style.color="#FF2222";
			}
			tds[7].lastChild.nodeValue = Honour;
		} else {
			tds[7].lastChild.nodeValue = "";
		}

	} else {
		tds[3].lastChild.nodeValue = "";
		tds[5].lastChild.nodeValue = "";
		tds[7].lastChild.nodeValue = "";
	}
	}
//alert(data);

}

function evalData(event) {

if(xmlhttp.readyState==4) {


	if(xmlhttp.getResponseHeader("Content-Type") != "text/plain") {
		targetDoc.getElementById("serverMessageTD").lastChild.nodeValue="SK Hunter: No response from server.";
		times=null;
		//alert(xmlhttp.responseText);
		return;
	}
	
	if(xmlhttp.responseText.match(/\/\*MAINT\*\//)) {
		targetDoc.getElementById("serverMessageTD").lastChild.nodeValue="SK Hunter Server Response: server is in maintainance.";
		times=null;
		return;
	}	

	if(xmlhttp.responseText.match(/\/\*VERSION\*\//)) {
		targetDoc.getElementById("serverMessageTD").lastChild.nodeValue="SK Hunter Server Response: Please upgrade your Hunter extension.";
		times=null;
		return;
	}

	if(xmlhttp.responseText.match(/\/\*ERROR\*\//)) {
		targetDoc.getElementById("serverMessageTD").lastChild.nodeValue="SK Hunter Server Response:" + xmlhttp.responseText;
		throw "SK HUNTER server response:"+xmlhttp.responseText;
		times=null;
		return;
	}

	if(xmlhttp.responseText.match(/\/\*DROP\*\//)) {
		targetDoc.getElementById("serverMessageTD").lastChild.nodeValue="SK Hunter Server Response: No data for this sector.";
		times=null;
		return;
	}

	if(!xmlhttp.responseText.match(/\/\*OK\*\//)) {
		targetDoc.getElementById("serverMessageTD").lastChild.nodeValue="SK Hunter Server Response: Your request failed.";
		times=null;
		return;
	}
	var firstChar = xmlhttp.responseText.indexOf("/*OK*/") + 6;
	var lastChar = xmlhttp.responseText.indexOf("/*EOT*/");
	var msgEv = xmlhttp.responseText.substring(firstChar, lastChar);
	//alert(msgEv);
	
        try {
          eval(msgEv);
        } catch(x) {
          targetDoc.getElementById("serverMessageTD").lastChild.nodeValue="SK HUNTER server response does not make sense. See Javascript Error Console.";
          throw "SK HUNTER server response is jibberish. Forced ads might have been injected or your PHP script gets filtered by host.";
		  
          return;
        }
//alert(landchange[1][1]);
	//display server message
	if(srvmsg){
		var tdM = targetDoc.getElementById("serverMessageTD");
		if(srvmsg_link){
                   var Aelem = targetDoc.createElement("a");
                   Aelem.setAttribute("href",srvmsg_link);
                   if(srvmsg_color)Aelem.style.color=srvmsg_color;
                   var AelemT = targetDoc.createTextNode(srvmsg);

                   Aelem.appendChild(AelemT);
                   tdM.replaceChild(Aelem,tdM.lastChild);
                   
                } else {
		  if(srvmsg_color)tdM.style.color=srvmsg_color;
		  tdM.lastChild.nodeValue=srvmsg;
                }
	}


	//add times to selection element
	//UI=targetDoc.getElementById("timeline");
	order=Components.classes["@mozilla.org/preferences-service;1"].
		getService(Components.interfaces.nsIPrefService).
		getBranch("extensions.skhunter.").getBoolPref('order');
	if(order){
	//ascending
	for (i=times.length-1;i>=0;i--){
		s = targetDoc.createElement("option");
		s.setAttribute("value",i);
		
		t = targetDoc.createTextNode(times[i]);
		s.appendChild(t);
		UI.appendChild(s);
	}
	} else {
	//descending
	for (i=0;i<times.length;i++){
		s = targetDoc.createElement("option");
		s.setAttribute("value",i);
		t = targetDoc.createTextNode(times[i]);
		s.appendChild(t);
		UI.appendChild(s);
	}
	}

	var s = targetDoc.createElement("option");
	s.setAttribute("value",times.length);
	var t = targetDoc.createTextNode("none");
	
	s.appendChild(t);
	UI.appendChild(s);
	
	//add a reference to the target documet to use by the event handler

	//add a reference to the recieved data to use by the event handler
	//UI.data = xmlhttp.responseText
	
	
	//display data; pass the object itself for reference;
	//UI.onchange(UI);
	data = msgEv;
	//UI.addEventListener("change", makeReport, false);
	// works!! document.addEventListener("change", function(e) { makeReport(e);}, true);
	UI.addEventListener("change", function(e) { makeReport(e);}, true);
	//window.addEventListener("change", function(e) { makeReport(e);}, false);
	//UI.addEventListener("change", function(e) { makeReport(e);}, false);
	//makeReport(event); 
	
	// does still work !! alert(targetDoc.getElementsByTagName("body")[0].innerHTML);
	var event = document.createEvent("HTMLEvents");
	event.initEvent("change", true, false);
	UI.dispatchEvent(event);
	

	
	//
}	
}


function buildPostData(server){
try{

	var documentB=targetDoc.getElementsByTagName("body")[0];
	

	var postData = "";
	var ITarget = null;
	
	var prefs = Components.classes["@mozilla.org/preferences-service;1"].getService(Components.interfaces.nsIPrefService).getBranch("extensions.skhunter.");
	
	var aPWD;// = encodeURIComponent(prefs.getCharPref('aPWD')); 
	var kdnm;// = encodeURIComponent(prefs.getCharPref('kdnm'));
	var email;// = encodeURIComponent(prefs.getCharPref('email'));
	var kdx;// = encodeURIComponent(prefs.getIntPref('kdx'));
	var kdy;// = encodeURIComponent(prefs.getIntPref('kdy'));
	//
	if (server == 1){
		aPWD = encodeURIComponent(prefs.getCharPref('aPWD')); 
		kdnm = encodeURIComponent(prefs.getCharPref('kdnm'));
		email = encodeURIComponent(prefs.getCharPref('email'));
		kdx = encodeURIComponent(prefs.getIntPref('kdx'));
		kdy = encodeURIComponent(prefs.getIntPref('kdy'));
	} else {
		aPWD = encodeURIComponent(prefs.getCharPref('aPWD2')); 
		kdnm = encodeURIComponent(prefs.getCharPref('kdnm2'));
		email = encodeURIComponent(prefs.getCharPref('email2'));
		kdx = encodeURIComponent(prefs.getIntPref('kdx2'));
		kdy = encodeURIComponent(prefs.getIntPref('kdy2'));	
	}
	//
	var tcap = encodeURIComponent(prefs.getIntPref('tcap'));
	
	
	//postData = "l="+encodeURIComponent(targetDoc.location.href);
	postData = "l=terranova";
	postData += "&aPWD=" + aPWD;
	postData += "&hver=" + _skhunterVersion;
	postData += "&kdnm=" + kdnm;
	postData += "&email=" + email;
	postData += "&kdx=" + kdx;
	postData += "&kdy=" + kdy;
	postData += "&tcap=" + tcap;
	
}
catch(err)
{
  txt = "SK HUNTER::buildPostData() failed: " + err.description;
  throw txt;
}	



	SKTime = function(HTMLObj){
		try{
			var DateString = HTMLObj.getElementsByTagName("tr")[0].getElementsByTagName("td")[2].innerHTML
			DateString=DateString.replace(/,/g, "");
			var Date=DateString.split(" ");
			this.month=encodeURIComponent(Date[0]);
			this.day= new Number(Date[1]);
			var Temp2=Date[2].split(":");
			this.hour=new Number(Temp2[0]);
			this.minute=new Number(Temp2[1]);
			//this.sufix=encodeURIComponent(Date[3]); no more suffix nowadays. 
			alert('time');
		}
		catch(err)
		{
			txt = "SK HUNTER::buildPostData::SKTime() failed: " + err.description;
			throw txt;
		}
	
	}
	
	SKTime_ = function(HTMLObj){
		var DateString = HTMLObj.innerHTML;
		DateString = DateString.replace(/,/g, "");
		var start = DateString.indexOf("(");
		var end = DateString.indexOf(")");
		DateString = DateString.slice(start+1,end);
		var Date=DateString.split(" ");
		this.month=encodeURIComponent(Date[0]);
		this.day= new Number(Date[1]);
		var Temp2=Date[2].split(":");
		this.hour=new Number(Temp2[0]);
		this.minute=new Number(Temp2[1]);
		//this.sufix=""; MAGNUS: Not used anymore, date no longer in PM/AM
	}
	
	var date;
	
	var ITarget = targetDoc.getElementById("time");
	// alert(ITarget.innerHTML);
	if(ITarget){
			date = new SKTime_(ITarget);
	} else {
		ITarget = getSectorTable(documentB,1,0,/^Money:/);	
		date = new SKTime(ITarget);
	}
		
	//postData += "&m="+date.month+"&d="+date.day+"&hour="+date.hour+"&min="+date.minute+"&s="+date.sufix; MAGNUS: Not used anymore, date no longer in PM/AM
	postData += "&m="+date.month+"&d="+date.day+"&hour="+date.hour+"&min="+date.minute;

	SKgalaxy = function(HTMLObj){
		try{	
			var Coords=HTMLObj.getElementsByTagName("tr")[0].getElementsByTagName("td")[1].getElementsByTagName("input");
			this.X=encodeURIComponent(Coords[0].getAttribute("value"));
			this.Y=encodeURIComponent(Coords[1].getAttribute("value"));
		}
		catch(err)
		{
			txt = "SK HUNTER::buildPostData::SKgalaxy() failed: " + err.description;
			throw txt;
		}		
	}
	
	ITarget = getSectorTable(documentB,0,1,/Galaxy:/);
	var galaxy = new SKgalaxy(ITarget);
	postData += "&x=" + galaxy.X + "&y="+ galaxy.Y;

	getAlliance = function(HTMLObj){
		try{
			var temp = HTMLObj.getElementsByTagName("tr")[0].getElementsByTagName("td")[2].getElementsByTagName("a");
			if(temp[0]) {
				return encodeURIComponent(temp[0].innerHTML);
			} else {
				return "none";
			}
		}
		catch(err)
		{
			txt = "SK HUNTER::buildPostData::getAlliance() failed: " + err.description;
			throw txt;
		}		
		
	}

try{	
   // ITarget = getSectorTable(documentB,0,0,/^The Sector Of:/);
	// var alliance = getAlliance(ITarget);
	// postData += "&ali=" + alliance;
		postData += "&ali=" + "no";

        ITarget = getSectorTable(documentB,0,0,/^Kingdom Name/);
	var slots=ITarget.getElementsByTagName("tr");	
	for (i=1;i<slots.length;i++){
		var status= slots[i].getAttribute("class");
		var tds=slots[i].getElementsByTagName("td");
		var a=tds[0].getElementsByTagName("a")[0];
		if(a) {
			var name=a.innerHTML;
			name=name.slice(0,name.indexOf("(")-1);
		} else {
			name="";
		}

		//var type=tds[1].innerHTML;
		type="";

		a=tds[2].getElementsByTagName("a")[0];
		if(a) {
			var land=a.innerHTML.replace(/,/g, "");
		} else {
			land=tds[2].innerHTML.replace(/,/g, "");
		}
		
		a=tds[3].getElementsByTagName("a")[0];
		if(a) {
			var nw=a.innerHTML.replace(/,/g, "");
		} else {
			nw=tds[3].innerHTML.replace(/,/g, "");
		}

		a=tds[4].getElementsByTagName("a")[0];
		if(a) {
			var h=a.innerHTML.replace(/,/g, "");
		} else {
			h=tds[4].innerHTML.replace(/,/g, "");
		}
		postData+="&name["+i+"]="+name;
		postData+="&sts["+i+"]="+status;
		postData+="&type["+i+"]="+type;
		postData+="&land["+i+"]="+land;
		postData+="&nw["+i+"]="+nw;
		postData+="&h["+i+"]="+h;
	}
}
catch(err)
{
  txt = "Error description: " + err.description + "\n";
  throw txt;
}
	//alert(postData);
	return postData;
}



var skhunter = {
  onLoad: function() {
    // initialization code
    this.initialized = true;
    this.strings = document.getElementById("skhunter-strings");
    document.getElementById("contentAreaContextMenu")
            .addEventListener("popupshowing", function(e) { this.showContextMenu(e); }, false);

	var appcontent = document.getElementById("appcontent");   
    if(appcontent && !appcontent.hasHunter) {
      appcontent.addEventListener("DOMContentLoaded", this.onPageLoad, true);
      appcontent.hasHunter = true;
    }			
  },
  
 
//

	
  //MAGNUS: This seems to be called when viewsector or mysector are viewed.
  
  onPageLoad: function(aEvent) {
    targetDoc = aEvent.originalTarget; 
    if(targetDoc.location.href.match(/viewsector/) == null) return;
	if(targetDoc.location.href.match(/mysector/)) return;	
	
	var sk1 = 0;

	//MAGNUS: Here the variables inputed in the Options regarding the server are recovered.
	var sss = Components.classes["@mozilla.org/preferences-service;1"].getService(Components.interfaces.nsIPrefService).getBranch("extensions.skhunter.").getCharPref('sSHORT');	
	var sss2 = Components.classes["@mozilla.org/preferences-service;1"].getService(Components.interfaces.nsIPrefService).getBranch("extensions.skhunter.").getCharPref('sSHORT2');	
	

	//MAGNUS: Here I'm fixing the SK server which used to be s1.starkingdoms.com and now is http://www.starkingdoms.com/game/terranova/. Unsure atm with the "/" maybe eescape char needed altho unlikely.
	//var smatch_ = new RegExp (sss+"\.starkingdoms\.com");	
	//var smatch2 = new RegExp (sss2+"\.starkingdoms\.com");		
	
	var smatch_ = new RegExp ("www\.starkingdoms\.com/game/"+sss+"");
	var smatch2 = new RegExp ("www\.starkingdoms\.com/game/"+sss2+"");
	
	//TODO: Fix this sh&t
	
    if(targetDoc.location.href.match(smatch_) != null) {
		sk1 = 1;
	} else {
		if(targetDoc.location.href.match(smatch2) != null){
			sk1 = 2;
		} else {
			return;
		}
	} 
	

	
	var HunterEnabled = Components.classes["@mozilla.org/preferences-service;1"].getService(Components.interfaces.nsIPrefService).getBranch("extensions.skhunter.").getBoolPref('on');
	var HunterEnabled2 = Components.classes["@mozilla.org/preferences-service;1"].getService(Components.interfaces.nsIPrefService).getBranch("extensions.skhunter.").getBoolPref('on2');

	if(sk1 == 1) 
	{
		if(!HunterEnabled) return;
	}
	if(sk1 == 2) 
	{
		if(!HunterEnabled2) return;
	}


	xmlhttp = new XMLHttpRequest();
		//-->? xmlhttp.targetDoc = targetDoc;
    xmlhttp.onreadystatechange=evalData;
	
	var sURL;

	if (sk1 == 1){
	sURL = Components.classes["@mozilla.org/preferences-service;1"].getService(Components.interfaces.nsIPrefService).getBranch("extensions.skhunter.").getCharPref('sURL');
	} else {
	sURL = Components.classes["@mozilla.org/preferences-service;1"].getService(Components.interfaces.nsIPrefService).getBranch("extensions.skhunter.").getCharPref('sURL2');
	}
	
	//if(!targetDoc.location.href.match(/s1/)) return;
    xmlhttp.open("POST",sURL,true);
    xmlhttp.setRequestHeader('Content-Type','application/x-www-form-urlencoded; charset=UTF-8');
	var postData = buildPostData(sk1); 
    xmlhttp.send(postData);
   

	// make User Interface
	documentBody=targetDoc.getElementsByTagName("body")[0];
	//var ITarget=documentBody.getElementsByTagName("table")[6].getElementsByTagName("tbody")[0];
        var ITarget = getSectorTable(documentBody,0,0,/^Kingdom Name/);
	//create selection element
	UI=targetDoc.createElement("select");
	UI.setAttribute("id","timeline");
	
	//create first option element
	//s = targetDoc.createElement("option");
	//s.setAttribute("value",0);
	//t = targetDoc.createTextNode(" ");
	//s.appendChild(t);
	//UI.appendChild(s);

	//text surrounding selection tag
	var t = targetDoc.createTextNode("View ");
	var t2 = targetDoc.createTextNode(" ago");

	//create selection <td>
	var td=targetDoc.createElement("td");
	td.appendChild(t);
	td.appendChild(UI);
	td.appendChild(t2);

	//create hunter table row
	tr=targetDoc.createElement("tr");
	tr.appendChild(td);

	//tds for server message
	var tM = targetDoc.createTextNode("loading data, please wait...");
	var tdM=targetDoc.createElement("td");
	//set server message id for later reference
	tdM.setAttribute("id","serverMessageTD");
	tdM.setAttribute("colspan","8");
	tdM.appendChild(tM);
	tr.appendChild(tdM);

	//ITarget is the the table body, it's chiles are the table rows displaying kindom lines
	var ref=ITarget.getElementsByTagName("tr");
	ITarget.insertBefore(tr,ref.item(0));

	ref=ITarget.getElementsByTagName("tr");
	var tdref=ref[1].getElementsByTagName("td");	
	//add tds "change"
	td=targetDoc.createElement("td");
	t = targetDoc.createTextNode("change");
	td.appendChild(t);
	ref[1].insertBefore(td,tdref.item(3));
	td2=td.cloneNode(true);
	ref[1].insertBefore(td2,tdref.item(5));
	td3=td.cloneNode(true);
	ref[1].insertBefore(td3,tdref.item(7));

	//create tds for change values;
	for(i=1;i<21;i++){
		tdref=ref[i+1].getElementsByTagName("td");
		td=targetDoc.createElement("td");
		t = targetDoc.createTextNode("--");
		td.appendChild(t);
		ref[i+1].insertBefore(td,tdref.item(3));
		td2=td.cloneNode(true);
		ref[i+1].insertBefore(td2,tdref.item(5));
		td3=td.cloneNode(true);
		ref[i+1].insertBefore(td3,tdref.item(7));
	}
	
	
// 
//
    
  },  

  showContextMenu: function(event) {
    // show or hide the menuitem based on what the context menu is on
    // see http://kb.mozillazine.org/Adding_items_to_menus
    document.getElementById("context-skhunter").hidden = gContextMenu.onImage;
  },
  
  onMenuItemCommand: function(e) {
    var promptService = Components.classes["@mozilla.org/embedcomp/prompt-service;1"]
                                  .getService(Components.interfaces.nsIPromptService);
	var HunterEnabled = Components.classes["@mozilla.org/preferences-service;1"].
									getService(Components.interfaces.nsIPrefService).
									getBranch("extensions.skhunter.").getBoolPref('on');	
	var seURL = Components.classes["@mozilla.org/preferences-service;1"].getService(Components.interfaces.nsIPrefService).getBranch("extensions.skhunter.").getCharPref('sURL');									
	var strH = this.strings.getString("helloMessage");
	if(HunterEnabled){
		promptService.alert(window, this.strings.getString("helloMessageTitle"),
                                strH + " " + seURL );
	} else {
		promptService.alert(window, this.strings.getString("helloMessageTitle"),
                                this.strings.getString("sorryMessage"));	
	}
  },

};
window.addEventListener("load", function(e) { skhunter.onLoad(e); }, false);

//window.addEventListener("onchange", function(e) { skhunter.makeReport(e);}, false);
// works!! window.addEventListener("change", function(e) { alert('fuck you'); }, false);
