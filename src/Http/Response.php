<?php

namespace Novaday\Moadian\Http;

class Response
{
    private $statusCode;
    private $error;
    private $body;

    public function setResponse($httpResponse)
    {
        $this->statusCode = $httpResponse->getStatusCode();

        if ($this->statusCode >= 400) {
            $this->error = $httpResponse->getBody()->getContents();
            $this->body = $this->parseJson($this->error);
        } else {
            $this->body = $httpResponse->getBody()->getContents();
            $this->body = $this->parseJson($this->body);
        }

        return $this;
    }

    public function getStatusCode()
    {
        return $this->statusCode;
    }

    public function getError()
    {
        return $this->error;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function isSuccessful()
    {
        return is_null($this->error) && $this->statusCode >= 200 && $this->statusCode < 300;
    }

    private function parseJson($json)
    {
        $data = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            // Handle JSON parsing error
            $this->error = 'Failed to parse response as JSON';
            return [
                'error' => $this->error,
                'raw' => $json,
            ];
        }

        if (!$this->isSuccessful()) {
            return [
                'error' => $data['errors'][0]['message'],
                'errorCode' => $data['errors'][0]['code']
            ];
        }

        return $data;
    }
}
