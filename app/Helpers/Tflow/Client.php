<?php namespace App\Helpers\Tflow;

define('DEBUG_HTTP', false);
define('DEBUG_SERVER_WITH_XDEBUG', false);

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Cookie\CookieJar;
use GuzzleHttp\Cookie\SetCookie;
use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Aws\S3\StreamWrapper;

class Client
{
    private $guzzleClient;

    private $clientId;
    private $clientSecret;

    private $accessToken;
    private $accessTokenType;
    private $accessTokenExpiration;

    public function __construct($baseUri, $clientId, $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;

        $params = [
            'base_uri' => $baseUri,
            'debug' => defined('DEBUG_HTTP') && DEBUG_HTTP === true,
        ];

        if(DEBUG_SERVER_WITH_XDEBUG)
        {
            $cookieJar = new CookieJar();
            $cookieJar->setCookie(new SetCookie([
                'Name' => 'XDEBUG_SESSION',
                'Value' => 'xdebug',
                'Domain' => 'tracker.local', // edit accordingly
            ]));
            $params['cookies'] = $cookieJar;
        }

        $this->guzzleClient = new GuzzleClient($params);
    }

    protected function getAccessToken()
    {
        if($this->accessToken === null)
        {
            //TODO: add support for token expiration

            $response = $this->guzzleClient->request('POST', '/oauth/access_token', [
                    'form_params' => [
                        'grant_type' => 'client_credentials',
                        'client_id' => $this->clientId,
                        'client_secret' => $this->clientSecret,
                    ],
                ]);

            $data = json_decode($response->getBody()->getContents(), true);
            if($data === null)
                throw new \Exception(json_last_error_msg());

            $this->accessToken = $data['access_token'];
            $this->accessTokenType = $data['token_type']; // only 'Bearer' token type is supported
            $this->accessTokenExpiration = $data['expires_in'];
        }

        return $this->accessToken;
    }

    protected function getAuthorizationHeaders()
    {
        return [
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
        ];
    }

    public function getOrder($orderId)
    {
        $response = $this->guzzleClient->request('GET', '/api/v2/order/' . $orderId, [
            'headers' => $this->getAuthorizationHeaders(),
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data;
    }

    public function createOrder($orderParameters)
    {
        $response = $this->guzzleClient->request('POST', '/api/v2/order/create', [
            'headers' => $this->getAuthorizationHeaders(),
            'json' => $orderParameters,
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data;
    }

    public function createCompany($companyParameters)
    {
        $response = $this->guzzleClient->request('POST', '/api/v2/client/create', [
            'headers' => $this->getAuthorizationHeaders(),
            'json' => $companyParameters,
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data;
    }

    public function listJobs($orderId = null, $jobName = null)
    {
        $options = [
            'headers' => $this->getAuthorizationHeaders(),
        ];
        $query = [];
        if($orderId !== null)
        {
            $query[] = ['order_id' => $orderId];
        }
        if($jobName !== null)
        {
            $query[] = ['job_name' => $jobName];
        }

        if(!empty($query))
            $options['query'] = $query;

        $response = $this->guzzleClient->request('GET', '/api/v2/job/list', $options);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data;
    }

    public function createJob($jobParameters)
    {
        $response = $this->guzzleClient->request('POST', '/api/v2/job/create', [
            'headers' => $this->getAuthorizationHeaders(),
            'json' => $jobParameters,
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data;
    }

    public function uploadArtworkCreateJob($jobParameters)
    {
        $response = $this->guzzleClient->request('POST', '/api/v2/job/uploadAndCreate', [
            'headers' => $this->getAuthorizationHeaders(),           
            'multipart' => [
                [
                    'name' => '_json',
                    'contents' => json_encode($jobParameters),
                ],
                [
                    'name' => 'artwork',
                    'contents' => fopen('F:\xampp\htdocs\easyorderbanner\doc\tflow\logo.png', 'rb'),
                ],
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data;
    }

    public function getAllowedTransitions($jobId)
    {
        $response = $this->guzzleClient->request('GET', '/api/v2/job/' . $jobId . '/allowedTransitions', [
            'headers' => $this->getAuthorizationHeaders()
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data;
    }

    public function uploadArtwork($jobId, $artworkPath)
    {
        $context = stream_context_create([
            's3' => ['seekable' => true]
        ]);

        $response = $this->guzzleClient->request('POST', '/api/v2/job/' . $jobId . '/executeTransition', [
            'headers' => $this->getAuthorizationHeaders(),
            'multipart' => [
                [
                    'name' => '_json',
                    'contents' => json_encode(['transition_name' => 'upload_first_revision']),
                ],
                [
                    'name' => 'artwork',
                    'contents' => fopen($artworkPath, 'r', false, $context),
                ],
            ],
        ]);
    }

    public function listUsers($email = null)
    {
        $options = [
            'headers' => $this->getAuthorizationHeaders(),
        ];
        if($email !== null)
        {
            $options['query'] = [
                'filter' => [
                    'field' => 'email',
                    'value' => $email,
                ],
            ];
        }

        $response = $this->guzzleClient->request('GET', '/api/v2/user/list', $options);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data;
    }

    public function listClients($name = null)
    {
        $options = [
            'headers' => $this->getAuthorizationHeaders(),
        ];
        if($name !== null)
        {
            $options['query'] = [
                'filter' => [
                    'field' => 'name',
                    'value' => $name,
                ],
            ];
        }

        $response = $this->guzzleClient->request('GET', '/api/v2/client/list', $options);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data;
    }

    public function listProducts($name = null)
    {
        $options = [
            'headers' => $this->getAuthorizationHeaders(),
        ];
        if($name !== null)
        {
            $options['query'] = [
                'filter' => [
                    'field' => 'name',
                    'value' => $name,
                ],
            ];
        }

        $response = $this->guzzleClient->request('GET', '/api/v2/product/list', $options);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data;
    }

    public function listTflows($tflowName = null, $queueName = null)
    {
        $options = [
            'headers' => $this->getAuthorizationHeaders(),
        ];
        $query = [];
        if($tflowName !== null)
        {
            $query[] = ['tflow_name' => $tflowName];
        }
        if($queueName !== null)
        {
            $query[] = ['queue_name' => $queueName];
        }

        if(!empty($query))
            $options['query'] = $query;

        $response = $this->guzzleClient->request('GET', '/api/v2/tflow/list', $options);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data;
    }

    public function checkReady($tflowId, $tflowName, $queuesInfo)
    {
       $options = [
          'headers' => $this->getAuthorizationHeaders(),
          'query' => [
             'tflow' => [
                'id' => $tflowId,
                'name' => $tflowName,
             ],
             'queues' => $queuesInfo,
          ],
       ];

       $response = $this->guzzleClient->request('POST', '/api/v2/tflowDownload/checkReady', $options);

       $data = json_decode($response->getBody()->getContents(), true);

       return $data;
    }

    public function createWebhook($webhookParameters)
    {
        $response = $this->guzzleClient->request('POST', '/api/v2/webhook/create', [
            'headers' => $this->getAuthorizationHeaders(),
            'json' => $webhookParameters,
        ]);

        $data = json_decode($response->getBody()->getContents(), true);

        return $data;
    }
}
