// Popup.js
// (c) LLib Source Code Trust. All rights reserved

// Must call InsertJSGetOffset() in your webpage when including this file

function TogglePopup(event, idPopup, idParent, offsetX, offsetY) {
 elPopup = document.getElementById(idPopup);
 elParent = document.getElementById(idParent);

 if ((elPopup.style.display != '') && (elPopup.style.display != 'none')) {
  HidePopup(event, idPopup);
  return;
 }
 if (elPopup.style.position == 'absolute') {
  if (typeof elParent != 'undefined') {
   var position = getOffset(elParent);
   elPopup.style.left = (position.left + offsetX) + 'px';
   elPopup.style.top = ((position.top + elParent.offsetHeight) + offsetY) + 'px';
  } else {
   // contextmenu
   elPopup.style.left = (event.clientX + offsetX) + 'px';
   elPopup.style.top = (event.clientY + offsetY) + 'px';
  }
 }
 if (typeof event != 'undefined') {
  // IE8 and below don't support stopPropagation();
  if (event.stopPropagation) event.stopPropagation();
  else event.cancelBubble = true;
 }
 elPopup.style.display = 'block';
 return false;
}

function HidePopup(event, idControl)
{
 elControl = document.getElementById(idControl);
 if ((typeof elControl != 'undefined') && (elControl != null)) {
  if (elControl.parentElement) {
   elControl.parentElement.removeChild(elControl);
   return;
  }
  elControl.style.display = 'none';
 }
 if (typeof event != 'undefined') {
  // IE8 and below don't support stopPropagation();
  if (event.stopPropagation) event.stopPropagation();
  else event.cancelBubble= true;
 }
}
