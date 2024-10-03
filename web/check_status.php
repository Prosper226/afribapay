<?php
    use lab\Operator\AfribaPAY;
    require(dirname(__DIR__, 1).'/vendor/autoload.php');

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {
        $order_id = $_GET['order_id'];

        $oper = new AfribaPAY('sandbox');
        $status = $oper->status($order_id);

        header('Content-Type: application/json');
        echo json_encode($status);
        exit();
    }
?>
