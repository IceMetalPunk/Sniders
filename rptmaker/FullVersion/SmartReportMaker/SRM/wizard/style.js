/*

Text[...]=[title,text]

Style[...]=[TitleColor,TextColor,TitleBgColor,TextBgColor,TitleBgImag,TextBgImag,TitleTextAlign,TextTextAlign, TitleFontFace, TextFontFace, TipPosition, StickyStyle, TitleFontSize, TextFontSize, Width, Height, BorderSize, PadTextArea, CoordinateX , CoordinateY, TransitionNumber, TransitionDuration, TransparencyLevel ,ShadowType, ShadowColor]
*/

var FiltersEnabled = 1 // if your not going to use transitions or filters in any of the tips set this to 0

//**************************** wizard Step 2 **************************************************
Step_2[0]=["Smart Report Maker","The database server can be in the form of a hostname, such as db1.myserver.com, or as an IP-address, such as 192.168.0.1 "]
Step_2[1]=["Smart Report Maker","The username used to connect to the database server. "]
Step_2[2]=["Smart Report Maker","The password is used together with the username, which forms the database user account."]
Step_2[3]=["Smart Report Maker","The database used to hold the data."]
Step_2[4]=["Smart Report Maker","Select the data source of the report, it could be 'Table' or 'SQL Query'"]
//*************************** wizard step 3 ****************************************************
Step_3[0] = ["Smart Report Maker","Select the table which you will use to create the report ."];
//*************************** wizard step 3 sql ****************************************************
Step_3_sql[0] = ["Smart Report Maker","The SQL statement used to create the report.<br/><b>Note:</b> avoid using 'order by' because it will be Done visually in a next step."];
//*************************** wizard step 4 ****************************************************
Step_4[0] = ["Smart Report Maker","Select the fields that you would like to be shown in the report"];
//*************************** wizard step 5 ****************************************************
Step_5[0] = ["Smart Report Maker","You can group records in the report, the tool supports unlimited grouping levels"];
//*************************** wizard step 6 ****************************************************
Step_6[0] = ["Smart Report Maker","You can sort records by up to five fields, in either ascending or descending order."];
//*************************** wizard step 7 ****************************************************
Step_7[0] = [ "Smart Report Maker","How would you like to lay out your report?"];
//*************************** wizard step 8 ****************************************************
Step_8[0] = ["Smart Report Maker","This is a list of the available Cascading Style Sheet (CSS) styles , pick the one you want to be used in the report ."];
Step_8[1] = ["Smart Report Maker","You can create a new cascade style sheet (CSS) style but that is NOT  recommended unless you have a good knowledge of cascade style sheets"];
Step_8[2] = ["Smart Report Maker","You can edit the details of the selected Cascade style sheet (CSS) style  but that is NOT recommended unless you have a good knowledge of cascade style sheets"];
//*************************** wizard step 9 ****************************************************
Step_9[0] = ["Smart Report Maker","Report Title"];
Step_9[1] = ["Smart Report Maker","Report Footer. It could contain HTML tags."];
Step_9[2] = ["Smart Report Maker","Report Header. It could contain HTML tags"];
Step_9[3] = ["Smart Report Maker","This name will be used to save the report on the server."];
Step_9[4] = ["Smart Report Maker","This the max number of records that could be displayed in one page. 'Next' and 'Previous' links will be shown in your report to navigate between pages."];
//************************************************************************
Step_s[0] = ["Smart Report Maker","Select the function which you want to apply"];
Step_s[1] = ["Smart Report Maker","Select the column on which you want to apply the function. it should be a numerical column"];
Step_s[2] = ["Smart Report Maker","GROUP BY specifies that all sets of values selected are grouped together according to their unique values in the value list. This produces a summary table with one entry per group of records. For example, to calculate the average salary for male and female employees your options should be : <br/>  Function = Average <br/> Affected column =Salary <br/>  Group by column = Gender"];


//******************************* tables relations ********************************************

tables_relations[0] = ["Smart Report Maker","Select the two related tables"];
tables_relations[1] = ["Smart Report Maker","Select the primary key and the foreign key of the relation"];
tables_relations[2] = ["Smart Report Maker","Click to add a relation "];
tables_relations[3] = ["Smart Report Maker","Select the relation which you like to remove then click to remove it"];

//*************************************************************************************************

New_Style[0] = ["Smart Report Maker","Enter style name. Spaces are not allowed"];
New_Style[1] = ["Smart Report Maker","Enter style content in this area using CSS. Please do not change classes names nor classes order "];




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

