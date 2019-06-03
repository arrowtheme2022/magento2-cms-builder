function sack(file) {
	this.xmlhttp = null;

	this.resetData = function() {
		this.method = "POST";
		this.queryStringSeparator = "?";
		this.argumentSeparator = "&";
		this.URLString = "";
		this.encodeURIString = true;
		this.execute = false;
		this.element = null;
		this.elementObj = null;
		this.requestFile = file;
		this.vars = new Object();
		this.responseStatus = new Array(2);
	};

	this.resetFunctions = function() {
		this.onLoading = function() { };
		this.onLoaded = function() { };
		this.onInteractive = function() { };
		this.onCompletion = function() { };
		this.onError = function() { };
		this.onFail = function() { };
	};

	this.reset = function() {
		this.resetFunctions();
		this.resetData();
	};

	this.createAJAX = function() {
		try {
			this.xmlhttp = new ActiveXObject("Msxml2.XMLHTTP");
		} catch (e1) {
			try {
				this.xmlhttp = new ActiveXObject("Microsoft.XMLHTTP");
			} catch (e2) {
				this.xmlhttp = null;
			}
		}

		if (! this.xmlhttp) {
			if (typeof XMLHttpRequest != "undefined") {
				this.xmlhttp = new XMLHttpRequest();
			} else {
				this.failed = true;
			}
		}
	};

	this.setVar = function(name, value){
		this.vars[name] = Array(value, false);
	};

	this.encVar = function(name, value, returnvars) {
		if (true == returnvars) {
			return Array(encodeURIComponent(name), encodeURIComponent(value));
		} else {
			this.vars[encodeURIComponent(name)] = Array(encodeURIComponent(value), true);
		}
	}

	this.processURLString = function(string, encode) {
		encoded = encodeURIComponent(this.argumentSeparator);
		regexp = new RegExp(this.argumentSeparator + "|" + encoded);
		varArray = string.split(regexp);
		for (i = 0; i < varArray.length; i++){
			urlVars = varArray[i].split("=");
			if (true == encode){
				this.encVar(urlVars[0], urlVars[1]);
			} else {
				this.setVar(urlVars[0], urlVars[1]);
			}
		}
	}

	this.createURLString = function(urlstring) {
		if (this.encodeURIString && this.URLString.length) {
			this.processURLString(this.URLString, true);
		}

		if (urlstring) {
			if (this.URLString.length) {
				this.URLString += this.argumentSeparator + urlstring;
			} else {
				this.URLString = urlstring;
			}
		}

		// prevents caching of URLString
		this.setVar("rndval", new Date().getTime());

		urlstringtemp = new Array();
		for (key in this.vars) {
			if (false == this.vars[key][1] && true == this.encodeURIString) {
				encoded = this.encVar(key, this.vars[key][0], true);
				delete this.vars[key];
				this.vars[encoded[0]] = Array(encoded[1], true);
				key = encoded[0];
			}

			urlstringtemp[urlstringtemp.length] = key + "=" + this.vars[key][0];
		}
		if (urlstring){
			this.URLString += this.argumentSeparator + urlstringtemp.join(this.argumentSeparator);
		} else {
			this.URLString += urlstringtemp.join(this.argumentSeparator);
		}
	}

	this.runResponse = function() {
		eval(this.response);
	}

	this.runAJAX = function(urlstring) {
		if (this.failed) {
			this.onFail();
		} else {
			this.createURLString(urlstring);
			if (this.element) {
				this.elementObj = document.getElementById(this.element);
			}
			if (this.xmlhttp) {
				var self = this;
				if (this.method == "GET") {
					totalurlstring = this.requestFile + this.queryStringSeparator + this.URLString;
					this.xmlhttp.open(this.method, totalurlstring, true);
				} else {
					this.xmlhttp.open(this.method, this.requestFile, true);
					try {
						this.xmlhttp.setRequestHeader("Content-Type", "application/x-www-form-urlencoded")
					} catch (e) { }
				}

				this.xmlhttp.onreadystatechange = function() {
					switch (self.xmlhttp.readyState) {
						case 1:
							self.onLoading();
							break;
						case 2:
							self.onLoaded();
							break;
						case 3:
							self.onInteractive();
							break;
						case 4:
							self.response = self.xmlhttp.responseText;
							self.responseXML = self.xmlhttp.responseXML;
							self.responseStatus[0] = self.xmlhttp.status;
							self.responseStatus[1] = self.xmlhttp.statusText;

							if (self.execute) {
								self.runResponse();
							}

							if (self.elementObj) {
								elemNodeName = self.elementObj.nodeName;
								elemNodeName.toLowerCase();
								if (elemNodeName == "input"
								|| elemNodeName == "select"
								|| elemNodeName == "option"
								|| elemNodeName == "textarea") {
									self.elementObj.value = self.response;
								} else {
									self.elementObj.innerHTML = self.response;
								}
							}
							if (self.responseStatus[0] == "200") {
								self.onCompletion();
							} else {
								self.onError();
							}

							self.URLString = "";
							break;
					}
				};

				this.xmlhttp.send(this.URLString);
			}
		}
	};

	this.reset();
	this.createAJAX();
}

