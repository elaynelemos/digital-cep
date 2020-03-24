<?php

namespace Lay\DigitalCep;

class Search {
  private $url = [];

  private function getContent(string $url, string $zipCode, 
  string $complement): string {
    $opts = [
      "http" => [
          "method" => "GET",
          "header" => "Accept: application/json\r\n",
          'ignore_errors' => true
      ]
    ];
    $context = stream_context_create($opts);
    $zipCode = preg_replace('/[^0-9]/im', '', $zipCode);
    $get = @file_get_contents($url . $zipCode . $complement, false, $context);
    
    return $get;
  }
  private function replaceKeys(string $source, array $oldKeys, 
  array $newKeys): ?string {
    $len = count($oldKeys);
    if ($len === count($newKeys)) {
      for ($i = 0; $i < $len; $i++) {
        $source = str_replace($oldKeys[$i], $newKeys[$i], $source);
      }
      return $source;
    } else {
      return null;
    }
  }
  
  private function eliminateKeys(array $source, array $keys): array {
    $i = null;
    $keysLength = count($keys);
    for ($i = 0; $i < $keysLength; $i++) {
      unset($source[$keys[$i]]);
    }
    return $source;
  } 
  
  private function joinKeys(array $source, array $destKeys, 
  array $oldKeys): array {
    $keysLength = count($destKeys);
    $i = null;
    for ($i = 0; $i < $keysLength; $i++) {
      if(@$source[$destKeys[$i]] != null && @$source[$oldKeys[$i]] != null) {
        $source[$destKeys[$i]] = $source[$destKeys[$i]] . ' - ' .
        $source[$oldKeys[$i]];
        unset($source[$oldKeys[$i]]);
      }
    }
    @$source["cep"] = str_replace( "-", "", $source["cep"]);
    return $source;
  }
  
  public function getAddressFromZipcode(string $zipCode): array {
    $this->url[] = "https://viacep.com.br/ws/";
    $this->url[] = "http://cep.la/";
    $this->url[] = "https://apps.widenet.com.br/busca-cep/api/cep/";
    $url = $this->url;
    $get = NULL;
    if(strlen(preg_replace("/[^0-9]/", "", $zipCode)) !== 8 || 
    (int)$zipCode === 0) {
      return array();
    }
    foreach ($url as $str) {
      try {
        switch ($str) {
          case $url[0]:
            $get = $this->getContent($str, $zipCode, "/json");
            $get = $this->replaceKeys($get, array("localidade"), 
              array("cidade"));
            break;
          case $url[1]:
            $get = $this->getContent($str, $zipCode, "");
            break;
          case $url[2]:
            $get = $this->getContent($str, $zipCode, ".json");
            $get = $this->replaceKeys($get, 
              array("code", "state", "city", "district", "address"), 
              array("cep", "uf", "cidade", "bairro", "logradouro"));
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
    $get = $this->joinKeys((array)$get, array("logradouro"), 
      array("complemento"));
    $get = $this->eliminateKeys($get, array("unidade", "ibge", "status", 
      "gia", "ok", "statusText", "complemento"));
    return $get;
  }
}