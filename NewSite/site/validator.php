<?php

global $formname, $isspam;
$formname = $PHP_SELF;
$warn_body = "";
				
 function _local_replace_bad($value) {
   global $isspam, $formname, $report_to, $warn_body, $warn_header;

   # mail adress(ess) for reports...
   $report_to = "ajcannane+scheffspam@gmail.com";
   # array holding strings to check, we do not trust these strings in $_POST
   $suspicious_str = array
   (
       "content-type:"
       ,"charset="
       ,"mime-version:"
       ,"multipart/mixed"
       ,"bcc:"
   );
   $suspect_found = false;
   // remove added slashes from $value...
   $value = stripslashes($value);
   # checks if $value contains $suspect...
   foreach($suspicious_str as $suspect) {
   if(strpos(strtolower($value),$suspect)!==false) {
       //if(eregi($suspect, strtolower($value))) {
           # if we found some suspicios string, then we add our string, so it
           # will be messed a little bit. :)
           $suspect_found = true;
       $value = str_ireplace($suspect, "(anti-spam-".$suspect.")", $value); // Use this, if you use PHP5
       //$value = preg_replace('/'.preg_quote($suspect,'/').'/i', "(anti-spam-".$suspect.")", $value); // Use this, if you use PHP < 5
           //$value = eregi_replace($suspect, "(anti-spam-".$suspect.")", $value);
       }
   }
   if ($suspect_found) {
       # if at least one suspicios string was found, then do something more
       $ip = (empty($_SERVER['REMOTE_ADDR'])) ? 'empty' : $_SERVER['REMOTE_ADDR'];
       $rf = (empty($_SERVER['HTTP_REFERER'])) ? 'empty' : $_SERVER['HTTP_REFERER'];
       $ua = (empty($_SERVER['HTTP_USER_AGENT'])) ? 'empty' : $_SERVER['HTTP_USER_AGENT'];
       $ru = (empty($_SERVER['REQUEST_URI'])) ? 'empty' : $_SERVER['REQUEST_URI'];
       $rm = (empty($_SERVER['REQUEST_METHOD'])) ? 'empty' : $_SERVER['REQUEST_METHOD'];
       # very often HTTP_USER_AGENT is empty. We consider this is 100% spam
       if ($suspect_found && $ua == "empty") {
           exit();
       }
       # if we are here, then HTTP_USER_AGENT is not empty. this is only 80-90% that it is spam
       # Remember, that POST values were already changed. But we still want to inform our
       # admin about this suspicios request.
       if(isset($report_to) && !empty($report_to))
   {
       
	   $warn_header =  "Stopped possible mail-injection @ " .
                   $_SERVER['HTTP_HOST'] . $formname . " by " . $ip .
                   " (" . date('d/m/Y H:i:s') . ")\r\n\r\n" .
                   "*** IP/HOST\r\n" . $ip . "\r\n\r\n" .
                   "*** USER AGENT\r\n" . $ua . "\r\n\r\n" .
                   "*** REFERER\r\n" . $rf . "\r\n\r\n" .
                   "*** REQUEST URI\r\n" . $ru . "\r\n\r\n" .
                   "*** REQUEST METHOD\r\n" . $rm . "\r\n\r\n";
 
	   
	   $warn_body .=
                  "*** SUSPECT\r\n-----\r\n" . $value . "\r\n-----\n";
           
       } # if report
	   $isspam="yes";
   } # if suscpect found
   return($value);
 }
# what we do - is we simply check all posted values.
if(is_array($_POST))
{
   foreach($_POST as $f=>$v) {
       $_POST[$f] = _local_replace_bad($v);
   }
}
if(is_array($HTTP_POST_VARS))
   $HTTP_POST_VARS=$_POST;
if(is_array($_GET))
{
   foreach($_GET as $f=>$v) {
       $_GET[$f] = _local_replace_bad($v);
   }
}
if(is_array($HTTP_GET_VARS))
   $HTTP_GET_VARS=$_GET;

if(is_array($_REQUEST))
{
   foreach($_REQUEST as $f=>$v) {
       $_REQUEST[$f] = _local_replace_bad($v);
   }
}

if ($warn_body !=""){
@mail(
               $report_to,
               "[ABUSE] [SUSPECT] @ " . $_SERVER['HTTP_HOST'] . " by " . $ip,
               $warn_header . $warn_body
           );
}

 # if register_globals is set to "on", then we should overwrite them once again.
 if (ini_get("register_globals") == 1)
   extract($_POST, EXTR_OVERWRITE);
?>
