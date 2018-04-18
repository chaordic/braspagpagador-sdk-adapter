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
use Webjump\Braspag\Pagador\Transaction\Api\CreditCard\Send\RequestInterface as CreditCardData;
use Webjump\Braspag\Pagador\Transaction\Api\Billet\Send\RequestInterface as BilletCardData;
use Webjump\Braspag\Pagador\Transaction\Api\Debit\Send\RequestInterface as DebitCardData;
class GetCommand extends CommandAbstract
{
    protected function execute()
    {
        $sales = SalesFactory::make($this->request);
        $client = ClientHttpFactory::make();

        $params = $this->request->getParams();
        $uriComplement = $params['uriComplement']['payment_id'];

        $environment = $this->request->getData()->getEnvironment();

        $response = $client->request($sales, 'GET', $uriComplement, $environment);

        $type = '';
        if ($this->request->getType()) {
            $type = $this->request->getType();
        }

        $this->result = ResponseFactory::make($this->getResponseToArray($response), $type);
    }

    protected function getType(array $data)
    {
        $type = '';

        if ($data['Payment']['Type'] === CreditCardData::PAYMENT_TYPE) {
            $type = ResponseFactory::CLASS_TYPE_CREDIT_CARD;
        }

        if ($data['Payment']['Type'] === BilletCardData::PAYMENT_TYPE) {
            $type = ResponseFactory::CLASS_TYPE_BILLET;
        }

        if ($data['Payment']['Type'] === DebitCardData::PAYMENT_TYPE) {
            $type = ResponseFactory::CLASS_TYPE_DEBIT_CARD;
        }

        return $type;
    }
}
