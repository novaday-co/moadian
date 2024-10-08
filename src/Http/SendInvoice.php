<?php

namespace Novaday\Moadian\Http;

use Novaday\Moadian\Invoice;
use Novaday\Moadian\Services\EncryptionService;
use Novaday\Moadian\Services\SignatureService;
use Novaday\Moadian\Traits\HasToken;
use Ramsey\Uuid\Nonstandard\Uuid;

class SendInvoice extends Request
{
    use HasToken;

    private Invoice $invoice;

    public function __construct(Invoice $invoice)
    {

        parent::__construct();

        $this->path    = 'invoice';
        $this->method  = 'post';
        $this->invoice = $invoice;
        $this->headers['Content-Type'] = 'application/json';
    }

    public function prepare(SignatureService $signer, EncryptionService $encryptor)
    {
        $this->addToken($signer);
        $jws = $signer->sign($this->invoice->toArray());

        $aesHex = bin2hex(random_bytes(32));
        $iv     = bin2hex(random_bytes(12));

        $jwe = $encryptor->encrypt($jws, hex2bin($aesHex), hex2bin($iv));

        $this->body[] = [
            'payload' => $jwe,
            'header'  => [
                'requestTraceId' => Uuid::uuid4()->toString(),
                'fiscalId' => config('moadian.username')
            ]
        ];
    }
}
