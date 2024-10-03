<?php
    use lab\Operator\AfribaPAY;
    require(dirname(__DIR__, 1).'/vendor/autoload.php');

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $oper = new AfribaPAY('sandbox');
        $payInData = [
            'operator' => $_POST['operator'], 
            'country'  => $_POST['country'], 
            'phone'    => $_POST['phone'], 
            'amount'   => $_POST['amount'], 
            'currency' => $_POST['currency']
        ];
        $payIn = $oper->payIn($payInData);
        header('Content-Type: application/json');
        echo json_encode($payIn);
        exit();
    }

?>
