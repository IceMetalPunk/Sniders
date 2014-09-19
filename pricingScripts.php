<?php
  $link=mysql_connect("localhost", "root", "tux898");
  $db=mysql_select_db("sniders2013", $link);
?>

function PLToggleGroup() {
  var checked=document.changeForm.group.checked;
  if (checked) {
    document.changeForm.description.value="";
    document.changeForm.description.style.display="none";
    document.changeForm.description.disabled=true;
    document.getElementById("changeDefaults").style.display="none";
    items=document.getElementById("changeDefaults").getElementsByTagName("input");
    for (i in items) { items[i].value=""; items[i].disabled=true; }
  }
  else {
    document.getElementById("confirmationMessage").innerHTML="";
    changeConfirmed=false;
    document.changeForm.description.style.display="";
    document.changeForm.description.disabled=false;
    document.getElementById("changeDefaults").style.display="";
    items=document.getElementById("changeDefaults").getElementsByTagName("input");
    for (i in items) { items[i].disabled=false; }
  }
}
function PLToggleAdd() {
  document.addForm.sub.accessKey="C";
  document.changeForm.sub.accessKey="";
  document.getElementById("addSection").style.display="";
  document.getElementById("changeSection").style.display="none";
  document.addForm.type.focus();
}
function PLToggleChange() {
  document.addForm.sub.accessKey="";
  document.changeForm.sub.accessKey="U";
  document.getElementById("addSection").style.display="none";
  document.getElementById("changeSection").style.display="";
  document.changeForm.type.focus();
}

var changeConfirmed=false;
function ConfirmChange() {
  if (!changeConfirmed && document.changeForm.group.checked) {
    changeConfirmed=true;
    document.getElementById("confirmationMessage").innerHTML="<span class='error' style='display:inline; font-size:14pt'>You are changing several items at once. Are you sure you want to continue?</span>";
    return false;
  }
  else { return true; }
}

PLItemTypes=[<?php
  $query="SELECT DISTINCT `P-Type` FROM `t-price`";
  $query=mysql_query($query);
  $out="";
  
  while ($result=mysql_fetch_assoc($query)) {
    $val=$result["P-Type"];
    if ($val=="") { continue; }
    if ($out!="") { $out.=", "; }
    $out.='{"label": "'.ucwords(strtolower($val)).'", "value": "'.$val.'"}';
  }
  echo $out;
?>];

function PLInit() {
  document.changeForm.group.checked=false;
  $(document.addForm.type).autocomplete({

    /* Take the autocomplete options from the PLItemTypes array we built in PHP */
    source: function(req, response) { 

        /* Make a pattern out of whatever the person typed */
        var re = $.ui.autocomplete.escapeRegex(req.term); 

        /* The ^ in a Regular Expression pattern means "at the beginning of the text" */
        var matcher = new RegExp( "^" + re, "i" ); 
    
        /* Respond with only those items in the itemStyles array whose labels match that pattern */
        response($.grep(PLItemTypes, function(item){ 
            return matcher.test(item.label); }) ); 
       },
    autoFocus: true,

    /* When an autocomplete item is selected, update the field */
    select: function(e, ui) {
      document.addForm.type.value=ui.item.value;
      return false;
    }
  });
  
  $(document.changeForm.type).autocomplete({

    /* Take the autocomplete options from the PLItemTypes array we built in PHP */
    source: function(req, response) { 

        /* Make a pattern out of whatever the person typed */
        var re = $.ui.autocomplete.escapeRegex(req.term); 

        /* The ^ in a Regular Expression pattern means "at the beginning of the text" */
        var matcher = new RegExp( "^" + re, "i" ); 
    
        /* Respond with only those items in the itemStyles array whose labels match that pattern */
        response($.grep(PLItemTypes, function(item){ 
            return matcher.test(item.label); }) ); 
       },
    autoFocus: true,

    /* When an autocomplete item is selected, update the field */
    select: function(e, ui) {
      document.changeForm.type.value=ui.item.value;
      return false;
    }
  });
}

<?php mysql_close($link); ?>