<?php

/* Captcha Validation */
   session_start();
   if(($_SESSION['security_code'] == $_POST['security_code']) && (!empty($_SESSION['security_code'])) ) {

		/* Only use this if you have a HTML form posting to a PHP script, this is not a php form! */
		include 'validator.php';
		
		if ($isspam != "yes")
		{
			$to='scheffs@bigpond.com';
			//$to='sudeep.viraj@bakasit.com.au';
			while (list($key,$val) = each($_POST)){ 
				if (($val !="") and ($val !="Submit Enquiry")) {
				$formmessage.="$key = $val\n";		
				}
			}
			//echo $formmessge;
			#reset the from: address for a neater look 
			$header.="From: ".$_REQUEST['name']." <".$_REQUEST['email'].">\n";
			$header.="Return-Path: ".$_REQUEST['name']." <".$_REQUEST['email'].">\n";
			#if there's an email element, use it for reply-to 
			if ($email) 
			$header.="Reply-To: <".$_REQUEST['email'].">\n";
			#log the IP Address of the sender. 
			if ($HTTP_X_FORWARDED_FOR) 
			$header.="X-Originating-IP: $HTTP_X_FORWARDED_FOR via $REMOTE_ADDR\n"; 
			else 
			$header.="X-Originating-IP: $REMOTE_ADDR\n";
			mail($to,"Correspondance from your Website",$formmessage,$header);
			header("Location: contact.php?submit=1");
		}
		
		// Finishing Captcha code
		unset($_SESSION['security_code']);
   } else {
     // echo 'Sorry, you have provided an invalid security code';
	  header("Location: error.html");
   }
	
	?>