<?php

namespace Novaday\Moadian\Http;

use Novaday\Moadian\Services\EncryptionService;
use Novaday\Moadian\Services\SignatureService;
use Novaday\Moadian\Traits\HasToken;

class FiscalInfo extends Request
{
    use HasToken;

    public function __construct()
    {
        parent::__construct();

        $this->path = 'fiscal-information';
        $this->params['memoryId'] = config('moadian.username');
    }

    public function prepare(SignatureService $signer, EncryptionService $encryptor)
    {
        $this->addToken($signer);
    }
}