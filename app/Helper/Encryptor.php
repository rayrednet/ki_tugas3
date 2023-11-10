<?php

namespace App\Helper;

use Exception;
use PhpParser\Node\Expr\Cast\String_;
use phpseclib3\Crypt\AES;
use phpseclib3\Crypt\DES;
use phpseclib3\Crypt\Hash;
use phpseclib3\Crypt\RC4;

class Encryptor {

    public const AES_CBC = 'aes-cbc';
    public const AES_CFB = 'aes-cfb';
    public const AES_OFB = 'aes-ofb';
    public const AES_CTR = 'aes-ctr';
    public const DES_CBC = 'des-cbc';
    public const DES_CFB = 'des-cfb';
    public const DES_OFB = 'des-ofb';
    public const DES_CTR = 'des-ctr';
    public const RC4 = 'rc4';

    private AES|DES|RC4|null $encryptor = null;

    public function __construct(String $type, String $key, String $iv)
    {
        if (strlen($key) != 32) {
            throw new Exception('Key harus 32 byte!');
        }
        if (strlen($iv) != 16) {
            throw new Exception('IV harus 16 byte!');
        }

        if (substr($type, 0, 3) == 'des') {
            $key = substr($key, 0, 8);
            $iv = substr($iv, 0, 8);
        }
        else if ($type == Encryptor::RC4) {
            $hasher = new Hash('sha256');
            for ($i = 0; $i < 3; $i++) {
                $key .=  $hasher->hash($key);
            }
        }

        if ($type == Encryptor::AES_CBC) {
            $this->encryptor = new AES('cbc');
            $this->encryptor->setKey($key);
            $this->encryptor->setIV($iv);
        }
        else if ($type == Encryptor::AES_CFB) {
            $this->encryptor = new AES('cfb');
            $this->encryptor->setKey($key);
            $this->encryptor->setIV($iv);
        }
        else if ($type == Encryptor::AES_OFB) {
            $this->encryptor = new AES('ofb');
            $this->encryptor->setKey($key);
            $this->encryptor->setIV($iv);
        }
        else if ($type == Encryptor::AES_CTR) {
            $this->encryptor = new AES('ctr');
            $this->encryptor->setKey($key);
            $this->encryptor->setIV($iv);
        }
        else if ($type == Encryptor::DES_CBC) {
            $this->encryptor = new DES('cbc');
            $this->encryptor->setKey($key);
            $this->encryptor->setIV($iv);
        }
        else if ($type == Encryptor::DES_CFB) {
            $this->encryptor = new DES('cfb');
            $this->encryptor->setKey($key);
            $this->encryptor->setIV($iv);
        }
        else if ($type == Encryptor::DES_OFB) {
            $this->encryptor = new DES('ofb');
            $this->encryptor->setKey($key);
            $this->encryptor->setIV($iv);
        }
        else if ($type == Encryptor::DES_CTR) {
            $this->encryptor = new DES('ctr');
            $this->encryptor->setKey($key);
            $this->encryptor->setIV($iv);
        }
        else if ($type == Encryptor::RC4) {
            $this->encryptor = new RC4();
            $this->encryptor->setKey($key);
        }
        else {
            throw new Exception('Enkripsi tidak didukung!');
        }
    }

    public function encrypt(String $data) : String
    {
        return $this->encryptor->encrypt($data);
    }

    public function decrypt(String $data) : String
    {
        return $this->encryptor->decrypt($data);
    }
}
