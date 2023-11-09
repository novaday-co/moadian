<?php 

namespace Novaday\Moadian\Http;

use Ramsey\Uuid\Uuid;

class Request
{
    // Headers fields
    public $authorization;
    public $requestTraceId;
    public $timestamp = '';

    // Body fields
    private $packet = null;
    private $packets = [];
    public $signature;
    public $signatureKeyId = null;

    public $needToken = false;

    public function __construct() 
    {
        $this->timestamp = (string)intval(microtime(true) * 1000);
        $this->requestTraceId = Uuid::uuid4()->toString();
    }

    public function setPacket(Packet $packet)
    {
        $this->packet = $packet;
        $this->needToken = $packet->needToken;
    }

    public function setPackets(array $packets)
    {
        $this->packets = $packets;
        $this->needToken = $packets[0]->needToken;
    }

    public function getPacket()
    {
        return $this->packet ?? $this->packets[0];
    }

    public function getPackets()
    {
        return $this->packets;
    }

    public function setToken(string $token)
    {
        $this->authorization = $token;
    }

    public function getHeaders()
    {
        $headers = [
            'requestTraceId' => $this->requestTraceId,
            'timestamp' => $this->timestamp
        ];

        if ($this->needToken) {
            $headers['authorization'] = 'Bearer ' . $this->authorization;
        }

        return $headers;
    }

    public function getBody()
    {  
        return json_encode($this->toArray());
    }

    public function toArray()
    {
        $packetKey   = '';
        $packetValue = '';

        if (isset($this->packet)) {
            $packetKey   = 'packet';
            $packetValue = $this->packet->toArray();
        } else {
            $packetKey   = 'packets';
            $packetValue = [];
            foreach ($this->packets as $packet) {
                $packetValue[] = $packet->toArray();
            }
        }

        return [
            $packetKey  => $packetValue,
            'signature' => $this->signature,
        ];
    }
}