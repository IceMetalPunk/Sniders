// Dialog.js
// (c) LLib Source Code Trust. All rights reserved

// Must call InsertJSGetOffset() in your webpage when including this file

var bCancelled = false;

function cancelUpload()
{
 bCancelled = true;
}

function startUpload()
{
 var dialogform = document.getElementById('dialogform');
 if (typeof cancel != 'undefined') return true;
 var Inputs = document.getElementsByTagName('input');
 var bNoFiles = true;
 for (var i = 0; (i < Inputs.length) && !bCancelled; i++) {
  if (Inputs[i].type == 'file') {
   if (Inputs[i].value.length != 0) {
    // There is a file to upload so we have to submit the form
    bNoFiles = false;
    UploadId = Inputs[i].id;
    break;
   }
  }
 }
 if (bNoFiles || bCancelled) {
  // If we aren't uploading any files, use AJAX submit instead
  return CmSubmit();
 }
 ProgressBar[UploadId].ShowBar(true);
 // Disable form buttons
 var submitButtons = document.getElementsByName('submit');
 for (var i = 0; i < submitButtons.length; i++) submitButtons[i].disabled = 'disabled';

 var uploadDiv = document.createElement('div');
 uploadDiv.id = 'uploadStatusDiv';
 uploadDiv.style.textAlign = 'center';
 uploadDiv.style.margin = '10px auto';
 dialogform.appendChild(uploadDiv);

 var amountTotalP = document.createElement('p');
 amountTotalP.appendChild(document.createTextNode('0% -- 0 KB uploaded out of 0 KB total'));
 amountTotalP.setAttribute('id', 'amountTotal');
 var speedP = document.createElement('p');
 speedP.appendChild(document.createTextNode('0 KB/s -- 00:00 remaining'));
 speedP.setAttribute('id', 'speed');
 uploadDiv.appendChild(amountTotalP);
 uploadDiv.appendChild(speedP);
 setTimeout('GetProgress(' + uBrowserID + ')', 500);
 // both of these used in updateProgress
 time = 0.5;
 oldPercent = 0;
 barLocation = getOffset(uploadDiv);
 window.scrollTo(barLocation.left, barLocation.top);
 return true;
}

function uploadDone()
{
 var uploadIFrame = document.getElementById('uploadIFrame');
 var responseDocument = uploadIFrame.contentDocument != null ? uploadIFrame.contentDocument : uploadIFrame.contentWindow.document;
 // Check the iFrame for a response from the server
 responseBlock = responseDocument.getElementsByTagName('pre'); // This is the element that Chrome and Firefox inserts plain text into
 if (responseBlock.length == 0) {
  responseBlock = responseDocument.getElementsByTagName('body'); // This is the element that IE inserts plain text into
  if (responseBlock.length == 0) {
   return;
  }
 }
 responseJS = responseBlock[0].innerHTML;
 if (responseJS != '') {
  // Re-enable form buttons
  var submitButtons = document.getElementsByName('submit');
  for (var i = 0; i < submitButtons.length; i++) submitButtons[i].removeAttribute('disabled');
  // Remove the progress bar
  uploadDiv = document.getElementById('uploadStatusDiv');
  if (uploadDiv != null) uploadDiv.parentNode.removeChild(uploadDiv);
  if (typeof UploadId != 'undefined') ProgressBar[UploadId].ShowBar(false);
  // Run the javascript
  eval(responseJS);
 }
}

function updateProgress(progressXML, AJAXstatus, progressText)
{
 if (AJAXstatus != 200) return;
 // Make sure there is a progress bar to update
 if (document.getElementById('uploadStatusDiv') == null) return;

 updateTimeout = setTimeout('GetProgress(' + uBrowserID + ')', 1000);
 time += 1.0;

 var bytesRead = progressXML.getElementsByTagName('bytesRead')[0];
 var totalBytes = progressXML.getElementsByTagName('totalBytes')[0];
 var bytesReadKB = bytesRead.firstChild.nodeValue / 1024;
 var totalBytesKB = totalBytes.firstChild.nodeValue / 1024;
 if (bytesReadKB > totalBytesKB) bytesReadKB = totalBytesKB;

 var amountTotalP = document.getElementById('amountTotal').firstChild;
 var speedP = document.getElementById('speed').firstChild;
 var percent = Math.round((bytesReadKB / totalBytesKB) * 100);
 amountTotalP.nodeValue = percent + '% -- ' + Math.round(bytesReadKB) + ' KB uploaded out of ' + Math.round(totalBytesKB) + ' KB total';

 var speed = (bytesReadKB / time) * 10;
 speed = Math.round(speed);
 speed /= 10;
 var remainingSec = Math.round((totalBytesKB - bytesReadKB) / speed);
 var displayMin = Math.floor(remainingSec / 60) ;
 var displaySec = remainingSec % 60;
 speedP.nodeValue = speed + ' KB/s' + ' -- ' + displayMin + ':' + ((displaySec < 10)? '0' : '') + displaySec + ' remaining';

 ProgressBar[UploadId].UpdateBar(percent);

 if (percent >= 100) clearTimeout(updateTimeout);
}

function GetProgress(uBrowserID)
{
 SimpleAjaxCall('uploadProgress', "uBrowserID=" + uBrowserID, function(AJAX)
 {
  updateProgress(AJAX.responseXML, AJAX.status, AJAX.responseText);
 });
}
