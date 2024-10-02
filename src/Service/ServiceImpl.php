<?php

namespace lab\Service;

use lab\Logger\LoggerManager;
use GuzzleHttp\Client;
use Exception;
use InvalidArgumentException;

class ServiceImpl implements ServiceInterface {

    private $logger;
    private $errorLogger;
    protected $credentials = [];
    private $baseUrl;
    private $target;
    private $currency;
    
    public function __construct($credentials = []) {
        $this->baseUrl  = $credentials['baseUrl'];
        $this->credentials = $credentials;
        $this->logger = LoggerManager::getLogger('app');
        $this->errorLogger = LoggerManager::getErrorLogger();
    }

    private function requestHandler($method = 'GET', $body = [], $endpoint = "", $headers = [], $decode = true){
        try{
            $client = new Client(['base_uri' => $this->baseUrl, 'headers' => $headers]);
            $body = ($body) ? ["json" => $body] : [];
            $response = $client->request($method, $endpoint, $body);
            $contents = $response->getBody()->getContents();
            $decodedContents = ($decode) ? json_decode($contents, true) : $contents;
            $statusCode = $response->getStatusCode();
            return (Object)['statusCode' => $statusCode, 'content' => $decodedContents];
        }catch (\GuzzleHttp\Exception\RequestException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $statusCode = $response->getStatusCode();
                $contents = $response->getBody()->getContents();
                if ($statusCode >= 400 && $statusCode < 500) {
                    return (Object)['statusCode' => $statusCode, 'content' => $contents];
                } elseif ($statusCode >= 500 && $statusCode < 600) {
                    throw new Exception("Server Error: $contents");
                } else {
                    throw new Exception("Unexpected HTTP status code: $statusCode");
                }
            } else {
                throw new Exception("Request failed: " . $e->getMessage());
            }
            throw new Exception("Error in request: " . $e->getMessage());
        }catch(Exception $e){
            throw new Exception($e);
        }
    }

    private function getBasicAuth(){
        $username = $this->credentials["ApiUser"];
        $password = $this->credentials["ApiSecret"];
        return 'Basic '.base64_encode("$username:$password");
    }

    private function getBearerToken(){
        try{
            $headers = [
                "Authorization" => $this->getBasicAuth(),
            ];
            $endpoint = "/v1/token";
            $response = self::requestHandler('POST', null, $endpoint, $headers);
            $statusCode = $response->statusCode;
            if($statusCode != 200){
                throw new Exception($statusCode.':'.$response->content);
            }
            $content = $response->content;
            return $content['data']['access_token'];
        }catch(Exception $e){
            $this->errorLogger->error('Error in getBearerTokeb : ' . $e->getMessage());
            return null;
        }
    }

    public function listPays(){
        try {
            $this->logger->info('listPays method called');
            $headers = [
                "Authorization" => "Bearer ". self::getBearerToken()
            ];
            $endpoint = '/v1/countries';
            $response = self::requestHandler('GET', null, $endpoint, $headers);
            $statusCode = $response->statusCode;
            if($statusCode != 200){
                throw new Exception($statusCode.':'.$response->content);
            }
            return (Array)$response;
        } catch (Exception $e) {
            $this->errorLogger->error('Error in listPays method: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    public function infos(){
        try {
            $this->logger->info('infos method called');
            $headers = [
                "Authorization" => "Bearer ". self::getBearerToken()
            ];
            $endpoint = '/v1/infos';
            $response = self::requestHandler('GET', null, $endpoint, $headers);
            $statusCode = $response->statusCode;
            if($statusCode != 200){
                throw new Exception($statusCode.':'.$response->content);
            }
            return (Array)$response;
        } catch (Exception $e) {
            $this->errorLogger->error('Error in infos method: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    public function status(string $order_id) {
        try {
            $this->logger->info('status method called');
            $headers = [
                "Authorization" => "Bearer ". self::getBearerToken()
            ];
            $endpoint = '/v1/status?order_id='.$order_id;
            $response = self::requestHandler('GET', null, $endpoint, $headers);
            $statusCode = $response->statusCode;
            if($statusCode != 200){
                throw new Exception($statusCode.':'.$response->content);
            }
            return (Array)$response;
        } catch (Exception $e) {
            $this->errorLogger->error('Error in status method: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    public function balance(){
        try {
            $this->logger->info('balance method called');
            $headers = [
                "Authorization" => "Bearer ". self::getBearerToken()
            ];
            $endpoint = '/v1/balance';
            $response = self::requestHandler('GET', null, $endpoint, $headers);
            $statusCode = $response->statusCode;
            if($statusCode != 200){
                throw new Exception($statusCode.':'.$response->content);
            }
            return (Array)$response;
        } catch (Exception $e) {
            $this->errorLogger->error('Error in balance method: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    public function history(string $date_start, string $date_end, int $size = 100){
        try {
            $this->logger->info('history method called');
            $headers = [
                "Authorization" => "Bearer ". self::getBearerToken()
            ];
            $endpoint = '/v1/history?size='.$size.'&date_start='.$date_start.'&date_end='.$date_end;
            $response = self::requestHandler('GET', null, $endpoint, $headers);
            $statusCode = $response->statusCode;
            if($statusCode != 200){
                throw new Exception($statusCode.':'.$response->content);
            }
            return (Array)$response;
        } catch (Exception $e) {
            $this->errorLogger->error('Error in history method: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    public function payIn(array $data = ['operator' => null, 'country' => null, 'phone'=> null, 'amount'=> null, 'currency'=> null, 'order_id' => null, 'reference_id' => null, 'otp_code' => null]) {
        try {  
            $requiredKeys = ['operator', 'country', 'phone', 'amount', 'currency'];
            $missingKeys = array_diff($requiredKeys, array_keys($data));
            if (!empty($missingKeys)) {
                $missingKeysStr = implode(', ', $missingKeys);
                throw new InvalidArgumentException("Missing required key(s): $missingKeysStr in \$data array.");
            }
            $this->logger->info('payIn method called');
            $headers = [
                "Authorization" => "Bearer ". self::getBearerToken(),
            ];
            $timestamp = time();
            $body = [
                "operator" => $data['operator'],
                "country" => $data['country'],
                "phone_number" => $data['phone'],
                "amount" => $data['amount'],
                "currency" => $data['currency'],
                "order_id" => $data['order_id'] ?? 'order-'.$timestamp,
                "merchant_key" => $this->credentials['merchantKey'],
                "reference_id" => $data['reference_id'] ?? 'ref-'.$timestamp,
                "lang" => "fr",
                "return_url" => $this->credentials['return_url'],
                "cancel_url" => $this->credentials['cancel_url'],
                "notif_url" => $this->credentials['notif_url'],
            ];
            if(isset($data['otp_code']) && $data['otp_code']){
                $body["otp_code"] = $data['otp_code'];
            }
            $endpoint = '/v1/pay/payin';
            $response = self::requestHandler('POST', $body, $endpoint, $headers);
            $statusCode = $response->statusCode;
            if($statusCode != 200){
                throw new Exception($statusCode.':'.$response->content);
            }
            return (Array)$response;
        } catch (Exception $e) {
            $this->errorLogger->error('Error in payIn method: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    public function payOut(array $data = ['operator' => null, 'country' => null, 'phone'=> null, 'amount'=> null, 'currency'=> null, 'order_id' => null, 'reference_id' => null]) {
        try {  
            $requiredKeys = ['operator', 'country', 'phone', 'amount', 'currency'];
            $missingKeys = array_diff($requiredKeys, array_keys($data));
            if (!empty($missingKeys)) {
                $missingKeysStr = implode(', ', $missingKeys);
                throw new InvalidArgumentException("Missing required key(s): $missingKeysStr in \$data array.");
            }
            $this->logger->info('payOut method called');
            $headers = [
                "Authorization" => "Bearer ". self::getBearerToken()
            ];
            $timestamp = time();
            $body = [
                "operator" => $data['operator'],
                "country" => $data['country'],
                "phone_number" => $data['phone'],
                "amount" => $data['amount'],
                "currency" => $data['currency'],
                "order_id" => $data['order_id'] ?? 'order-'.$timestamp,
                "merchant_key" => $this->credentials['merchantKey'],
                "reference_id" => $data['reference_id'] ?? 'ref-'.$timestamp,
                "lang" => "fr",
                "return_url" => $this->credentials['return_url'],
                "cancel_url" => $this->credentials['cancel_url'],
                "notif_url" => $this->credentials['notif_url'],
            ];
            $endpoint = '/v1/pay/payout';
            $response = self::requestHandler('POST', $body, $endpoint, $headers);
            $statusCode = $response->statusCode;
            if($statusCode != 200){
                throw new Exception($statusCode.':'.$response->content);
            }
            return (Array)$response;

        } catch (Exception $e) {
            $this->errorLogger->error('Error in payOut method: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }



    public function paysListDeroulante(array $data) {
        try {
            $this->logger->info('paysListDeroulante method called');
            $countries = [];
            foreach ($data['content']['data'] as $country) {
                $countries[] = [
                    'country_code' => $country['country_code'],
                    'country_name' => $country['country_name']
                ];
            }
            return (Array)$countries;
        } catch (Exception $e) {
            $this->errorLogger->error('Error in paysListDeroulante method: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    public function paysCurrencies(array $data, string $countryCode) {
        try {
            $this->logger->info('paysCurrencies method called');
            if (isset($data['content']['data'][$countryCode])) {
                $currencies = array_keys($data['content']['data'][$countryCode]['currencies']);
                return $currencies;
            } else {
                throw new Exception("Le pays spécifié n'existe pas dans les données.");
            }
        } catch (Exception $e) {
            $this->errorLogger->error('Error in paysCurrencies method: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }

    public function paysOperateurs(array $data, string $countryCode, string $currencyCode) {
        try {
            $this->logger->info('paysOperateurs method called');
            if (isset($data['content']['data'][$countryCode]['currencies'][$currencyCode])) {
                $operators = $data['content']['data'][$countryCode]['currencies'][$currencyCode]['operators'];
                return $operators;
            } else {
                throw new Exception("Le pays ou la monnaie spécifié n'existe pas dans les données.");
            }
        } catch (Exception $e) {
            $this->errorLogger->error('Error in paysOperateurs method: ' . $e->getMessage());
            return ['error' => $e->getMessage()];
        }
    }


}




?>