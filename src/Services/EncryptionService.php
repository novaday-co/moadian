<?php

namespace Novaday\Moadian\Services;

class EncryptionService
{
    private const CIPHER = 'aes-256-gcm';
    private const TAG_LENGTH = 16;
    /**
     * @var		string	$publicKey Must be get by getServerInfo
     */
    public $publicKey = '';
    /**
     * @var		string	$KeyId Must be get by getServerInfo
     */
    public $KeyId = '';

    public function __construct($publicKey = '', $KeyId = '')
    {
        $this->publicKey = $publicKey;
        $this->KeyId = $KeyId;
    }

    public function encryptAesKey(string $aesKey): string
    {
        $rsa = $this->getRsaInstance();

        if ($rsa !== null) {
            $encrypted = $rsa->encrypt($aesKey);
        } else {
            $encrypted = $this->pureOpensslEncryption($aesKey);
        }

        return base64_encode($encrypted);
    }

    private function getRsaInstance()
    {
        $rsaClass = class_exists(\phpseclib3\Crypt\RSA::class) ? \phpseclib3\Crypt\RSA::class : \phpseclib\Crypt\RSA::class;

        if (class_exists($rsaClass)) {
            $rsa = new $rsaClass();
            if (property_exists($rsa, 'publicKey')) {
                $rsa->setPublicKey($this->publicKey);
                $rsa->setHash('sha256');
                $rsa->setMGFHash('sha256');
            } else {
                $rsa = \phpseclib3\Crypt\RSA::loadPublicKey($this->publicKey);
            }
            return $rsa;
        }

        return null;
    }

    private function pureOpensslEncryption(string $aesKey): string
    {
        if (!openssl_public_encrypt($aesKey, $encrypted, $this->publicKey, OPENSSL_PKCS1_OAEP_PADDING)) {
            throw new \RuntimeException('Unable to encrypt AES Key');
        }

        return $encrypted;
    }

    /**
     * Encrypts the given text using the provided key and initialization vector (IV).
     *
     * @param string $text The plaintext to be encrypted.
     * @param string $key The encryption key used for encryption in binary format.
     * @param string $iv The initialization vector (IV) used for encryption in binary format.
     * @return string The base64-encoded encrypted ciphertext with authentication tag appended.
     */
    public function encrypt(string $text, string $key, string $iv): string
    {
        $text = $this->xorStrings($text, $key);

        $tag = '';

        $cipherText = openssl_encrypt($text, self::CIPHER, $key, OPENSSL_RAW_DATA, $iv, $tag, "", self::TAG_LENGTH);

        // Return the base64-encoded encrypted ciphertext with the authentication tag appended
        return base64_encode($cipherText . $tag);
    }

    public function xorStrings(string $source, string $key): string
    {
        $sourceLength = strlen($source);
        $keyLength = strlen($key);
        $result = '';
        for ($i = 0; $i < $sourceLength; $i++) {
            $result .= $source[$i] ^ $key[$i % $keyLength];
        }
        return $result;
    }

    public function decrypt(string $encryptedText, string $key, string $iv, int $tagLen)
    {

    }

}
