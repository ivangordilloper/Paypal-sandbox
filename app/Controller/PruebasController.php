<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('AppController', 'Controller');
App::uses('Paypal', 'Paypal.Lib');


/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers/pages-controller.html
 */
class PruebasController extends AppController {

   public function index($value='')
  {

    $this->Paypal = new Paypal(array(
        'sandboxMode' => true,
        'nvpUsername' => 'US_merchan_test_api1.paypal.com',
        'nvpPassword' => 'FPRWT7BQB4VLCSVC',
        'nvpSignature' => 'AFcWxV21C7fd0v3bYYYRCpSSRl31AQXPjVWQa8RhokttbLGOWz.xE4ZP'
    ));


    $order = array(
      'description' => 'Your purchase with Acme clothes store',
      'currency' => 'MXN',
      'return' => 'http://localhost/Pruebas/confirmar',
      'cancel' => 'http://localhost/Pruebas/denegado',
      'custom' => 'bingbong',
      'shipping' => '150',
      'items' => array(
          0 => array(
          'name' => 'Blue shoes',
          'description' => 'A pair of really great blue shoes',
          'tax' => 2.00,
          'subtotal' => 8.00,
          'qty' => 1,
      ),
      1 => array(
          'name' => 'Red trousers',
          'description' => 'Tight pair of red pants, look good with a hat.',
          'tax' => 1.50,
          'subtotal' => 6.00,
          'qty' => 1,
      ),
  )
);
try {
  $hola =  $this->Paypal->setExpressCheckout($order);
  $this->Session->write('Person.order', $order);
  $this->redirect($hola);
} catch (Exception $e) {
  // $e->getMessage();
}
  }
 public function confirmar($value='')
 {
   $this->Paypal = new Paypal(array(
       'sandboxMode' => true,
       'nvpUsername' => 'US_merchan_test_api1.paypal.com',
       'nvpPassword' => 'FPRWT7BQB4VLCSVC',
       'nvpSignature' => 'AFcWxV21C7fd0v3bYYYRCpSSRl31AQXPjVWQa8RhokttbLGOWz.xE4ZP'
   ));
   $this->set('order',$this->Session->read('Person.order'));
   $this->Session->write('Paypal.token', $this->request->query['token']);
   $this->Session->write('Paypal.PayerID', $this->request->query['PayerID']);
   $this->set('token',   $this->Paypal->getExpressCheckoutDetails($this->request->query['token']));
 }

  public function aceptado($token='')
  {

    $this->Paypal = new Paypal(array(
        'sandboxMode' => true,
        'nvpUsername' => 'US_merchan_test_api1.paypal.com',
        'nvpPassword' => 'FPRWT7BQB4VLCSVC',
        'nvpSignature' => 'AFcWxV21C7fd0v3bYYYRCpSSRl31AQXPjVWQa8RhokttbLGOWz.xE4ZP'
    ));

    try {
      $this->set('token' , $this->Paypal->doExpressCheckoutPayment($this->Session->read('Person.order'),$this->Session->read('Paypal.token') ,$this->Session->read('Paypal.PayerID') ));

    } catch (PaypalRedirectException $e) {
        $this->redirect($e->getMessage());
    } catch (Exception $e) {
        // $e->getMessage();
    }


  }
  public function denegado($value='')
  {
    # code...
  }

  public function pagodirecto($value='')
  {
    $this->Paypal = new Paypal(array(
        'sandboxMode' => true,
        'nvpUsername' => 'US_merchan_test_api1.paypal.com',
        'nvpPassword' => 'FPRWT7BQB4VLCSVC',
        'nvpSignature' => 'AFcWxV21C7fd0v3bYYYRCpSSRl31AQXPjVWQa8RhokttbLGOWz.xE4ZP'
    ));

    $payment = array(
    'amount' => 30.00,
    'card' => '4037077906870099', // This is a sandbox CC
    'expiry' => array(
        'M' => '10',
        'Y' => '2021',
    ),
    'cvv' => '321',
    'currency' => 'USD' // Defaults to GBP if not provided
);

try {
  $this->set('pago',  $this->Paypal->doDirectPayment($payment));
} catch (Exception $e) {
  $this->set('pago', $e->getMessage());
}
  }


  public function refundpago($value='')
  {

    $this->Paypal = new Paypal(array(
        'sandboxMode' => true,
        'nvpUsername' => 'US_merchan_test_api1.paypal.com',
        'nvpPassword' => 'FPRWT7BQB4VLCSVC',
        'nvpSignature' => 'AFcWxV21C7fd0v3bYYYRCpSSRl31AQXPjVWQa8RhokttbLGOWz.xE4ZP'
    ));


    $refund = array(
  'transactionId' => '7R942755BR664832W',  // Original PayPal Transcation ID
  'type' => 'Full',                    // , Partial, ExternalDispute, Other
    'amount' =>  167.50,                      // Amount to refund, only required if Refund Type is Partial
    'note' => 'Refund because we are nice', // Optional note to customer
    'reference' => 'abc123',                // Optional internal reference
    'currency' => 'MXN'                     // Defaults to GBP if not provided
    );

try {
  $hola= $this->Paypal->refundTransaction($refund);
  $this->set('hola',$hola);

} catch (Exception $e) {
  $this->set('hola',$e->getMessage());
}
      }
}
