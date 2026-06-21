<?php
// require ReCaptcha class
require('recaptcha-master/src/autoload.php');

// configure
$enquiryEmail = getenv('ENQUIRY_EMAIL') ?: 'grant@scheffskitchens.com.au';
$from = 'Website contact form <' . $enquiryEmail . '>';
$sendTo = 'ScheffsKitchens <' . $enquiryEmail . '>';
$subject = 'Correspondance from your Website';
$fields = array('name' => 'Name', 'surname' => 'Surname', 'phone' => 'Phone', 'email' => 'Email', 'message' => 'Message'); // array variable name => Text to appear in the email
$okMessage = 'Contact form successfully submitted. Thank you, I will get back to you soon!';
$errorMessage = 'There was an error while submitting the form. Please try again later';

// let's do the sending

try
{
    if (!empty($_POST)) {

        // CSRF: verify request originates from this site
        $allowedOrigin = (isset($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
        $requestOrigin = isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : '';
        $requestReferer = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        if ($requestOrigin && $requestOrigin !== $allowedOrigin) {
            throw new \Exception('Invalid request origin.');
        }
        if (!$requestOrigin && $requestReferer && strpos($requestReferer, $allowedOrigin) !== 0) {
            throw new \Exception('Invalid request referer.');
        }

        // validate the ReCaptcha, if something is wrong, we throw an Exception,
        // i.e. code stops executing and goes to catch() block

        // ReCaptcha validation — bypass allowed in development via RECAPTCHA_BYPASS=true
        $recaptchaBypass = filter_var(getenv('RECAPTCHA_BYPASS'), FILTER_VALIDATE_BOOLEAN);
        if (!$recaptchaBypass) {
            if (!isset($_POST['g-recaptcha-response'])) {
                throw new \Exception('ReCaptcha is not set.');
            }

            // ReCaptcha secret is read from the RECAPTCHA_SECRET_KEY environment variable.
            // Set this in docker-compose.yml or server environment — never hardcode it.
            $recaptchaSecret = getenv('RECAPTCHA_SECRET_KEY');
            if (!$recaptchaSecret) {
                throw new \Exception('Server configuration error.');
            }

            $recaptcha = new \ReCaptcha\ReCaptcha($recaptchaSecret, new \ReCaptcha\RequestMethod\CurlPost());

            // we validate the ReCaptcha field together with the user's IP address
            $response = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

            if (!$response->isSuccess()) {
                throw new \Exception('ReCaptcha was not validated.');
            }
        }

        // everything went well, we can compose the message, as usually

        $emailText = "You have new message from contact form\n=============================\n";

        foreach ($_POST as $key => $value) {

            if (isset($fields[$key])) {
                // Strip newlines from values to prevent email header injection
                $safeValue = str_replace(["\r", "\n"], ' ', $value);
                $emailText .= "$fields[$key]: $safeValue\n";
            }
        }

        // Sanitize X-Forwarded-For: validate as IP before trusting
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $forwardedIp = preg_replace('/[\r\n]/', '', $_SERVER['HTTP_X_FORWARDED_FOR']);
            // Use only the first IP in the chain and validate it
            $firstIp = trim(explode(',', $forwardedIp)[0]);
            $originIp = filter_var($firstIp, FILTER_VALIDATE_IP)
                ? "$firstIp via {$_SERVER['REMOTE_ADDR']}"
                : $_SERVER['REMOTE_ADDR'];
        } else {
            $originIp = $_SERVER['REMOTE_ADDR'];
        }

        $headers = array(
            'Content-Type: text/plain; charset="UTF-8";',
            'From: ' . $from,
            'Reply-To: ' . $from,
            'Return-Path: ' . $from,
            'X-Originating-IP: ' . $originIp
        );

        mail($sendTo, $subject, $emailText, implode("\r\n", $headers));

        $responseArray = array('type' => 'success', 'message' => $okMessage);
    }
}
catch (\Exception $e)
{
    $responseArray = array('type' => 'danger', 'message' => $errorMessage);
}

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $encoded = json_encode($responseArray);

    header('Content-Type: application/json');

    echo $encoded;
}
else {
    echo $responseArray['message'];
}
