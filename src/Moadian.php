<?php
namespace Novaday\Moadian;

use GuzzleHttp\Client;
use Novaday\Moadian\Exceptions\MoadianException;
use Novaday\Moadian\Http\EconomicCodeInformation;
use Novaday\Moadian\Http\FiscalInfo;
use Novaday\Moadian\Http\GetNonce;
use Novaday\Moadian\Http\InquiryByReferenceNumber;
use Novaday\Moadian\Http\InquiryByUid;
use Novaday\Moadian\Http\Request;
use Novaday\Moadian\Http\Response;
use Novaday\Moadian\Http\SendInvoice;
use Novaday\Moadian\Http\ServerInfo;
use Novaday\Moadian\Services\EncryptionService;
use Novaday\Moadian\Services\SignatureService;

class Moadian
{
    private Client $client;
    private SignatureService $signer;
    private EncryptionService $encryptor;
    private Response $response;

    public function __construct($privateKey, $certificate, $baseUri ='https://tp.tax.gov.ir/requestsmanager/api/v2/')
    {
        $this->client = new Client([
            'base_uri' => $baseUri,
            'headers'  => ['Content-Type' => 'application/json'],
            'timeout'  => 60
        ]);
        $this->signer = new SignatureService($privateKey, $certificate);
        $this->encryptor = new EncryptionService();
        $this->response = new Response();
    }

    /**
     * Sends a request to the API server.
     * 
     * @param Request $request The request to send.
     * @return mixed The response from API server.
     */
    public function sendRequest(Request $request)
    {
        $request->prepare($this->signer, $this->encryptor);

        $body = !empty($request->getBody()) ? json_encode($request->getBody()) : null;
        $httpResp = $this->client->request($request->method, $request->path, [
            'body'    => $body,
            'headers' => $request->getHeaders(),
            'query'   => $request->getParams()
        ]);

        return $this->response->setResponse($httpResp);
    }

    public function getNonce(int $validity = 30)
    {
        $request = new GetNonce($validity);

        $response = $this->sendRequest($request);

        if($response->isSuccessful()){
            $result = $response->getBody();
            return $result['nonce']; 
        }

        throw new MoadianException('Unable to retrieve Token');
    }

    public function getServerInfo()
    {
        $request = new ServerInfo();
        return $this->sendRequest($request);
    }

    public function getFiscalInfo()
    {
        $request = new FiscalInfo();
        return $this->sendRequest($request);
    }

    /**
     * Inquiry invoice with reference uuid.
     * 
     * @param string $uid
     * @param string $start Optional. start time e.g 2023-05-14T00:00:00.000000000+03:30
     * @param string $end Optional. end time e.g 2023-05-14T23:59:59.123456789+03:30
     */
    public function inquiryByUid(string $uid, string $start = '', string $end = '')
    {
        $request = new InquiryByUid($uid, $start, $end);
        return $this->sendRequest($request);
    }

    /**
     * Inquiry invoice with reference ID.
     * 
     * @param string $referenceId
     * @param string $start Optional. start time e.g 2023-05-14T00:00:00.000000000+03:30
     * @param string $end Optional. end time e.g 2023-05-14T23:59:59.123456789+03:30
     */
    public function inquiryByReferenceNumbers(string $referenceId, string $start = '', string $end = '')
    {
        $request = new InquiryByReferenceNumber($referenceId, $start, $end);
        return $this->sendRequest($request);
    }

    public function getEconomicCodeInformation(string $taxID)
    {
        if (strlen($taxID) < 9 || strlen($taxID) >= 12)
            throw new MoadianException('$taxID must be between 10 and 11 digits');

        $request = new EconomicCodeInformation($taxID);
        return $this->sendRequest($request);
    }

    public function sendInvoice(Invoice $invoice)
    {
        $request = new SendInvoice($invoice);
        $this->requirePublicKey();
        return $this->sendRequest($request);
    }

    private function requirePublicKey()
    {
        if (empty($this->encryptor->publicKey)) {
            $serverInfo = $this->getServerInfo();

            if ($serverInfo->isSuccessful()) {
                $info = $serverInfo->getBody();
                $this->encryptor->KeyId = $info['publicKeys'][0]['id'];

                $pem = chunk_split($info['publicKeys'][0]['key'], 64, "\n");
                $this->encryptor->publicKey = "-----BEGIN PUBLIC KEY-----\n" . $pem . "-----END PUBLIC KEY-----\n";
            }
        }
    }
}
