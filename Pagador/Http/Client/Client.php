<?php
/**
 * @author      Webjump Core Team <dev@webjump.com>
 * @copyright   2016 Webjump (http://www.webjump.com.br)
 * @license     http://www.webjump.com.br  Copyright
 *
 * @link        http://www.webjump.com.br
 *
 */
namespace Webjump\Braspag\Pagador\Http\Client;

use Webjump\Braspag\Factories\HttpFactory as HttpClient;
use Webjump\Braspag\Pagador\Http\Services\ServiceInterface;
use Webjump\Braspag\Factories\HandlerFactory;

class Client implements ClientInterface
{
    protected $client;

    protected $handler;

    /**
     * Client constructor.
     */
    public function __construct()
    {
        $this->client = HttpClient::make();
        $this->handler = HandlerFactory::make();
    }

    /**
     * @param ServiceInterface $service
     * @param string $method
     * @param string $uriComplement
     * @param string $environment
     * @return mixed|\Psr\Http\Message\ResponseInterface
     */
    public function request(
        ServiceInterface $service,
        $method = 'POST',
        $uriComplement = '',
        $environment = 'production'
    )
    {
        $params = $service->getRequest()->getParams();

        switch (strtolower($environment)) {
            case 'homolog':
                $apiURI = self::API_URI_HOMOLOG;
                $apiConsultURI = self::API_CONSULT_URI_HOMOLOG;
                break;
            case 'sandbox':
                $apiURI = self::API_URI_SANDBOX;
                $apiConsultURI = self::API_CONSULT_URI_SANDBOX;
                break;
            default:
                $apiURI = self::API_CONSULT_URI_SANDBOX;
                $apiConsultURI = self::API_CONSULT_URI_TEST;
        }

        $uri = $apiURI . $service->getEndPoint() . $uriComplement;

        if ($method === 'GET') {
            $uri = $apiConsultURI . $service->getEndPoint() . $uriComplement;
        }

        $headers = isset($params['headers']) ? $params['headers'] : [];
        $body = isset($params['body']) ? $params['body'] : [];
        return $this->client->request(
            $method,
            $uri,
            [
                'headers' => $headers,
                'body' => \json_encode($body),
                'handler' => $this->handler
            ]
        );
    }

    public function getClient()
    {
        return $this->client;
    }
}
