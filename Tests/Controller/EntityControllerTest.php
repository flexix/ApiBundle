<?php

namespace Flexix\ApiBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EntityControllerTest extends WebTestCase {

    protected static $username = 'donatello@tmnt.com';
    protected static $password = 'cowabunga';
    protected static $client = null;

    // protected static $calendarId;



    protected function setUp() {
        self::$client = static::createClient();
//        $route='/login';
//        $crawler = self::$client->request('GET', $route,[], [], [ 'HTTP_X-Requested-With' => 'XMLHttpRequest',
//    'CONTENT_TYPE' => 'application/json',]);
//        $form = $crawler->selectButton('_submit')->form(array(
//            '_username'  => self::$username,
//            '_password'  => self::$password
//        ));
//
//        self::$client->submit($form);
    }

//    public function testInsertAction() {
//       
//        $data = [
//            "name"=>"ala ala ala ala  ma kota"];
//        $crawler = self::$client->request('POST', 'api/v1/warranty-type/new', [],[] ,['CONTENT_TYPE' => 'application/json'], json_encode($data));
//        var_dump(self::$client->getRequest()->getContent());
//        var_dump(self::$client->getResponse()->getContent());
//       
//        $this->assertEquals(200, self::$client->getResponse()->getStatusCode());
//    }



    public function testInsertAction() {

        $data = [
            "name" => "ala mala ala ala ala  ma kota"];
        // $crawler = self::$client->request('POST', 'api/v1/warranty-type/new', [],[] ,['CONTENT_TYPE' => 'application/json'], json_encode($data));
        //  var_dump(self::$client->getRequest()->getContent());
        // var_dump(self::$client->getResponse()->getContent());

        $code = $this->sendRequest('http://localhost/proinserv/web/app_dev.php/api/v1/warranty-type/new', $data, 'POST');

        $this->assertEquals(200, $code);
    }

    protected function sendRequest($uri, $data, $type) {
        $curlHandler = \curl_init();
        \curl_setopt($curlHandler, CURLOPT_URL, $uri);
        $data_string = \json_encode($data);
        \curl_setopt($curlHandler, CURLOPT_POSTFIELDS, $data_string);
        \curl_setopt($curlHandler, CURLOPT_CUSTOMREQUEST, $type);
        \curl_setopt($curlHandler, CURLOPT_RETURNTRANSFER, true);
        \curl_setopt($curlHandler, CURLOPT_SSL_VERIFYPEER, false);
        \curl_setopt($curlHandler, CURLINFO_HEADER_OUT, true);
        \curl_setopt($curlHandler, CURLOPT_FOLLOWLOCATION, TRUE);
        \curl_setopt($curlHandler, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string)) );
        $info = \curl_getinfo($curlHandler);
        var_dump($info);
        $json = \json_decode(\curl_exec($curlHandler));
        var_dump($json);
        $httpCode = curl_getinfo($curlHandler, CURLINFO_HTTP_CODE);
        var_dump($httpCode);
        \curl_close($curlHandler);

        return $httpCode;
    }

//    
//    public function testEditAction() {
//       
//        $data = [
//            "name"=>"ala nie ma kota"];
//        $crawler = self::$client->request('POST', 'api/v1/warranty-type/edit/3', [], [], [ 'HTTP_X-Requested-With' => 'XMLHttpRequest',
//    'CONTENT_TYPE' => 'application/json',], json_encode($data));
//        var_dump(self::$client->getRequest()->getContent());
//        var_dump(self::$client->getResponse()->getContent());
//    
//        $this->assertEquals(200, self::$client->getResponse()->getStatusCode());
//    }
//    
//    
    public function testGetAction() {

       
        $code = $this->sendRequest('http://localhost/proinserv/web/app_dev.php/api/v1/warranty-type/get/1',[], 'GET');
       $this->assertEquals(200, $code);
    }

//    
//    
//    
//    
}
