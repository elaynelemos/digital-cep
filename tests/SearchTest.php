<?php

use PHPUnit\Framework\TestCase;
use Lay\DigitalCep\Search;

class SearchTest extends TestCase {
  /**
   * @dataProvider dataTest
   */
  public function testGetAddressFromZipcodeDefaultUsage(string $input, array $expected) {
    $result = new Search;
    $result = $result->getAddressFromZipCode($input);
    $expected = 
    $this->assertEquals($expected, $result);
  }

  public function dataTest() {
    return [
      "Praça da Sé Address" => [
        "01001000", 
        [
          "cep" => "01001000",
          "logradouro" => "Praça da Sé - lado ímpar",
          "bairro" => "Sé",
          "cidade" => "São Paulo",
          "uf" => "SP"
        ]
      ],
      "Somewhere Address" => [
        "03624010", 
        [
          "cep" => "03624010",
          "logradouro" => "Rua Luís Asson",
          "bairro" => "Vila Buenos Aires",
          "cidade" => "São Paulo",
          "uf" => "SP"
        ]
      ],
    ];
  }
}