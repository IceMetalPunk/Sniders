// (c) NCH Software. All rights reserved.

function convertTimeGMTToLocal(iTimeInMS) {
   var offset = (new Date).getTimezoneOffset();
   offset *= 60;
   offset *= 1000;
   return (iTimeInMS - offset);
}

function getDateString(iTimeInMS) {
   var date = new Date(iTimeInMS);
   var result = "";

   // Year
   result += date.getFullYear();

   result += "-";

   // Month
   if ((date.getMonth() + 1) < 10) result += "0";
   result += date.getMonth() + 1;

   result += "-";

   // Date
   if (date.getDate() < 10) result += "0";
   result += date.getDate();

   return result;
}

function getTimeString(iTimeInMS, bAppendSeconds) {
   var date = new Date(iTimeInMS);
   var result = "";

   // Hours
   if (date.getHours() < 10) result += "0";
   result += date.getHours();

   result += ":";

   // Minutes
   if (date.getMinutes() < 10) result += "0";
   result += date.getMinutes();

   // Seconds
   if (bAppendSeconds) {
      result += ":";
      if (date.getSeconds() < 10) result += "0";
      result += date.getSeconds();
   }

   return result;
}