var dhtmlgoodies_tree;
var folderImage = 'folder-open.gif';
var plusImage = 'elbow-end-plus.gif';
var minusImage = 'elbow-end-minus.gif';
var initExpandedNodes = '';	// Cookie - initially expanded nodes;
var timeoutEdit = 20;	// Lower value = shorter delay from mouse is pressed down to textbox appears.


var ajax = new sack();

function showHideNode(e,inputId)
{
	if(inputId){
		if(!document.getElementById('dhtmlgoodies_treeNode'+inputId))return;
		thisNode = document.getElementById('dhtmlgoodies_treeNode'+inputId).getElementsByTagName('IMG')[0]; 
	}else {
		thisNode = this;
	}
	if(thisNode.style.visibility=='hidden')return;
	var parentNode = thisNode.parentNode;
	inputId = parentNode.id.replace(/[^0-9]/g,'');
	if(thisNode.src.indexOf('plus')>=0){
		thisNode.src = thisNode.src.replace('plus','minus');
		parentNode.getElementsByTagName('UL')[0].style.display='block';
		if(!initExpandedNodes)initExpandedNodes = ',';
		if(initExpandedNodes.indexOf(',' + inputId + ',')<0) initExpandedNodes = initExpandedNodes + inputId + ',';
		
	}else{
		thisNode.src = thisNode.src.replace('minus','plus');
		parentNode.getElementsByTagName('UL')[0].style.display='none';
		initExpandedNodes = initExpandedNodes.replace(',' + inputId,'');
	}	
}

function showNode(e,inputId)
{
	if(inputId){
		if(!document.getElementById('dhtmlgoodies_treeNode'+inputId))return;
		thisNode = document.getElementById('dhtmlgoodies_treeNode'+inputId).getElementsByTagName('IMG')[0]; 
	}else {
		thisNode = this;
	}

	var parentNode = thisNode.parentNode;
	inputId = parentNode.id.replace(/[^0-9]/g,'');
	
	thisNode.src = thisNode.src.replace('plus','minus');
	parentNode.getElementsByTagName('UL')[0].style.display='block';
	if(!initExpandedNodes)initExpandedNodes = ',';
	if(initExpandedNodes.indexOf(',' + inputId + ',')<0) initExpandedNodes = initExpandedNodes + inputId + ',';
}

function okToNavigate()
{
	if(editCounter<10)return true;
	return false;		
}

var editCounter = -1;
var editEl = false;

function initEditLabel(){	}

function startEditLabel(){}

function showUpdate()
{
	document.getElementById('ajaxMessage').innerHTML = ajax.response;
}

