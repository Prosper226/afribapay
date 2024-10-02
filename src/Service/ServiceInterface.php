<?php

namespace lab\Service;

interface ServiceInterface {
    public function listPays();
    public function infos();
    public function status(string $order_id);
    public function balance();
    public function history(string $date_start, string $date_end, int $size);
    public function payIn(array $data);
    public function payOut(array $data);

    // Usuelles pour le visuel (formulaire)
    public function paysListDeroulante(array $data);
    public function paysCurrencies(array $data, string $countryCode);
    public function paysOperateurs(array $data, string $countryCode, string $currencyCode);
    
}




?>