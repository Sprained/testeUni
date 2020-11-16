<?php

namespace App\Helpers;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Http;

class PagarMe
{
    private function makeRequest()
    {
        return Http::withOptions(['verify' => false])->withBasicAuth(env('API_KEY_PARGARME'), 'x');
    }

    private function getUrl($action)
    {
        return sprintf('%s%s', env('URL_PAGARME'), $action);
    }

    public function get($action)
    {
        return $this->makeRequest()->get($this->getUrl($action))->json();
    }

    public function post($action, $data)
    {
        return $this->makeRequest()->post($this->getUrl($action), $data)->json();
    }

    public function put($action, $data)
    {
        return $this->makeRequest()->put($this->getUrl($action), $data)->json();
    }

    public function cancel($action)
    {
        return $this->makeRequest()->post($this->getUrl($action))->json();
    }
}