<?php

namespace Novaday\Moadian\Http;

use Ramsey\Uuid\Nonstandard\Uuid;

class Packet
{
    // Packet fields
    public $uid = '';
    public $packetType = "GET_SERVER_INFORMATION";
    public $retry = false;
    public $data;
    public $encryptionKeyId = '';
    public $symmetricKey = '';
    public $iv = '';
    public $fiscalId = '';
    public $dataSignature = '';

    public $path        = 'req/api/self-tsp/sync/GET_SERVER_INFORMATION';
    public $needToken   = false;
    public $needEncrypt = false;
   

    public function __construct() 
    {
        $this->uid = Uuid::uuid4()->toString();
    }

    public function toArray()
    {
        return [
            "uid" => $this->uid,
            "packetType" => $this->packetType,
            "retry" => $this->retry,
            "data" => $this->data,
            "encryptionKeyId" => $this->encryptionKeyId,
            "symmetricKey" => $this->symmetricKey,
            "iv" => $this->iv,
            "fiscalId" => $this->fiscalId,
            "dataSignature" => $this->dataSignature
        ];
    }
}