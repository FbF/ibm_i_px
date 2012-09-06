<?php
/**
 * PHP IBM Intranet Password Exchange Library
 *
 * @author Neil Crookes
 * @copyright Neil Crookes
 * @package IbmIPx
 * @license http://www.opensource.org/licenses/mit-license.php The MIT License
 */

/**
 * Example IIPX receiver script to illustrate usage of the PHP IBM Intranet
 * Password Exchange Library.
 *
 * When you run this script you should see the decoded XML message string
 * containing the user details.
 */

// A sample message as would be in the $_GET['msg'] querystring param of the
// request to your session receiver script
$base64Message = 'PFNpZ25vblJlcXVlc3QgdmVuZG9yPSJiYnBfdGVzdCI+PE5hbWU+PExhc3ROYW1lPlJvbHBoPC9M YXN0TmFtZT48Rmlyc3ROYW1lPkguIEouPC9GaXJzdE5hbWU+PC9OYW1lPjxFbXBsb3llZUlEPjxD b3VudHJ5Q29kZT5nYjwvQ291bnRyeUNvZGU+PFNlcmlhbE51bWJlcj4wMDU1MDA8L1NlcmlhbE51 bWJlcj48Q251bT4wMDU1MDA4NjY8L0NudW0+PC9FbXBsb3llZUlEPjxFbWFpbEFkZHJlc3M+aG93 YXJkcm9scGhAdWsuaWJtLmNvbTwvRW1haWxBZGRyZXNzPjxFeHRlcm5hbFBob25lPjQ0LTEyNTYt MzQ0MTkwPC9FeHRlcm5hbFBob25lPjxUaW1lU3RhbXA+MjAxMS4wMy4yNSAxNDowOTozNyBHTVQ8 L1RpbWVTdGFtcD48RXhwaXJhdGlvbj4yMDExLjA0LjI0IDE0OjA5OjM3IEdNVDwvRXhwaXJhdGlv bj48L1NpZ25vblJlcXVlc3Q+';

// A sample signature as would be in the $_GET['sig'] querystring param of the
// request to your session receiver script
$base64Signature = 'UXGN3I9K4Ovxn0C4FkivNrAqmPmIsZLDu/1DcKk0ZKTSDusSz0UFLGVTU7Xg6vRYlwwAyEGV3060 oeH0QscXBuylmrkAAW0xGBLgYGj/XnPRyPf3K5yCMCJ/ag9NR83fenCDO9qSPZh8wGwHKmxqrcka S/qjNbXALY4ykgm//Ds=';

// Your vendor code provided by IBM
$vendorCode = 'bbp_test';

//Create the SoapClient object to pass in
$SoapClient = new SoapClient('https://www.ibm.com/ibm/password/vendor/validate/services/IBMDSig?wsdl');

// That's the end of the section that initialises all the variables we need, now
// we get to the meaty stuff...

require_once 'ibm_i_px.php';

// Pear's Log class
require_once 'Log.php';

// Create a concrete log handler object from Pear Log. This example uses the
// file log handler and writes to a file out.log in the current directory.
$Logger = Log::factory('file', 'out.log');

// Set the Logger object as a static property in the custom Exception class the
// library uses so it's available whenever a new IbmIPxException is thrown.
IbmIPxException::setLogger($Logger);

// Finally, we have everything ready to create a new IbmIPx object and call
// the IbmIPx::getMessage() method.
$IbmIPx = new IbmIPx($vendorCode, $SoapClient);

// Wrap the IbmIPx::getMessage() call in a try/catch block so you can handle
// any Exceptions thrown however you want.
try {
  $xmlString = $IbmIPx->getMessage($base64Message, $base64Signature);
  echo '<h1>Success</h1>'.PHP_EOL;
  echo '<p>It worked, here\'s the message:</p>'.PHP_EOL;
  echo $xmlString.PHP_EOL;
} catch (IbmIPxException $e) {
  echo '<h1>Your attempted hack was an epic fail</h1>'.PHP_EOL;
  echo '<p>'.$e->getMessage().'</p>'.PHP_EOL;
} catch (Exception $e) {
  echo '<h1>Something went wrong, google the message:</h1>'.PHP_EOL;
  echo '<p>'.$e->getMessage().'</p>'.PHP_EOL;
}

// Check the exceptions log file to make sure they are working