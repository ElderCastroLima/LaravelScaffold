<?php

namespace App\Services;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Log;

class SendRequest{
	private $client;
    private $config = [];
	public function __construct(){
		$this->config = [
			'verify' => false,
			'http_errors' => false,
			'headers' => ['Accept' => 'application/json'],
			'base_uri' => ENV('HOST_API')
		];
	}

    public function call($method, $resource, $header, $data){
		$this->client = new Client($this->config);
        $options['body'] = $data;
		$options['headers'] = $header;
		try {
			Log::info($method.' => '.ENV('HOST_API').ENV('URI').$resource);
			Log::info('Body =>');
			Log::info($data);
			return $this->client->request($method,ENV('URI').$resource,$options);
		} catch (\exception $e) {
			Log::error($e);
			return 'error';
		}
	}

	public function callExternal($method, $resource, $header, $data){
		$this->client = new Client($this->config);
        $options['body'] = $data;
		$options['headers'] = $header;
		try {
			Log::info($method.' => '.ENV('HOST_API').$resource);
			Log::info('Body =>');
			Log::info($data);
			return $this->client->request($method,$resource,$options);
		} catch (\exception $e) {
			Log::error($e);
			return 'error';
		}
	}

	public function callGetGroup($options, $header)
	{
		$this->config['headers']['Authorization'] = $header['Authorization'];
		$this->client = new Client($this->config);
		foreach ($options as $key => $value) {
			$promises[$key] = $this->client->getAsync(ENV('URI').$value);
		}

		// Wait on all of the requests to complete. Throws a ConnectException
		// if any of the requests fail
		$results = Promise\unwrap($promises);

		// Wait for the requests to complete, even if some of them fail
		$results = Promise\settle($promises)->wait();

		// Get response
		$return = array();
		foreach ($options as $key => $value) {
			$body = '';
			if ($results[$key]['value']->getStatusCode() != 200)
			{
				unset($return);
				$return['StatusCode'] = $results[$key]['value']->getStatusCode();
				$return['ReasonPhrase'] = $results[$key]['value']->getReasonPhrase();
				break;
			}

			if ($results[$key]['value']->getStatusCode() == 200)
			{
				$body = $results[$key]['value']->getBody();
				$return['StatusCode'] = $results[$key]['value']->getStatusCode();
				$return['ReasonPhrase'] = $results[$key]['value']->getReasonPhrase();
				$return[$key] = json_decode($body->getContents());
			}
		}
		return $return;
	}
}
