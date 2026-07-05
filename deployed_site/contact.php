<?php
// require ReCaptcha class
require('recaptcha-master/src/autoload.php');

// configure — check $_SERVER first (FastCGI: SetEnv lands there, not in getenv())
function server_env(string $key): string {
    return $_SERVER[$key] ?? getenv($key) ?: '';
}

$enquiryEmail = server_env('ENQUIRY_EMAIL') ?: 'grant@scheffskitchens.com.au';
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

        if (!isset($_POST['g-recaptcha-response'])) {
            throw new \Exception('ReCaptcha is not set.');
        }

        // ReCaptcha validation — bypass allowed in development via RECAPTCHA_BYPASS=true
        $recaptchaBypass = filter_var(server_env('RECAPTCHA_BYPASS'), FILTER_VALIDATE_BOOLEAN);
        if (!$recaptchaBypass) {
            // ReCaptcha secret is read from the RECAPTCHA_SECRET_KEY environment variable.
            // Set this in docker-compose.yml or server environment — never hardcode it.
            $recaptchaSecret = server_env('RECAPTCHA_SECRET_KEY');
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

        $visitorEmail = isset($_POST['email']) ? filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL) : false;
        $replyTo = $visitorEmail ? $visitorEmail : $enquiryEmail;

        $headers = array(
            'Content-Type: text/plain; charset="UTF-8"',
            'From: ' . $from,
            'Reply-To: ' . $replyTo,
        );

        $sent = mail($sendTo, $subject, $emailText, implode("\r\n", $headers));

        if (!$sent) {
            throw new \Exception('mail() failed to send.');
        }

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
