// Ajax.js
// (c) LLib Source Code Trust. All rights reserved

//Ajax object is passed as a parameter to your readyStateFunc
//Your readStateFunc can either be a standalone function or an anonymous inline function
//If you define your readyStateFunc inline with the SimpleAjaxCall, then all the variables that were in scope when you made the SimpleAjaxCall are still available in your readyStateFunc
function SimpleAjaxCall(page, args, readyStateFunc, errorStateFunc, timeOutStateFunc, timeOut)
{
 var AJAX = createAJAXRequest(page);
 var requestTimer = null;
 if (timeOut != undefined) {
  requestTimer = setTimeout(function() {
   AJAX.abort();
   if (timeOutStateFunc != undefined) timeOutStateFunc(AJAX);
  }, timeOut);
 }
 AJAX.onreadystatechange = function()
 {
  if (AJAX.readyState == 4) {
   if (requestTimer) clearTimeout(requestTimer);
   if (AJAX.status == 200) {
    if (readyStateFunc != undefined) {
     readyStateFunc(AJAX);
    }
   } else if (errorStateFunc != undefined) {
    errorStateFunc(AJAX);
   }
  }
 };
 AJAX.send(args);
 return AJAX;
}

function createAJAXRequest(page) {
 var AJAX = null;
 if (window.XMLHttpRequest) {
  // code for IE7 + , Firefox, Chrome, Opera, Safari
  AJAX = new XMLHttpRequest();
 } else {
  // code for IE6, IE5
  AJAX = new ActiveXObject('Microsoft.XMLHTTP');
 }
 AJAX.open('POST', page, true);
 AJAX.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
 return AJAX;
}

function HandleAjaxJSReturn(AJAX)
{
 response = AJAX.responseText;
 if (response) eval(response);
}

function GetArg(controlElement)
{
 var retValue = '';
 if (!controlElement || !controlElement.tagName) return retValue;
 var tag = controlElement.tagName.toLowerCase();
 var controlName = controlElement.name ? controlElement.name : controlElement.id;
 if (tag == 'a') retValue = controlName + '=' + encodeURIComponent(controlElement.href);
 else if (tag == 'input') {
  if ((controlElement.type.toLowerCase() == 'checkbox') || (controlElement.type.toLowerCase()) == 'radio') {
   if (!controlElement.checked) return retValue;
  }
 }
 if (retValue.length == 0) retValue = controlName + '=' + encodeURIComponent(controlElement.value);

 return retValue;
}

function GetParams(id)
{
 form = document.getElementById(id);
 params = '';
 for (var i = 0; i < form.elements.length; i++) {
  input = form.elements[i];
  if (input.disabled || (input.type == undefined)) continue;
  if ((input.type == 'button') || (input.type == 'submit') || (input.type == 'label')) continue;
  var param = GetArg(input);
  if (param.length != 0) {
   params += param + '&';
  }
 }
 return params;
}
