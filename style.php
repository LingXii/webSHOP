<style>

body {
    overflow: scroll;
    background-color: #f8f8ff;
    padding: 0px;
    margin: 0px;
    width: 100%;
}    
    
input[type=text].login, select {
    width: 100%;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

input[type=password].login, select {
    width: 100%;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

input[type=submit].login {
    width: 100%;
    background-color: #5d70dc;
    color: white;
    padding: 14px 20px;
    margin: 8px 0;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

input[type=submit].login:hover {
    background-color: #4459d4;
}

input[type=submit].login2 {
    width: 50%;
    background-color: #5d70dc;
    color: white;
    padding: 14px 20px;
    margin: 8px 0;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

input[type=submit].login2:hover {
    background-color: #4459d4;
}

div.form{
    width: 35%;
    border-radius: 5px;
    background-color: #f2f2f2;
    padding: 20px;
    margin: 0 auto;
}

div.header {
    overflow: hidden;
    background-color: #bbc3ef;
    padding: 0px;
    margin: 0px;
    width: 100%;
}

a.title_conn {
    float: left;
    display: block;
    color: #001aaf;
    text-align: center;
    padding: 10px;
    text-decoration: none;
    font-size: 30px;
    font-weight: bold;
}

a.title {
    float: left;
    display: block;
    color: #001aaf;
    text-align: center;
    padding: 10px;
    text-decoration: none;
    font-size: 30px;
    font-weight: bold;
}

a.title:hover {
    color: #324acd;
}

a.topnav {
    float: right;
    display: block;
    color: #ffffff;
    text-align: center;
    padding: 15px 15px;
    text-decoration: none;
    font-size:20px;
    font-weight:bold;
}

a.topnav:hover {
    background-color: #ddd;
    color: black;
}

#posts
{
    width:98%;
    border-collapse:collapse;
    margin: 0 auto;
}
#posts td, #posts th 
{
    font-size:1em;
    border:1px solid #ffffff;
    padding:12px 7px;
}
#posts th 
{
    font-size:1.3em;
    text-align:left;
    padding-top:10px;
    padding-bottom:10px;
    background-color:#00158c;
    color:#ffffff;
}
#posts td 
{
    font-size:1em;
    text-align:left;
    color:#000000;
}
#posts tr.postodd 
{
    color:#000000;
    background-color:#eaedff;
}
#posts tr.posteven 
{
    color:#000000;
    background-color:#ced6ff;
}
#posts a 
{
    text-decoration: none;
    font-size: 17px;
    color: #000077;
}
#posts a:hover 
{
    text-decoration: underline; 
}
#posts a.t
{
    text-decoration: none;
    font-size: 17px;
    font-weight:bold;
    color: #ff0000;
}
#posts a.t:hover 
{
    text-decoration: underline; 
    font-weight:bold;
}

input[type=submit].posts {
    width: 80px;
    background-color: #5d70dc;
    color: white;
    padding: 5px;
    margin: 2px;
    border: 0px;
    border-radius: 4px;
    cursor: pointer;
    float: right;
}
input[type=submit].posts:hover {
    background-color: #4459d4;
}

input[type=submit].items {
    width: 100%;
    background-color: #5d70dc;
    color: white;
    padding: 5px;
    margin: 2px;
    border: 0px;
    border-radius: 4px;
    cursor: pointer;
    float: right;
}
input[type=submit].items:hover {
    background-color: #4459d4;
}

input[type=submit].warn {
    width: 100%;
    background-color: #ff0000;
    color: white;
    padding: 5px;
    margin: 2px;
    border: 0px;
    border-radius: 4px;
    cursor: pointer;
    float: right;
}
input[type=submit].warn:hover {
    background-color: #dd0000;
}

#notice
{
    width:45%;
    border-collapse:collapse;
    margin: 15px;
}
#notice td, #notice th 
{
    font-size:1em;
    border:1px solid #ffffff;
    padding:12px 7px;
}
#notice th 
{
    font-size:1.3em;
    text-align:left;
    padding-top:10px;
    padding-bottom:10px;
    background-color:#00158c;
    color:#ffffff;
}
#notice td 
{
    font-size:1em;
    text-align:left;
    color:#000000;
}
#notice tr.postodd 
{
    color:#000000;
    background-color:#eaedff;
}
#notice tr.posteven 
{
    color:#000000;
    background-color:#ced6ff;
}
#notice a 
{
    text-decoration: none;
    font-size: 17px;
    color: #000077;
}
#notice a:hover 
{
    text-decoration: underline; 
}
#notice a.t
{
    text-decoration: none;
    font-size: 17px;
    font-weight:bold;
    color: #ff0000;
}
#notice a.t:hover 
{
    text-decoration: underline; 
    font-weight:bold;
}

a.edit_btn {
    float: left;
    display: block;
    color: #ffffff;
    text-align: center;
    padding: 12px;
    text-decoration: none;
    background-color: #00158c;
    font-size:18px;
    font-weight:bold;
    margin: 0px 20px;
    float: right;
}

a.edit_btn:hover {
    background-color: #ced6ff;
    color: black;
}

a.page_btn {
    float: left;
    display: block;
    color: #000000;
    text-align: center;
    padding: 12px;
    text-decoration: none;
    background-color: #ced6ff;
    font-size:18px;
    font-weight:bold;
    margin: 1px;
    float: left;
}

a.page_btn:hover {
    background-color: #eaedff;
    color: black;
}

a.npage_btn {
    float: left;
    display: block;
    color: #ffffff;
    text-align: center;
    padding: 12px;
    text-decoration: none;
    background-color: #00158c;
    font-size:18px;
    font-weight:bold;
    margin: 1px;
    float: left;
}

a.npage_btn:hover {
    background-color: #ced6ff;
    color: black;
}

#editor input[type=text], select {
    width: 90%;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    font-size: 17px;
    margin: 10px
}

#editor textarea, select {
    width: 100%;
    height: 350px;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    font-size: 15px;
}

#editor input[type=submit] {
    width: 100%;
    background-color: #5d70dc;
    color: white;
    padding: 14px 20px;
    margin: 8px 0;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

#editor label
{
    float:right;
}

#editor input[type=submit]:hover {
    background-color: #4459d4;
}

#editor input[type=file] 
{  
    margin: 15px
}  

div.editor{
    width: 60%;
    border-radius: 5px;
    background-color: #f2f2f2;
    padding: 20px;
    margin: 0 auto;
}

#reader
{
    width:98%;
    border-collapse:collapse;
    margin: 0 auto;
}
#reader td, #reader th 
{
    font-size:1em;
    border:1px solid #ffffff;
    padding:12px 7px;
}
#reader th 
{
    font-size:1.3em;
    text-align:left;
    padding-top:10px;
    padding-bottom:10px;
    background-color:#00158c;
    color:#ffffff;
}
#reader td 
{
    font-size:1em;
    text-align:left;
    color:#000000;
    min-height: 80px;
}
#reader tr.postodd 
{
    color:#000000;
    background-color:#eaedff;
}
#reader tr.posteven 
{
    color:#000000;
    background-color:#ced6ff;
}
#reader a 
{
    text-decoration: none;
    font-size: 17px;
    color: #000077;
}
#reader a:hover 
{
    text-decoration: underline; 
}
#reader p
{
    text-decoration: none;
    font-size: 17px;
    color: #000000;
    margin: 0 auto;
}

div.userleft{
    width: 20%;
    float: left;
    border: 0px;
    padding: 0px;
    margin: 10px;
}

div.userright{
    width: 77%;
    float: right;
    border: 0px;
    padding: 0px;
    margin: 10px;
}

input[type=submit].button {
    width: 130px;
    background-color: #5d70dc;
    color: white;
    padding: 10px 15px;
    margin: 3px 5px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}
input[type=submit].button:hover {
    background-color: #4459d4;
}

input[type=text].button, select {
    width: 60%;
    padding: 12px 20px;
    margin: 8px 0;
    display: inline-block;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

a.button
{
    text-decoration: none;
    font-size: 17px;
    color: #000077;
}
a.button:hover 
{
    text-decoration: underline; 
}

div.middle{
    width: 62%;
    border-radius: 5px;
    padding: 0px;
    margin: 0 auto;
}

div.middle_big{
    width: 100%;
    border-radius: 5px;
    padding: 0px;
    margin: 0 auto;
}

#people
{
    width:98%;
    margin: 0 auto;
}
#people td 
{
    font-size:1em;
    padding:5px 5px;
    border: 0px;
    text-align:left;
    color:#000000;
}
#people a 
{
    text-decoration: none;
    font-size: 17px;
    color: #000077;
}
#people a:hover 
{
    text-decoration: underline; 
}
#people input[type=submit] {
    width: 130px;
    background-color: #5d70dc;
    color: white;
    padding: 4px;
    margin: 0px;
    border: 0px;
    border-radius: 4px;
    cursor: pointer;
    float: right;
}
#people input[type=submit]:hover {
    background-color: #4459d4;
}
#people input[type=text], select {
    width: 60%;
    padding: 4px;
    margin: 0px;
    display: inline-block;
    border: 0px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
}

input[type=submit].lock {
    width: 22px;
    height: 22px;
    background: url('files/lock.png');
    float: right;
    padding: 0px;
    margin: 0px;
    border: 0px;
    cursor: pointer;
}

input[type=submit].lock:hover {
    background: url('files/lock_h.png');
}

input[type=submit].sbutton {
    width: 80px;
    background-color: #5d70dc;
    color: white;
    padding: 4px;
    margin: 0px;
    border: 0px;
    border-radius: 4px;
    cursor: pointer;
    float: right;
}
input[type=submit].sbutton:hover {
    background-color: #4459d4;
}

#list
{
    width:100%;
    margin: 0 auto;
}
#list td 
{
    font-size:1em;
    padding:0px;
    border: 0px;
    text-align:left;
    color:#000000;
}
#list a 
{
    text-decoration: none;
    font-size: 17px;
    color: #000077;
}
#list a:hover 
{
    text-decoration: underline; 
}

</style>