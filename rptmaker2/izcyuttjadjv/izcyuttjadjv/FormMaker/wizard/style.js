/*

Text[...]=[title,text]

Style[...]=[TitleColor,TextColor,TitleBgColor,TextBgColor,TitleBgImag,TextBgImag,TitleTextAlign,TextTextAlign, TitleFontFace, TextFontFace, TipPosition, StickyStyle, TitleFontSize, TextFontSize, Width, Height, BorderSize, PadTextArea, CoordinateX , CoordinateY, TransitionNumber, TransitionDuration, TransparencyLevel ,ShadowType, ShadowColor]
*/

var FiltersEnabled = 1 // if your not going to use transitions or filters in any of the tips set this to 0

//**************************** wizard Step 2 **************************************************
Step_2[0]=["Smart Form Maker","The database server can be in the form of a hostname, such as db1.myserver.com, or as an IP-address, such as 192.168.0.1 "]
Step_2[1]=["Smart Form Maker","The username used to connect to the database server. "]
Step_2[2]=["Smart Form Maker","The password is used together with the username, which forms the database user account."]
Step_2[3]=["Smart Form Maker","The database used to hold the data."]
Step_2[4]=["Smart Form Maker","The Table that you want to make tasks on it."]
Step_2[5]=["Smart Form Maker","This action will allow you to insert data into the table."]
Step_2[6]=["Smart Form Maker","This action will allow you to update data into the table."]
Step_2[7]=["Smart Form Maker","This action will allow you to delete rows from the table."]
Step_2[8]=["Smart Form Maker","Enter a Valid SQL statment and the system will take fields in the query and will reterive not null values in next step <br/><b>Note</b>if you have multiple table query the system will take one of them and you can make relations in step 4 if you selcted Auto Detect Relations in previous step not no."]
Step_2[9]=["Smart Form Maker","This action will allow you to automaticly detect relationships with the table you had selected or you can determine the relations with the table in next steps."]
Step_2[10]=["Smart Form Maker","Select the data source of the form, it could be 'Table' or 'SQL Query'"]
//*************************** wizard step 3 ****************************************************
Step_3[0] = ["Smart Form Maker","Select the fields that you would like to be managed in the form. <br><b>Note:</b>Red fields are not null so it's required in insert task."];

//*************************** wizard step 4 ****************************************************
Step_4[0] = ["Smart Form Maker","Manage form settings like lables, text values, null values and validation."];
Step_4[1] = ["Smart Form Maker","Select the column that will displayed as text in drop down list."];

Step_4[2] = ["Smart Form Maker","Select the table that you want to make a relation with it."];
Step_4[3] = ["Smart Form Maker","Select the field that will displayed as adrop down list text value."];
Step_4[4] = ["Smart Form Maker","This is the fields that will be dispalyed in the generated form."];
Step_4[5] = ["Smart Form Maker","Here you can set the label names of every field."];
Step_4[6] = ["Smart Form Maker","You can make the field required or not. </br><b>Note:</b> Notnull fields and primary keys can not be change."];
Step_4[7] = ["Smart Form Maker","Create relationships with another tables."]; 
Step_4[8] = ["Smart Form Maker","Make some validation on the field like regular expressions, range of numbers and dates ,allow special characters or not and so on."];
Step_4[9] = ["Smart Form Maker","This is the error message that displayed when data entered in field not suitable"];
Step_4[10] = ["Smart Form Maker","Please select a regular expression template to validate the field or you can enter a custom regular expression in the textbox"]
Step_4[11] = ["Smart Form Maker","This option to avoid entering special characters like ('!','@','#','$','%','^','&','*','(',')','|')"]
Step_4[12] = ["Smart Form Maker","Select the range of the number to validate"]
Step_4[13] = ["Smart Form Maker","Select the date range to validate"]

//* wizard step 5 ***
Step_5[1] = ["Smart Form Maker","Details form data source for the detail form"]
Step_5[2] = ["Smart Form Maker","Foreign Key to join"]

//*************************** wizard step 6 ****************************************************
Step_6[0] = ["Smart Form Maker","This is a list of the available Cascading Style Sheet (CSS) styles , pick the one you want to be used in the report ."];
Step_6[1] = ["Smart Form Maker","You can create a new cascade style sheet (CSS) style but that is NOT  recommended unless you have a good knowledge of cascade style sheets"];
Step_6[2] = ["Smart Form Maker","You can edit the details of the selected Cascade style sheet (CSS) style  but that is NOT recommended unless you have a good knowledge of cascade style sheets"];
//*************************** wizard step 7 ****************************************************
Step_7[0] = ["Smart Form Maker","Form Title"];
Step_7[3] = ["Smart Form Maker","This name will be used to save the form on the server."];
Step_7[4] = ["Smart Form Maker","This the max number of records that could be displayed in one page. 'Next' and 'Previous' links will be shown in your form to navigate between pages."];

//************************************************************************


//******************************* tables relations ********************************************

tables_relations[0] = ["Smart Form Maker","Select the two related tables"];
tables_relations[1] = ["Smart Form Maker","Select the primary key and the foreign key of the relation"];
tables_relations[2] = ["Smart Form Maker","Click to add a relation "];
tables_relations[3] = ["Smart Form Maker","Select the relation which you like to remove then click to remove it"];

//*************************************************************************************************

New_Style[0] = ["Smart Form Maker","Enter style name. Spaces are not allowed"];
New_Style[1] = ["Smart Form Maker","Enter style content in this area using CSS. Please do not change classes names nor classes order "];




/*Style[0]=["white","black","#000099","#E8E8FF","","","","","","","","","","",200,"",2,2,10,10,51,1,0,"",""]
Style[1]=["white","black","#000099","#E8E8FF","","","","","","","center","","","",200,"",2,2,10,10,"","","","",""]
Style[2]=["white","black","#000099","#E8E8FF","","","","","","","left","","","",200,"",2,2,10,10,"","","","",""]
Style[3]=["white","black","#000099","#E8E8FF","","","","","","","float","","","",200,"",2,2,10,10,"","","","",""]
Style[4]=["white","black","#000099","#E8E8FF","","","","","","","fixed","","","",200,"",2,2,1,1,"","","","",""]
Style[5]=["white","black","#000099","#E8E8FF","","","","","","","","sticky","","",200,"",2,2,10,10,"","","","",""]
Style[6]=["white","black","#000099","#E8E8FF","","","","","","","","keep","","",200,"",2,2,10,10,"","","","",""]
Style[7]=["white","black","#000099","#E8E8FF","","","","","","","","","","",200,"",2,2,40,10,"","","","",""]
Style[8]=["white","black","#000099","#E8E8FF","","","","","","","","","","",200,"",2,2,10,50,"","","","",""]
*/
Style=["white","black","orange","#E8E8FF","","","","","","","","","","",200,"",2,2,10,10,24,0.5,75,"simple","gray"]
/*
Style[10]=["white","black","black","white","","","right","","Impact","cursive","center","",3,5,200,150,5,20,10,0,50,1,80,"complex","gray"]
Style[11]=["white","black","#000099","#E8E8FF","","","","","","","","","","",200,"",2,2,10,10,51,0.5,45,"simple","gray"]
Style[12]=["white","black","#000099","#E8E8FF","","","","","","","","","","",200,"",2,2,10,10,"","","","",""]
*/
applyCssFilter()

