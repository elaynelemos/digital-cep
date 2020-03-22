<?php

namespace Lay\DigitalCep;

class Search {
  private $url = [];

  public function getAddressFromZipcode(string $zipCode): array {
    $this->url[] = "https://viacep.com.br/ws/";
    $this->url[] = "http://cep.la/";
    $this->url[] = "https://apps.widenet.com.br/busca-cep/api/cep/";
    $url = $this->url;
    $opts = [
      "http" => [
          "method" => "GET",
          "header" => "Accept: application/json\r\n",
          'ignore_errors' => true
      ]
    ];
    $context = stream_context_create($opts);
    $zipCode = preg_replace('/[^0-9]/im', '', $zipCode);
    $get = NULL;
    foreach ($url as $str) {
      try {
        switch ($str) {
          case $url[0]:
            $get = @file_get_contents($str . $zipCode . "/json", false, $context);
            break;
          case $url[1]:
            $get = @file_get_contents($str . $zipCode, false, $context);
            break;
          case $url[2]:
            $get = @file_get_contents($str . $zipCode . ".json", false, $context);
            $target = array("code", "state", "city", "district", "address");
            $src = array("cep", "uf", "cidade", "bairro", "logradouro");
            $len = count($target);
            for ($i = 0; $i < $len; $i++) {
              $get = str_replace($target[$i], $src[$i], $get);
            }
            break;
        }
      }
      catch(Exception $e) {
        echo 'Error: '. $str . 'not available!' . PHP_EOL;
      }
      finally {
        $get = json_decode($get);
      }
      if(!empty($get)) break;
    }
    return (array) $get;
  }
}