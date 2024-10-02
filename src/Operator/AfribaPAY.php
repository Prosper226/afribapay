<?php

namespace lab\Operator;

use lab\Service\ServiceImpl;

class AfribaPAY extends ServiceImpl {

    const CREDENTIALS = [
        "sandbox" => [
            "ApiUser" => "pk_de85c2c0-6232-47d2-898c-1fa5e448d8ca",
            "ApiSecret" => "sk_hHz25TRdys1m7eLDnw2Dj9Ly0mvW43Yu3lI08S48m65",
            "baseUrl" => "https://api-sandbox.afribapay.com",
            "merchantKey" => "ya6t4M09WIvadli8z0faXr3UL",
            "return_url" => "https://mywebsite.com/callbacks",
            "cancel_url" => "https://mywebsite.com/callbacks",
            "notif_url" => "https://mywebsite.com/url_to_send_notification"
        ],
        "production" => [
            "ApiUser" => "",
            "ApiSecret" => "",
            "baseUrl" => "",
            "merchantKey" => "",
            "return_url" => "",
            "cancel_url" => "",
            "notif_url" => ""
        ],
    ];

    public function __construct($platform = "production") {
        parent::__construct(self::CREDENTIALS[$platform]);
    }

}









?>