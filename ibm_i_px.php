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
 * Encapsulates the IIPX reciever client logic.
 */
class IbmIPx {

  /**
   * Stores the vendor code passed in to the constructor
   *
   * string
   */
  protected $_myVendorCode;

  /**
   * Stores the SoapClient object passed in to the constructor
   *
   * SoapClient
   */
  protected $_soapClient;

  /**
   * Stores the params passed in the object properties
   *
   * @param string $vendorCode The vendor code assigned to you by IBM
   * @param SoapClient $soapClient
   */
  public function  __construct($vendorCode, SoapClient $soapClient) {
    $this->_myVendorCode = $vendorCode;
    $this->_soapClient = $soapClient;
  }

  /**
   * The public method of the class used to verify and decode the message
   *
   * @param string $base64Message
   * @param string $base64Ssignature
   * return string or throws IbmIPxException
   */
  public function getMessage($base64Message, $base64Ssignature) {
    $xmlMessage = $this->_verify($base64Message, $base64Ssignature);
    $this->_validateVendorCode($xmlMessage);
    return $xmlMessage;
  }

  /**
   * Calls verifyWithException on the IIPX webservice with the given
   * base64 message and base64 signature and returns the repsonse, which will be
   * the decoded XML on success or throws an exception (an IbmIPxException to be
   * precise) which has the same message as the SoapFault exception thrown by
   * the Soap Service
   *
   * @param string $base64Message
   * @param string $base64Signature
   * @return string
   */
  protected function _verify($base64Message, $base64Signature) {

    try {
      $xmlMessage = $this->_soapClient->verifyWithException($base64Message, $base64Signature);
      return $xmlMessage;
    } catch (SoapFault $e) {
      throw new IbmIPxException($e->getMessage());
    }

  }

  /**
   * Check the vendor code attribute of the root (SignonRequest) node of the XML
   * string is the vendor code provided by IBM, else throws an IbmIPxException
   * 
   * @param string $xmlString
   * @return boolean true or throws IbmIPxException
   */
  protected function _validateVendorCode($xmlString) {

    $xml = simplexml_load_string($xmlString);

    if ($xml['vendor'] != $this->_myVendorCode) {
      throw new IbmIPxException('Invalid vendor code');
    }

    return true;

  }
  
}

/**
 * Custom Exception class that logs all exceptions.
 *
 * You must call IbmIPxException::setLogger() and pass it a Pear Log object
 * before a new IbmIPxException is thrown.
 */
class IbmIPxException extends Exception {

  /**
   * Instance of the Pear Log class
   *
   * @var Log object
   */
  protected static $_Logger;

  /**
   * Called when a new IbmIPxException gets thrown and logs the message and
   * code using the Pear Log object in the static $_Logger property, which you
   * can set using setLogger() static method.
   *
	 * @param string $message The message to log
	 * @param integer $code The severity of the issue. Pear Log severity levels
   *        are used, e.g. PEAR_LOG_EMERG, which are defined at the top of the
   *        Pear Log class definition file.
	 * @param Exception $previous The previous exception used for the exception
   *        chaining.
   */
  public function __construct($message = null, $code = null) {
    parent::__construct($message, $code);
    // Log the exception message
    self::$_Logger->log($message, $code);
  }

  /**
   * Static method which stores a Pear Log object in the protected static
   * property $_Logger, on which the log() method is called in the constructor
   * when a new IbmIPxException is thrown.
   *
   * @param Log $Logger Instance of Pear Log class
   */
  public static function setLogger(Log $Logger) {
    self::$_Logger = $Logger;
  }

}
?>
