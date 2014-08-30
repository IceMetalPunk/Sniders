LAddEventListener(window, 'onload', function() 
 {
  PreloadImage();
  JavascriptifyTables();
 });
function PreloadImage() 
{
 //preload up/down images
 //must make them global (don't use var) so they don't get garbage collected
 imageup = new Image();
 imagedown = new Image();

 imageup.src = 'upsort.gif';
 imagedown.src = 'downsort.gif';
}
function JavascriptifyTables()
{
 //Older IE doesn't have getElementsByClassName
 var tables = document.getElementsByTagName('table');
 for (var i = 0; i < tables.length; i++) {
  if (tables[i].className.search('hltbl') != -1) new SmartTable(tables[i]);
 }
}
function SmartTable(table)
{
 this.id = table.getAttribute('id');
 this.MakeFromExisting(table);
 this.SetVariables();
}
//All of the className parts that seem to be duplicates are because older IE uses className when it refers to class (nonstandard behavior)
SmartTable.prototype.MakeFromExisting = function(table)
{
 this.table = table;

 if (this.table.getAttribute('data-sortcolumn')) {
  if (this.table.getAttribute('data-asc') == 1) szOrder = 'asc';
  else szOrder = 'desc';
  this.columnSorted = [ this.table.getAttribute('data-sortcolumn'), szOrder ];
 }
 this.tableHead = this.table.getElementsByTagName('thead')[0];
 this.tableHeadRow = this.table.rows[0];
 this.tableHeadElements = this.tableHeadRow.getElementsByTagName('th');
 this.tableBody = table.getElementsByTagName('tbody')[0];

 this.tableBodyRows = new Array();
 var existingTableRows = this.table.rows;
 for (var i = 1; i < existingTableRows.length; i++) {
  this.tableBodyRows[i-1] = existingTableRows[i];
  this.tableBodyRows[i-1].Cols = existingTableRows[i].cells;
  if ((i % 2) == 0) {
   this.tableBodyRows[i-1].setAttribute('class', 'tr0');this.tableBodyRows[i-1].setAttribute('className', 'tr0');
   this.tableBodyRows[i-1].setAttribute('onmouseout', 'this.className = "tr0"');
  } else {
   this.tableBodyRows[i-1].setAttribute('class', 'tr1');this.tableBodyRows[i-1].setAttribute('className', 'tr1');
   this.tableBodyRows[i-1].setAttribute('onmouseout', 'this.className = "tr1"');
  }
  this.tableBodyRows[i-1].setAttribute('onmouseover', 'this.className = "trh"');
 }
 if (this.columnSorted) {
  this.AddArrow(this.columnSorted[0]);
 }
}
SmartTable.prototype.SetVariables = function()
{
 //thead variables/settings
 var colEdit = new Array();
 for (var i = 0; i < this.tableHeadElements.length; i++) {
  if (this.tableHeadElements[i].getAttribute('data-sort')) {
   this.tableHeadElements[i].onclick = function(i, table) { return function() { table.SortColumn(i);}; }(i, this);
  }
 }
}
SmartTable.prototype.SortColumn = function(index)
{
 //mergesort uses more memory than in place sorts O(n), but has a worst case run time of O(n log(n)) and is a stable sort
 //advantage to using a stable sort is that when a user sorts with one column, then sorts another (say this second column has many identical values),
 //the results of the second sort will have all entries with identical values still sorted by the parameters of the first sort
 if (this.tableBodyRows.length == 0) return;

 var resultArray = null;
 if (!this.columnSorted || ((this.columnSorted[0] == index) && (this.columnSorted[1] == 'asc'))) {
  this.columnSorted = [index, 'desc'];
  resultArray = this.MergeSort(false, index, 0, this.tableBodyRows.length - 1);
 } else {
  this.columnSorted = [index, 'asc'];
  resultArray = this.MergeSort(true, index, 0, this.tableBodyRows.length - 1);
 }
 //Remove all rows from table, then re-append them in the sorted order (this.columnSorted[index] holds current sort direction)
 for (var i = 0; i < this.tableBodyRows.length; i++) this.tableBody.removeChild(this.tableBodyRows[i]);
 // replace this.tableBodyRows with an array that is sorted (uses extra memory, but allows for stable sort)
 var replacement = new Array();
 for (var i = 0; i < this.tableBodyRows.length; i++) replacement[i] = this.tableBodyRows[resultArray[i][0]];
 this.tableBodyRows = replacement;
 // populate table with elements in sorted order
 for (var i = 0; i < this.tableBodyRows.length ; i++) {
  this.tableBody.appendChild(this.tableBodyRows[i]);
  // fix table highlight since rows have moved
  if ((i % 2) == 0) {
   this.tableBodyRows[i].setAttribute('class', 'tr0');
   this.tableBodyRows[i].setAttribute('className', 'tr0');
   this.tableBodyRows[i].onmouseout = function() { this.className = 'tr0' };
  } else {
   this.tableBodyRows[i].setAttribute('class', 'tr1');
   this.tableBodyRows[i].setAttribute('className', 'tr1');
   this.tableBodyRows[i].onmouseout = function() { this.className = 'tr1' };
  }
 }
 //column sort direction arrow, first find and remove existing one, then create and attach new one
 for (var i = 0; i < this.tableHeadElements.length; i++) {
  if (this.tableHeadElements[i].SortArrow) {
   this.tableHeadElements[i].removeChild(this.tableHeadElements[i].SortArrow);
   this.tableHeadElements[i].SortArrow = null;
   break;
  }
 }
 this.AddArrow(index);
}
SmartTable.prototype.AddArrow = function (index)
{
 var arrow = document.createElement('img');
 if (this.columnSorted[1] == 'desc') arrow.setAttribute('src', 'downsort.gif');
 else arrow.setAttribute('src', 'upsort.gif');
 this.tableHeadElements[index].SortArrow = arrow;
 this.tableHeadElements[index].appendChild(arrow);
}
SmartTable.prototype.MergeSort = function(directionToCompare, indexToCompare,  leftBound, rightBound)
{
 //instead of copying entire rows, an array is returned with the row index, and the value being compared (saves memory and time during sort)
 if (leftBound == rightBound) return [[leftBound, this.GetCellValue(leftBound, indexToCompare)]];

 var left = leftBound;
 var middleBound = Math.round((leftBound + rightBound - 1) / 2);
 var right = middleBound + 1;
 var leftArray = this.MergeSort(directionToCompare, indexToCompare, left, middleBound);
 var rightArray = this.MergeSort(directionToCompare, indexToCompare, middleBound + 1, rightBound);
 var resultArray = new Array();

 while ((left <= middleBound) && (right <= rightBound)) {
  leftValue = leftArray[left - leftBound][1];
  rightValue = rightArray[right - (middleBound + 1)][1];
  if (leftValue < rightValue) iResult = -1;
  else if (leftValue > rightValue) iResult = 1;
  else if (isNaN(leftValue) && !isNaN(rightValue)) iResult = 1;
  else if (!isNaN(leftValue) && isNaN(rightValue)) iResult = -1;
  else iResult = -1; // Set to -1 so that sort descending will reverse the order
  if (!directionToCompare) iResult = -iResult;
  if (iResult <= 0) resultArray[resultArray.length] = leftArray[left++ - leftBound];
  else resultArray[resultArray.length] = rightArray[right++  - (middleBound + 1)];
 }
 while (left <= middleBound) resultArray[resultArray.length] = leftArray[left++ - leftBound];
 while (right <= rightBound) resultArray[resultArray.length] = rightArray[right++ - (middleBound + 1)];

 return resultArray;
}
SmartTable.prototype.GetCellValue = function(row, col)
{
 return this.tableBodyRows[row].Cols[col].getAttribute('data-order');
}

function FixTableOnclick(tableId)
{//stops onclick from propagating when clicking elements inside a table
 var table = document.getElementById(tableId);
 for (var i = 1; i < table.rows.length; i++) {
  for (var j = 0; j < table.rows[i].childNodes.length; j++) {
   for (var k = 0; k < table.rows[i].childNodes[j].childNodes.length; k++) {
    //IE uses cancelBubble
    LAddEventListener(table.rows[i].childNodes[j].childNodes[k], 'onclick', function (evt) { if (evt.stopPropagation) evt.stopPropagation(); else evt.cancelBubble = true; });
   }
  }
 }
}

function RowClicked(evt, tableID, rowFunc)
{
 if (!evt.target) evt.target = evt.srcElement;
 target = evt.target;
 while (target.tagName != 'TR') target = target.parentNode;
 target.firstChild.firstChild.checked = !target.firstChild.firstChild.checked;

 var inputs = target.getElementsByTagName('input');
 var args = '';
 for (var i = 0; i < inputs.length; i++) {
  args += '&'
  args += GetArg(inputs[i]);
 }
 args = args.substr(1);

 if (typeof rowFunc == 'function') rowFunc(evt, tableID, args);
}
