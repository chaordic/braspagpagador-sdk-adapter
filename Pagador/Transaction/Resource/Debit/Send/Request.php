<?php
/**
 * @author      Webjump Core Team <dev@webjump.com>
 * @copyright   2016 Webjump (http://www.webjump.com.br)
 * @license     http://www.webjump.com.br  Copyright
 *
 * @link        http://www.webjump.com.br
 *
 */
namespace Webjump\Braspag\Pagador\Transaction\Resource\Debit\Send;


use Webjump\Braspag\Pagador\Transaction\Resource\RequestAbstract;
use Webjump\Braspag\Pagador\Transaction\Api\Debit\Send\RequestInterface as Data;

class Request extends RequestAbstract
{
    /**
     * @param Data $data
     */
    public function __construct(Data $data)
    {
        $this->data = $data;
        $this->prepareParams();
    }

    /**
     * @return $this
     */
    protected function prepareParams()
    {
        $this->params = [
            'headers' => [
                'Content-Type' => self::CONTENT_TYPE_APPLICATION_JSON,
                'MerchantId' => $this->data->getMerchantId(),
                'MerchantKey' => $this->data->getMerchantKey()
            ],
            'body' => [
                'MerchantOrderId' => $this->data->getMerchantOrderId(),
                'Customer' => [
                    'Name' => $this->data->getCustomerName(),
                ],

                'Payment' => [
                    'Type' => Data::PAYMENT_TYPE,
                    'Amount' => $this->data->getPaymentAmount(),
                    'Provider' => $this->data->getPaymentProvider(),
                    'ReturnUrl' => $this->data->getPaymentReturnUrl(),
                    'DebitCard' => $this->getDebitCardParams()
                ]
            ]
        ];

        return $this;
    }

    protected function getDebitCardParams()
    {
        if ($this->data->getPaymentCreditSoptpaymenttoken()) {
            return [
                'paymentToken' => $this->data->getPaymentCreditSoptpaymenttoken(),
                'brand' => $this->data->getPaymentCreditCardBrand(),
                'saveCard' => $this->data->getPaymentCreditCardSaveCard(),
            ];
        }

        return [
            'CardNumber' => $this->data->getPaymentDebitCardCardNumber(),
            'Holder' => $this->data->getPaymentDebitCardHolder(),
            'ExpirationDate' => $this->data->getPaymentDebitCardExpirationDate(),
            'SecurityCode' => $this->data->getPaymentDebitCardSecurityCode(),
            'Brand' => $this->data->getPaymentDebitCardBrand(),
        ];
    }
}
