//Scripter: TengYong Ng
//Website: http://www.rainforestnet.com`
//Copyright (c) 2003 TengYong Ng
//Version: 1.8.3
//Contact: contact@rainforestnet.com

// This file has been heavily modified to open the calendar in a popup div instead 
// of a new window. However, all the calendar rendering logic is basically intact.

// This functionality is now dependant on popup.js. This will be included automatically 
// by calling DateTimeInit() from LWebDialog. If you are using NewCalendar outside of 
// LWebDialog, you must manually include popup.js in your webpage.

// These are now defined in LWebDialog::DateTimeInit()
//var aMonthNames = ["January", "February", "March", "April", "May", "June","July", "August", "September", "October", "November", "December"];
//var aWeekDayNames = ["Mon", "Tue", "Wed", "Thur", "Fri", "Sat", "Sun"];
//var aSelectDate = "Select Date";
//var aTodayIs = "Today is";

var WeekDayColor = "#C8D0D4";
var TodayColor = "#FFFF99";
var SelDateColor = "#D8E0E4";
var YearSelColor = "#000";

var cCalendar;
var dtSelected;
var wCalendar;
var dToday = new Date();

function NewCalendar(evt, iInputControlId) {
 if (document.getElementById(iInputControlId + 'cal') != null) {
  HidePopup(evt, iInputControlId + 'cal');
  return;
 }

 if (cCalendar) HidePopup(evt, cCalendar.Ctrl + 'cal');

 cCalendar = new Calendar(iInputControlId);

 var calendarControl = document.getElementById(iInputControlId);
 var dtExisting = calendarControl.value;
 if (dtExisting != "") {
  var Sp1 = dtExisting.indexOf("-", 0)
  var Sp2 = dtExisting.indexOf("-", (parseInt(Sp1) + 1));
  if ((Sp1 >= 0) && (Sp2 >= 0)) {
   var strMonth = dtExisting.substring(Sp1 + 1, Sp2);
   var strDate = dtExisting.substring(Sp2 + 1, Sp2 + 3);
   var strYear = dtExisting.substring(0, Sp1);

   var intMonth = parseInt(strMonth, 10) - 1;
   if ((intMonth >= 0) && (intMonth < 12))  cCalendar.Month = intMonth;

   var intDate = parseInt(strDate, 10);
   if ((intDate <= cCalendar.GetDaysInMonth()) && (intDate >= 1)) cCalendar.Date = strDate;

   var YearPattern = /^\d{4}$/;
   if (YearPattern.test(strYear)) cCalendar.Year = parseInt(strYear, 10);
  }
 }
 dtSelected = new Date(cCalendar.Year, cCalendar.Month, cCalendar.Date);

 wCalendar = document.createElement('div');
 wCalendar.id = iInputControlId + 'cal';
 wCalendar.style.backgroundColor = 'white';
 wCalendar.style.display = 'none';
 wCalendar.style.position = 'absolute';
 RenderCalendar();
 document.body.appendChild(wCalendar);
 TogglePopup(evt, iInputControlId + 'cal', iInputControlId, 0, 0);
 wCalendar.focus();
}

