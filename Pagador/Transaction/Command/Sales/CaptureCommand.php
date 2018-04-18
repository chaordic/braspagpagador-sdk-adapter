<?php
/**
 * @author      Webjump Core Team <dev@webjump.com>
 * @copyright   2016 Webjump (http://www.webjump.com.br)
 * @license     http://www.webjump.com.br  Copyright
 *
 * @link        http://www.webjump.com.br
 *
 */
namespace Webjump\Braspag\Pagador\Transaction\Command\Sales;


use Webjump\Braspag\Factories\ClientHttpFactory;
use Webjump\Braspag\Factories\ResponseFactory;
use Webjump\Braspag\Factories\SalesFactory;
use Webjump\Braspag\Pagador\Transaction\Command\CommandAbstract;

class CaptureCommand extends CommandAbstract
{
    protected function execute()
    {
        $sales = SalesFactory::make($this->request);
        $client = ClientHttpFactory::make();

        $params = $this->request->getParams();
        $uriComplement = sprintf('%s/capture/', $params['uriComplement']['payment_id']);

        if (isset($params['uriComplement']['additional'])) {
            $uriComplement .= '?' . \http_build_query($params['uriComplement']['additional']);
        }

        $environment = $this->request->getData()->getEnvironment();

        $response = $client->request($sales, 'PUT', $uriComplement, $environment);

        $this->result = ResponseFactory::make($this->getResponseToArray($response), 'actions');
    }
}
