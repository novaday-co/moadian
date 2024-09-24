<?php

namespace Novaday\Moadian\Http;

use Novaday\Moadian\Services\EncryptionService;
use Novaday\Moadian\Services\SignatureService;
use Novaday\Moadian\Traits\HasToken;

class InquiryByReferenceNumber extends Request
{
    use HasToken;

    public function __construct(string $referenceId, string $start = '', string $end = '')
    {
        parent::__construct();

        $this->path = 'inquiry-by-reference-id';
        $this->params['referenceIds'] = $referenceId;

        if (!empty($start)) {
            $this->params['start'] = $start;
        }
        if (!empty($end)) {
            $this->params['end'] = $end;
        }
    }

    public function prepare(SignatureService $signer, EncryptionService $encryptor)
    {
        $this->addToken($signer);
    }
}
