<?php
// Initiate the autoloader. The file should be generated by Composer.
// You will provide your own autoloader or require the files directly if you did
// not install via Composer.
require_once __DIR__ . '/./vendor/autoload.php';

// Register API keys at https://www.google.com/recaptcha/admin
$siteKey = '6Lc5Jw0UAAAAAETBRcbNldJHFFbsxKN-ZzaN1wiG';
$secret = '6Lc5Jw0UAAAAAKUkceAYCZoaH5Ysd7cxI2GbPoyr';
$enquiryTo = 'ajcannane@gmail.com';

// reCAPTCHA supported 40+ languages listed here: https://developers.google.com/recaptcha/docs/language
$lang = 'en';
?>
<!DOCTYPE HTML>
<!--
	Epilogue by TEMPLATED
	templated.co @templatedco
	Released for free under the Creative Commons Attribution 3.0 license (templated.co/license)
-->
<html>
	<head>
        <title>Scheff's Kitchens &amp; Cabinets</title>
        <meta charset="utf-8" />
        <meta name="Keywords" content="kitchens, kitchen renovations ,cabinets, joinery, vanity, built-in-robes, doors, bench tops, caesarstone, essastone, shelving, laminex" />
        <meta name="Description" content="Scheff’s Kitchens &amp; Cabinets will custom build your kitchen or any cabinet to suit your individual needs and lifestyle with very personal assistance" />		
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" href="assets/css/main.css" />
        <!-- Core CSS file -->
        <script src='https://www.google.com/recaptcha/api.js' async defer></script>
	</head>
	<body>

		<!-- Header -->
			<header id="header" class="alt gallery">
        <ul class="topnav" id="myTopnav">
  <li class="icon">
    <a href="javascript:void(0);" onclick="myFunction()">&#9776;</a>
  </li>
  <li><a href="./index.html">Home</a></li>
  <li><a href="./gallery.html">Gallery</a></li>
  <li><a href="./contact.php">Contact</a></li>
  
</ul>
        <div class="inner" id="mybanner">
          <h1>Scheff&apos;s Kitchen &amp; Cabinets</h1>
					<p>Custom built Kitchens</p>
				</div>
			</header>

		<!-- Wrapper -->
			<div id="wrapper">
                <section class="contactcard">
                  <!--<article class="item">-->
                    <div>
                            <img class="image left" src="images/Director-Grant-Scheuffele.jpg"/>
                    <address>
                    <strong>Scheff&apos;s Kitchens &amp; Cabinets</strong>
                    <br>93 Tapley's Hill Road,
                    <br>Hendon SA 5014
                <br><i class="fa fa-envelope" aria-hidden="true">&nbsp;scheffs@bigpond.com</i>
                <br><i class="fa fa-phone" aria-hidden="true">&nbsp;(08) 8445 6234</i><br>
                    </address>
                    </div>
                    <!--</article>
                  <article class="map item">-->
                    <div class="imap">
<iframe frameborder="0" style="border:0" src="https://www.google.com/maps/embed/v1/place?q=place_id:ChIJxyBsM4TGsGoRG5TfcLIUqBI&key=AIzaSyBdUxQUUlnvKl2EutYG_iQy2ExlGFNhuJ4" allowfullscreen></iframe>
</div>
                      <!--</article>-->
                      </section>
                <?php 
                if (isset($_POST['g-recaptcha-response'])):
                    $recaptcha = new \ReCaptcha\ReCaptcha($secret);
    // Make the call to verify the response and also pass the user's IP address
    $resp = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

    if ($resp->isSuccess()):
    /* Only use this if you have a HTML form posting to a PHP script, this is not a php form! */
                include 'validator.php';

                if ($isspam != "yes")
                {
                        //$to='scheffs@bigpond.com';
                        $to='ajcannane@gmail.com';
                        //$to='sudeep.viraj@bakasit.com.au';
                        while (list($key,$val) = each($_POST)){
                                if (($val !="") and ($val !="submit")) {
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
                        mail($enquiryTo,"Correspondance from your Website",$formmessage,$header);
                        header("Location: contact.php?submit=1");
                }
    ?>
    <?php else: 
    ?>
    <section id="intro" class="main">
    <p>Thankyou.
    <br>Unfortunately, we were unable to send your message.</p>
    </section>
    <?php endif;
    elseif ($_GET['submit'] == 1):
    ?>
    <section id="intro" class="main">
    <p>Thankyou for your message.
    <br>We will get back to you as soon as possible.</p>
    </section>
    <?php
    else: 
    ?>
    <h2>Enquiry</h2>
                <p>Please send us your enquiry</p>
<form action="contact.php" method="post" id="enquiryform" name="enquiryform">
    <label>Name</label>
      <input type="text" name="name" id="name" placeholder="Firstname"/>
    <label>Surname</label>
      <input type="text" name="surname" id="surname" placeholder="Surname"/>
    <label>Email</label>
      <input type="email" name="email" id="email2" placeholder="youremail@emailprovider.com"/>
    <label>Phone</label>
      <input type="text" name="phone" id="phone2" placeholder="0457114557"/></td>
    <label>Enquiry</label>
      <textarea name="enquiry" id="textarea2" cols="45" rows="5" placeholder="Enter your enquiry here."></textarea>
       <br />
     <div class="g-recaptcha" data-sitekey="6Lc5Jw0UAAAAAETBRcbNldJHFFbsxKN-ZzaN1wiG"></div>
      <br />
      <input name="submit" class="btn btn-primary" type="submit" title="Submit Form" value="Submit" />
    </form>
    <?php endif;
    ?>
        </div>
  			<script src="assets/js/hamburger.js"></script>
</body>
  </html>