function hideEdit()
{				
	var editObj = editEl.previousSibling;	
	if(editObj.value.length>0){
		editEl.innerHTML = editObj.value;	
		ajax.requestFile = fileName + '?updateNode='+editObj.id.replace(/[^0-9]/g,'') + '&newValue='+editObj.value;	// Specifying which file to get
		ajax.onCompletion = showUpdate;	// Specify function that will be executed after file has been found
		ajax.runAJAX();		// Execute AJAX function
					
	}
	editEl.style.display='inline';
	editObj.style.display='none';
	editEl = false;			
	editCounter=-1;
}

function mouseUpEvent()
{
	editCounter=-1;		
}

require([
	'jquery',
], function(jQuery){
	(function($) {
		$('#expand-all').click(function(){
			var menuItems = dhtmlgoodies_tree.getElementsByTagName('LI');
			for(var no=0;no<menuItems.length;no++){
				var subItems = menuItems[no].getElementsByTagName('UL');
				if(subItems.length>0 && subItems[0].style.display!='block'){
					showHideNode(false,menuItems[no].id.replace(/[^0-9]/g,''));
				}			
			}
			return false;
		});

		$('#collapse-all').click(function(){
			var menuItems = dhtmlgoodies_tree.getElementsByTagName('LI');
			for(var no=0;no<menuItems.length;no++){
				var subItems = menuItems[no].getElementsByTagName('UL');
				if(subItems.length>0 && subItems[0].style.display=='block'){
					showHideNode(false,menuItems[no].id.replace(/[^0-9]/g,''));
				}			
			}
			return false;
		});
	})(jQuery);
});
	


function initTree(){
	dhtmlgoodies_tree = document.getElementById('dhtmlgoodies_tree');
	var menuItems = dhtmlgoodies_tree.getElementsByTagName('LI');	// Get an array of all menu items
	for(var no=0;no<menuItems.length;no++){
		var subItems = menuItems[no].getElementsByTagName('UL');
		var img = document.createElement('IMG');
		img.src = imageFolder + plusImage;
		img.onclick = showHideNode;
		if(subItems.length==0)img.style.visibility='hidden';
		var aTag = menuItems[no].getElementsByTagName('A')[0];
		
		if(aTag.id)numericId = aTag.id.replace(/[^0-9]/g,'');else numericId = (no+1);
		
		aTag.id = 'dhtmlgoodies_treeNodeLink' + numericId;
		
		var input = document.createElement('INPUT');
		input.style.width = '200px';
		input.style.display='none';
		menuItems[no].insertBefore(input,aTag);
		input.id = 'dhtmlgoodies_treeNodeInput' + numericId;
		input.onblur = hideEdit;
					
		menuItems[no].insertBefore(img,input);
		menuItems[no].id = 'dhtmlgoodies_treeNode' + numericId;
		aTag.onclick = okToNavigate;
		aTag.onmousedown = initEditLabel;
		var folderImg = document.createElement('IMG');
		if(menuItems[no].className){
			folderImg.src = imageFolder + menuItems[no].className;
		}else{
			folderImg.src = imageFolder + folderImage;
		}
		menuItems[no].insertBefore(folderImg,input);
	}
	
	initExpandedNodes = categoryIds;
	if(initExpandedNodes){
		var nodes = initExpandedNodes.split(',');
		for(var no=0;no<nodes.length;no++){
			if(nodes[no]){
				explainNote(nodes[no]);
			}
		}			
	}
	
	document.documentElement.onmouseup = mouseUpEvent;
}

function explainNote(noteNumber){
	require([
		'jquery',
	], function(jQuery){
		(function($) {
			while($('#dhtmlgoodies_treeNode'+noteNumber).parent().attr('id') != 'dhtmlgoodies_tree') {
				noteNumber = $('#dhtmlgoodies_treeNode'+noteNumber).parent().parent().attr('lang');
				showNode(false,noteNumber);	
				explainNote(noteNumber);
			}
		})(jQuery);
	});
}

window.onload = initTree;