function RenderCalendar() {
 var YearPattern = /^\d{4}$/;
 if (!YearPattern.test(cCalendar.Year)) cCalendar.Year = dToday.getFullYear();

 var szForm = "<form name='Calendar'>";

 var szHeaderText = "<table border=1 style='border-spacing: 1px; vertical-align:top; width:260px;'><tr><td colspan=7><table border=0 style='border-spacing: 0px; width:200px;'><tr>";

 var SelectStr;
 szHeaderText += "<td align=left><select name='MonthSelector' onChange='cCalendar.Month = this.selectedIndex;RenderCalendar();'>";
 for (i = 0; i < 12; i++) {
  if (i == cCalendar.Month) SelectStr = "Selected ";
  else SelectStr = "";
  szHeaderText += "<option " + SelectStr + "value>" + aMonthNames[i] + "</option>";
 }
 szHeaderText += "</select></td>";

 szHeaderText += "<td align=right><a onclick='cCalendar.Year--;RenderCalendar()' style='text-decoration:none; font-weight:bold; color:" + YearSelColor + "; cursor:pointer;'>&lt;</a>&nbsp;<input type=text size=5 onKeyPress='return allowOnlyNumerals(event);' onchange=\"cCalendar.Year=this.value;setTimeout('RenderCalendar()', 200);\" value=" + cCalendar.Year + " style='font-family:Verdana; font-size: 13px; color:" + YearSelColor + "'/>&nbsp;<a onclick='cCalendar.Year++;RenderCalendar()' style='text-decoration:none; font-weight:bold; color:" + YearSelColor + "; cursor:pointer'>&gt;</a></td>";
 szHeaderText += "</tr></table></td></tr>";
 szHeaderText += "<tr style='background-color:#333333;'><td colspan=7 style='font-family:Verdana; font-size:13px; font-weight:bold; text-align:center; color:white'>" + aMonthNames[cCalendar.Month]+ " " + cCalendar.Year + "</td></tr>";

 szHeaderText += "<tr style='background-color:#788084'>";
 for (i = 0; i < 7; i++) szHeaderText += "<td style='text-align:center; font-family:Verdana; font-size=13px; color:white'>" + aWeekDayNames[i] + "</td>";
 szHeaderText += "</tr>";

 szForm += szHeaderText;

 var dCalendarDate = new Date(cCalendar.Year, cCalendar.Month);
 dCalendarDate.setDate(1);
 var dFirstDay = dCalendarDate.getDay();
 dFirstDay -= 1;
 if (dFirstDay == -1) dFirstDay = 6;
 var szCalendarData = "<tr>";
 var iDayCount = 0;
 for (i = 0; i < dFirstDay; i++) {
  szCalendarData += NewCell();
  iDayCount += 1;
 }
 for (j = 1; j <= cCalendar.GetDaysInMonth(); j++) {
  var szCell;
  iDayCount += 1;
  if ((j == dToday.getDate()) && (cCalendar.Month == dToday.getMonth()) && (cCalendar.Year == dToday.getFullYear()))
   szCell = NewCell(j, true, TodayColor, false);
  else {
   if ((j == dtSelected.getDate()) && (cCalendar.Month == dtSelected.getMonth())&&(cCalendar.Year == dtSelected.getFullYear())) {
    szCell = NewCell(j, true, SelDateColor, true);
   } else {
    if (iDayCount % 7 == 0) szCell = NewCell(j, false, WeekDayColor, false);
    else if ((iDayCount + 1) % 7 == 0) szCell = NewCell(j, false, WeekDayColor, false);
    else szCell = NewCell(j, null, WeekDayColor);
   }
  }
  szCalendarData = szCalendarData + szCell;

  if ((iDayCount % 7 == 0)&&(j < cCalendar.GetDaysInMonth())) szCalendarData = szCalendarData + "</tr><tr>";
 }
 szForm += szCalendarData;

 var dDate = new Date();
 var iMonth = dDate.getMonth() + 1;
 var iDate = dDate.getDate();
 var iYear = dDate.getFullYear();
 if (iDate < 10) iDate = "0" + iDate;
 if (iMonth < 10) iMonth = "0" + iMonth;
 szForm += "<tr><td colspan=7 align=center>";
 szForm += aTodayIs;
 szForm += " " + iYear + "-" + iMonth + "-" + iDate + "</td></tr>";

 szForm += "</table></form>";
 wCalendar.innerHTML = szForm;
}

function setText(ctrl, year, month, day)
{
 var txt = document.getElementById(ctrl);
 txt.value = "" + year + "-" + month + "-" + day;

 //On IE
 if (txt.fireEvent) {
  txt.fireEvent('onchange');
 }
 //On Gecko based browsers
 if (document.createEvent) {
  var evt = document.createEvent('HTMLEvents');
  if (evt.initEvent) {
   evt.initEvent('change', true, true);
  }
  if (txt.dispatchEvent) {
   txt.dispatchEvent(evt);
  }
 }
}

function NewCell(day, bHighLight, cBkColor, bInsetBorder) {
 var szHTMLText = "<td style='text-align:center; width:80px; font-family:Verdana; font-size:13px;";
 if (bInsetBorder) szHTMLText += " border-style: ridge;";
 if (cBkColor != null) szHTMLText +=" background-color:" + cBkColor + ";";
 if (bHighLight) szHTMLText += " font-weight:bold;";
 szHTMLText += "'>";

 if (day == null) day = "";
 else if (day < 10) day = "0" + day;
 var MonthDigit = cCalendar.Month + 1;
 if (MonthDigit < 10) MonthDigit = "0" + MonthDigit;

 szHTMLText += "<a onclick=\"setText('" + cCalendar.Ctrl + "', '" + cCalendar.Year  + "', '" + MonthDigit + "', '" + day + "');HidePopup(event, '" + cCalendar.Ctrl + "' + 'cal');\" style=\"text-decoration: none; color: black; cursor:pointer;\">" + day + "</a></td>";

 return szHTMLText;
}

function allowOnlyNumerals(event){
 var charCode = ('charCode' in event) ? event.which : event.keyCode;
 if((charCode==13) || (charCode > 31 && (charCode < 48 || charCode > 57))) return false;
 return true;
}

function Calendar(idControl) {
 this.Date = dToday.getDate();
 this.Month = dToday.getMonth();
 this.Year = dToday.getFullYear();
 this.Ctrl = idControl;
}

function GetDaysInMonth() {
 var DaysInMonth = [31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31];
 if ((this.Year % 4) == 0) {
  if (!((this.Year % 100 == 0) && (this.Year % 400) != 0)) DaysInMonth[1] = 29;
 }
 return DaysInMonth[this.Month];
}
Calendar.prototype.GetDaysInMonth = GetDaysInMonth;
