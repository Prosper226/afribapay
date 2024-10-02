<?php

use lab\Operator\AfribaPAY;
require(dirname(__DIR__, 1).'/vendor/autoload.php');


$oper = new AfribaPAY('sandbox');


// print_r($oper->listPays());
// print_r($oper->infos());
// print_r($oper->status('order-1727898854'));
// print_r($oper->balance());
// print_r($oper->history('2008-12-10', '2024-12-10'));

$payInData = [
    'operator' => "orange", 
    'country' => "CI", 
    'phone'=> "2252100000001", 
    'amount'=> 500, 
    'currency'=> "XOF", 
    // 'order_id' => null, 
    // 'reference_id' => null, 
    'otp_code' => 123456
];
// print_r($oper->payIn($payInData));

$payOutData = [
    'operator' => "orange", 
    'country' => "CM", 
    'phone'=> "24365600000", 
    'amount'=> 500, 
    'currency'=> "XAF", 
    // 'order_id' => null, 
    // 'reference_id' => null, 
];
// print_r($oper->payOut($payOutData));

// listPays = $oper->listPays();
// print_r($oper->paysCurrencies($listPays));
// print_r($oper->paysCurrencies($listPays, "CD"));
// print_r($oper->paysOperateurs($listPays, "BF", "XOF"));




?>