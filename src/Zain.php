<?php

namespace AlkhatibDev\LaravelZain;

use Illuminate\Support\Facades\Http;
use AlkhatibDev\LaravelZain\Exceptions\InvlalidConfigsValuesException;
use Illuminate\Support\Facades\Log;

class Zain {

    /**
     * Base DSP server url
     *
     * @var string
     */
    protected $baseURL;

    /**
     * Service product code
     *
     * @var string
     */
    protected $productCode;

    /**
     * Service username
     *
     * @var string
     */
    protected $username;

    /**
     * Service password
     *
     * @var string
     */
    protected $password;

    /**
     * Remember token when fetched from DSP
     *
     * @var boolean
     */
    protected $rememberToken;

    /**
     * Custom DSP token
     *
     * @var string
     */
    protected $token;


    /**
     * Create Zain instance
     *
     * @return void
     */
    public function __construct()
    {
        $this->baseURL = config('laravel-zain.base_url');
        $this->password = config('laravel-zain.password');
        $this->username = config('laravel-zain.username');
        $this->rememberToken = config('laravel-zain.remember_token');
        $this->productCode = config('laravel-zain.product_code');

        $this->validateConfigs();
    }

    /**
     * Login to DSP
     *
     * @return array
     */
    public function login()
    {
        $this->log("Before Login");

        $response = Http::post($this->baseURL . 'login.php', [
            'username' => $this->username,
            'password' => $this->password,
            'remember_me' => $this->rememberToken,
        ]);

        $body = json_decode($response->getBody(), true);

        $this->log("Login (login.php)");
        $this->log($response->body());

        return $body;
    }

    /**
     * Initial subscription
     *
     * @param string $phone
     * @return array
     */
    public function initiate($phone)
    {
        $this->log("Before Initiate");

        $response = Http::withHeaders($this->getHeaders())
            ->post($this->baseURL . 'initiate.php', [
                'msisdn' => $phone,
                'product_code' => $this->productCode,
            ]);

        $body = json_decode($response->getBody(), true);

        $this->log("Initiate (initiate.php)");
        $this->log($body);

        return $body;
    }

    /**
     * Verify OTP and complete payment/subscription
     *
     * @param string $otp
     * @param string $requestId
     * @return array
     */
    public function verify($otp, $requestId)
    {
        $this->log("Before Verify OTP");

        $response = Http::withHeaders($this->getHeaders())
            ->post($this->baseURL . 'payment.php', [
                'otp' => $otp,
                'subscribe_request_id' => $requestId,
            ]);

        $body = json_decode($response->getBody(), true);

        $this->log("Verify OTP (payment.php)");
        $this->log($body);

        return $body;
    }

    /**
     * Check subscription status of a single phone
     *
     * @param string $phone
     * @return array
     */
    public function checkSubscription($phone)
    {
        $this->log("Before Check Subscription");

        $response = Http::withHeaders($this->getHeaders())
        ->post($this->baseURL . 'check.php', [
            'msisdn' => $phone,
            'product_code' => $this->productCode,
        ]);

        $body = json_decode($response->getBody(), true);

        $this->log("Check Subscription (check.php)");
        $this->log($body);

        return $body;
    }

    /**
     * Unsubscribe specific phone
     *
     * @param string $phone
     * @return array
     */
    public function unsubscribe($phone)
    {
        $this->log("Before Unsubscribe");

        $response = Http::withHeaders($this->getHeaders())
            ->post($this->baseURL . 'cancel.php', [
                'msisdn' => $phone,
                'product_code' => $this->productCode,
            ]);

        $body = json_decode($response->getBody(), true);

        $this->log("Unsubscribe (cancel.php)");
        $this->log($body);

        return $body;
    }

    /**
     * Get headers
     *
     * @return array
     */
    private function getHeaders()
    {
        return [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $this->getAvailableToken(),
        ];
    }

    /**
     * Get DSP token
     *
     * @return string
     */
    public function token()
    {
        $this->log("Get Remote Token");

        $response = $this->login();

        if (isset($response['success']) && $response['success'] == true) {

            $this->log("Remote Token Fetched");

            $this->token = $response['token']; // To be cached into this instance

            return $response['token'];
        }

        return null;
    }

    /**
     * Get available DSP token, from loacl or remote
     *
     * @return string
     */
    private function getAvailableToken()
    {
        return $this->token ?? $this->token();
    }

    /**
     * Provide ready local DSP token
     *
     * @return AlkhatibDev\LaravelZain\Zain
     */
    public function withToken($token)
    {
        $this->log("Set Token Manually");

        $this->token = $token;

        return $this;
    }

    /**
     * Validate config file and its values
     *
     * @return void
     */
    public function validateConfigs()
    {
        $this->log("Validate Configs");

        if (
            is_null($this->baseURL) ||
            is_null($this->password) ||
            is_null($this->username) ||
            is_null($this->rememberToken) ||
            is_null($this->productCode)
        ) {
            $message = __('The provided configs is invalid, make sure laravel-zain config file is published and all its configs are set.');

            $this->log($message, 'error');

            throw new InvlalidConfigsValuesException($message);
        }
    }

    /**
     * Custom log function
     *
     * @param any $data
     * @param string $type
     * @return void
     */
    private function log($data, $type = 'debug')
    {
        if(config('laravel-zain.enable_logging', false)) {
            if ($type === 'error') {
                Log::error($data);
            } else {
                Log::debug($data);
            }
        }
    }

}
