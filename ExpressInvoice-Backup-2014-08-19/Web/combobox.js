// Path to arrow images
var arrowImage     = 'select_arrow.gif';        // Regular arrow
var arrowImageOver = 'select_arrow_over.gif';   // Mouse over
var arrowImageDown = 'select_arrow_down.gif';   // Mouse down

var selectBoxIds = 0;
var currentlyOpenedOptionBox = false;
var editableSelect_activeArrow = false;

function selectBox_switchImageUrl()
{
   if (this.src.indexOf(arrowImage) >= 0) this.src = this.src.replace(arrowImage,arrowImageOver);
   else this.src = this.src.replace(arrowImageOver,arrowImage);
}

function selectBox_showOptions()
{
   if (editableSelect_activeArrow && editableSelect_activeArrow != this) editableSelect_activeArrow.src = arrowImage;
   editableSelect_activeArrow = this;

   var numId = this.id.replace(/[^\d]/g,'');
   var optionDiv = document.getElementById('selectBoxOptions' + numId);
   if (optionDiv.style.display == 'block') {
      this.src = arrowImageOver;
      optionDiv.style.display = 'none';
   } else {
      this.src = arrowImageDown;
      optionDiv.scrollTop = 0;
      optionDiv.style.display = 'block';
      if (currentlyOpenedOptionBox && currentlyOpenedOptionBox != optionDiv) currentlyOpenedOptionBox.style.display = 'none';
      currentlyOpenedOptionBox = optionDiv;
   }
}

function selectOptionValue()
{
   var parentNode = this.parentNode.parentNode;
   var textInput = parentNode.getElementsByTagName('INPUT')[0];
// textInput.value = this.innerHTML;
   if (document.all) textInput.value = this.innerText;
   else textInput.value = this.textContent;

   this.parentNode.scrollTop = 0;
   this.parentNode.style.display = 'none';
   document.getElementById('arrowSelectBox' + parentNode.id.replace(/[^\d]/g,'')).src = arrowImageOver;

   //On IE
   if (textInput.fireEvent) textInput.fireEvent('onchange');

   //On Gecko based browsers
   if (document.createEvent) {
      var evt = document.createEvent('HTMLEvents');
      if (evt.initEvent) evt.initEvent('change', true, true);
      if (textInput.dispatchEvent) textInput.dispatchEvent(evt);
   }
}

var activeOption;
function highlightSelectBoxOption()
{
   if (this.style.backgroundColor == '#316AC5') {
      this.style.backgroundColor = '';
      this.style.color = '';
   } else {
      this.style.backgroundColor = '#316AC5';
      this.style.color = '#FFF';
   }

   if (activeOption) {
      activeOption.style.backgroundColor = '';
      activeOption.style.color = '';
   }
   activeOption = this;
}

function createEditableSelect(dest)
{

   dest.className = 'selectBoxInput';
   var div = document.createElement('DIV');
   div.style.styleFloat = 'left';
   div.style.width = dest.offsetWidth + 16 + 'px';
   div.style.position = 'relative';
   div.id = 'selectBox' + selectBoxIds;
   var parent = dest.parentNode;
   parent.insertBefore(div,dest);
   div.appendChild(dest);
   div.className = 'selectBox';
   div.style.zIndex = 10000 - selectBoxIds;

   var img = document.createElement('IMG');
   img.src = arrowImage;
   img.className = 'selectBoxArrow';

   img.onmouseover = selectBox_switchImageUrl;
   img.onmouseout = selectBox_switchImageUrl;
   img.onclick = selectBox_showOptions;
   img.id = 'arrowSelectBox' + selectBoxIds;

   div.appendChild(img);

   var optionDiv = document.createElement('DIV');
   optionDiv.id = 'selectBoxOptions' + selectBoxIds;
   optionDiv.className = 'selectBoxOptionContainer';
   var divWidth = div.offsetWidth;
   if (divWidth > 2) optionDiv.style.width = divWidth - 2 + 'px';
   div.appendChild(optionDiv);

   if (dest.getAttribute('selectBoxOptions')) {
      var options = dest.getAttribute('selectBoxOptions').split(';');
      var optionsTotalHeight = 0;
      var optionArray = new Array();
      for (var no = 0; no < options.length; no++) {
         var anOption = document.createElement('DIV');
         anOption.innerHTML = options[no];
         anOption.className = 'selectBoxAnOption';
         anOption.onclick = selectOptionValue;
         var wdth = parseInt(optionDiv.style.width.replace('px',''));
         if (!isNaN(wdth) && wdth > 2) anOption.style.width = wdth - 2 + 'px';
         anOption.onmouseover = highlightSelectBoxOption;
         optionDiv.appendChild(anOption);
         optionsTotalHeight = optionsTotalHeight + anOption.offsetHeight;
         optionArray.push(anOption);
      }
      if (optionsTotalHeight > optionDiv.offsetHeight) {
         for (var no = 0; no < optionArray.length; no++) {
            optionArray[no].style.width = optionDiv.style.width.replace('px','') - 22 + 'px';
         }
      }
      optionDiv.style.display = 'none';
      optionDiv.style.visibility = 'visible';
   }

   selectBoxIds = selectBoxIds + 1;
}
