<?php /*

	IMPORTANT: If you see this notice in your web browser when you
	test your feedback form, it means that your web host does not
	have PHP set up correctly, even if they tell you they have.
	This is a PHP script, which means your web server must have PHP
	installed for it to work. You should *never* be able to see this
	notice in a browser on a website with a working PHP system,
	not even when you use "View Source" in your browser. */

// ------------- CONFIGURABLE SECTION ------------------------

$mailto = 'info@oitcomputers.co.za' ;
$subject = "OITCOMPUTERS Website Contact feedback form" ;
$formurl = "http://www.oitcomputers.co.za/contact.html" ;
$thankyouurl = "http://www.oitcomputers.co.za/thankyou.html" ;
$errorurl = "http://www.oitcomputers.co.za/error.html" ;
$want_tel_field = 1;
$want_addr_field = 1;

$email_is_required = 1;
$name_is_required = 1;
$comments_is_required = 1;
$uself = 0;
$forcelf = 0;
$use_envsender = 0;
$use_sendmailfrom = 0;
$smtp_server_win = '' ;
$use_webmaster_email_for_from = 0;
$use_utf8 = 1;
$my_recaptcha_private_key = '' ;

// -------------------- END OF CONFIGURABLE SECTION ---------------

define( 'MAX_LINE_LENGTH', 998 );
$headersep = $uself ? "\n" : "\r\n" ;
$content_nl = $forcelf ? "\n" : (defined('PHP_EOL') ? PHP_EOL : "\n") ;
$content_type = $use_utf8 ? 'Content-Type: text/plain; charset="utf-8"' : 'Content-Type: text/plain; charset="iso-8859-1"' ;
if ($use_sendmailfrom) {
	ini_set( 'sendmail_from', $mailto );
}
if (strlen($smtp_server_win)) {
	ini_set( 'SMTP', $smtp_server_win );
}
$envsender = "-f$mailto" ;
$fullname = isset($_POST['fullname']) ? $_POST['fullname'] : $_POST['name'] ;
$email = $_POST['email'] ;
$comments = $_POST['comments'] ;
$http_referrer = getenv( "HTTP_REFERER" );

if (!isset($_POST['email'])) {
	header( "Location: $formurl" );
	exit ;
}
if (($email_is_required && (empty($email) || !preg_match('/@/', $email))) || ($name_is_required && empty($fullname)) || ($comments_is_required && empty($comments))) {
	header( "Location: $errorurl" );
	exit ;
}
if ( preg_match( "/[\r\n]/", $fullname ) || preg_match( "/[\r\n]/", $email ) ) {
	header( "Location: $errorurl" );
	exit ;
}
/*========Start==This was rem b4==================*/
if (strlen( $my_recaptcha_private_key )) {
	$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify?' .
							'secret=' . urlencode($my_recaptcha_private_key) . '&' .
							'remoteip=' . urlencode($_SERVER['REMOTE_ADDR']) . '&' .
							'v=' . "php_1.0" . '&' .
							'response=' . urlencode($_POST['g-recaptcha-response']) ;
	$recaptcha_reply = file_get_contents( $recaptcha_url );
	$recaptcha_decoded = json_decode ( $recaptcha_reply, TRUE );
	if ($recaptcha_decoded == NULL || (trim($recaptcha_decoded['success']) != TRUE)) {
		header( "Location: $errorurl" );
		exit ;
	}
}
/*=====Stop==This was rem b4=====================*/
if (empty($email)) {
	$email = $mailto ;
}
$fromemail = $use_webmaster_email_for_from ? $mailto : $email ;
if (function_exists( 'get_magic_quotes_gpc' ) && get_magic_quotes_gpc()) {
	$comments = stripslashes( $comments );
}
$opt_flds = $want_addr_field ? wordwrap ( "Address: " . $_POST['addr'] . $content_nl, MAX_LINE_LENGTH, $content_nl, true ) : '' ;
$opt_flds .= $want_tel_field ? wordwrap ( "Telephone: " . $_POST['tel'] . $content_nl, MAX_LINE_LENGTH, $content_nl, true ) : '' ;
$messageproper =
	"This message was sent from:" . $content_nl .
	$http_referrer . $content_nl .
	"------------------------------------------------------------" . $content_nl .
	"Name of sender: $fullname" . $content_nl .
	"Email of sender: $email" . $content_nl .
	$opt_flds .
	"------------------------- COMMENTS -------------------------" . $content_nl . $content_nl .
	wordwrap( $comments, MAX_LINE_LENGTH, $content_nl, true ) . $content_nl . $content_nl .
	"------------------------------------------------------------" . $content_nl ;

$headers =
	"From: \"$fullname\" <$fromemail>" . $headersep . "Reply-To: \"$fullname\" <$email>" . $headersep . "X-Mailer: chfeedback.php 2.18.0" .
	$headersep . 'MIME-Version: 1.0' . $headersep . $content_type ;

if ($use_envsender) {
	mail($mailto, $subject, $messageproper, $headers, $envsender );
}
else {
	mail($mailto, $subject, $messageproper, $headers );
}
header( "Location: $thankyouurl" );
exit ;

?>