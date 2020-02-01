<% @Language = "VBScript" %>
<% Response.buffer = true %>
<%
'//============================================================+
'// File name   : page_break.asp
'// Begin       : 2003-11-27
'// Last Update : 2003-11-29
'//
'// Description : Example of ASP page to handle requests by the 
'//               JRelaxTimer applet.
'//
'// Author: Nicola Asuni
'//
'// (c) Copyright:
'//               Tecnick.com S.r.l.
'//               Via Ugo Foscolo n.19
'//               09045 Quartu Sant'Elena (CA)
'//               ITALY
'//               www.tecnick.com
'//               info@tecnick.com
'//============================================================+

' dim parameters
Dim l ' applet license number
Dim t ' break type
Dim b ' current break number
Dim i ' time interval in minutes

' get parameters from url
l = Trim(Request("l"))
b = Trim(Request("b"))
t = Trim(Request("t"))
i = Trim(Request("i"))

' -------------------------------------------------------------
' Insert here your methods to display the apropriate content...

Response.write "<h1>License number:" + l + "</h1>"
Response.write "<h1>Break Type:" + t + "</h1>"
Response.write "<h1>Current break:" + b + "</h1>"
Response.write "<h1>Interval:" + i + " [minutes]</h1>"
' -------------------------------------------------------------

'//============================================================+
'// END OF FILE
'//============================================================+
%>



