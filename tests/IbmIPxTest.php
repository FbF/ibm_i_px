<?php

// Load subject under test
require_once dirname(__FILE__) . '/../ibm_i_px.php';

// Load PEAR Log
require_once 'Log.php';

/**
 * Test case for the IbmIPx class
 */
class IbmIPxTest extends PHPUnit_Framework_TestCase {

  public function setUp() {

    parent::setUp();

    $LoggerStub = $this->getMock('Log', array(), array(), '', false);

    IbmIPxException::setLogger($LoggerStub);

  }

  public function testInvalidVendorCode() {

    $messageWithInvalidVendorCode = '<SignonRequest vendor="invalid_vendor_code"></SignonRequest>';

    $SoapClient = $this->getMock('SoapClient', array(), array(), '', false);
    $IbmIPx = $this->getMock('IbmIPx', array('_verify'), array('sample_vendor_code', $SoapClient));

    $IbmIPx->expects($this->any())
           ->method('_verify')
           ->will($this->returnValue($messageWithInvalidVendorCode));

    try {
      $IbmIPx->getMessage('dummy message', 'dummy signature');
    } catch (IbmIPxException $expected) {
      return;
    }

    $this->fail('IbmIPxException was not thrown');

  }

  public function testValidVendorCode() {

    $SoapClient = $this->getMock('SoapClient', array(), array(), '', false);
    $IbmIPx = $this->getMock('IbmIPx', array('_verify'), array('sample_vendor_code', $SoapClient));

    $expected = '<SignonRequest vendor="sample_vendor_code"></SignonRequest>';
    $IbmIPx->expects($this->any())
           ->method('_verify')
           ->will($this->returnValue($expected));

    $actual = $IbmIPx->getMessage('dummy message', 'dummy signature');
    $this->assertEquals($expected, $actual);

  }

  public function testExceptionTransfer() {

    $SoapClient = $this->getMockFromWsdl('IBMDSig.wsdl');

    $expected = 'Sample message';
    $SoapClient->expects($this->any())
               ->method('verifyWithException')
               ->will($this->throwException(new SoapFault('1', $expected)));

    $IbmIPx = new IbmIPx('sample_vendor_code', $SoapClient);

    try {
      $IbmIPx->getMessage('dummy message', 'dummy signature');
      $this->fail('IbmIPxException not thrown');
    } catch (IbmIPxException $e) {
      $this->assertEquals($expected, $e->getMessage());
      return;
    }

    $this->fail('IbmIPxException not thrown');

  }

}