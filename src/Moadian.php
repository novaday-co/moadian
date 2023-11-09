<?php
namespace Novaday\Moadian;

use Novaday\Moadian\Exceptions\MoadianException;
use Novaday\Moadian\Services\ApiClient;

class Moadian
{
    protected $username;

    protected $privateKey;

    protected $client;

    public function __construct($username, $privateKey, $baseUri ='https://tp.tax.gov.ir/')
    {
        $this->username = $username;
        $this->privateKey = $privateKey;
        $this->client = new ApiClient($username, $privateKey, $baseUri);
    }

    public function getToken()
    {
        return $this->client->getToken();
    }

    public function getServerInfo()
    {
        $response = $this->client->getServerInfo();

        return $response;
    }

    public function getFiscalInfo()
    {
        $response = $this->client->getFiscalInfo();

        return $response;
    }

    public function inquiryByUid(array $uids)
    {
        $response = $this->client->inquiryByUid($uids);
        return $response;
    }

    public function inquiryByReferenceNumbers(array $refNums)
    {
        $response = $this->client->inquiryByReferenceNumbers($refNums);
        return $response;
    }

    public function getEconomicCodeInformation(string $taxID)
    {
        if (strlen($taxID) < 9 || strlen($taxID) >= 12)
            throw new MoadianException('$taxID must be between 10 and 11 digits');

        return $this->client->getEconomicCodeInformation($taxID);
    }

    public function sendInvoice(Invoice $moadianInvoice){
        return $this->client->sendInvoice($moadianInvoice);
    }
}
