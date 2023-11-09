<?php

namespace Novaday\Moadian\Http;

use Novaday\Moadian\Services\EncryptionService;
use Novaday\Moadian\Services\SignatureService;

abstract class Request
{
    public $path;
    public $method = 'get';

    protected $headers;
    protected $body;
    protected $params;

    public function __construct()
    {
        $this->headers['accept'] = '*/*';
        $this->body   = [];
        $this->params = [];
    }

    public function getHeaders()
    {
        return $this->headers;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function getParams()
    {
        return $this->params;
    }

    abstract function prepare(SignatureService $signer, EncryptionService $encryptor);
}
