<?php

namespace Novaday\Moadian\Http;

use Novaday\Moadian\Services\EncryptionService;
use Novaday\Moadian\Services\SignatureService;
use Novaday\Moadian\Traits\HasToken;

class EconomicCodeInformation extends Request
{
    use HasToken;

    public function __construct(string $taxId)
    {
        parent::__construct();

        $this->path = 'taxpayer';
        $this->params['economicCode'] = $taxId;
    }

    public function prepare(SignatureService $signer, EncryptionService $encryptor)
    {
        $this->addToken($signer);
    }
}