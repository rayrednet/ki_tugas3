<?php

namespace App\Helper;

use App\Models\User;
use Exception;
use phpseclib3\Crypt\Hash;

class PDF {

    private string $fileContent;

    public function __construct(string $fileContent)
    {
        $this->fileContent = $fileContent;
    }

    private function getSignature() : array {
        if (preg_match('/<!signature:(.*)>/', $this->fileContent, $matches, PREG_OFFSET_CAPTURE)) {
            return $matches;
        }
        return [];
    }

    public function putSignature(User $user) : string {
        if (count($this->getSignature()) > 0) {
            throw new Exception('Already signed');
        }

        $hasher = new Hash('sha256');
        $hashedContent = $hasher->hash($this->fileContent);
        $encryptedHashedContent = base64_encode($user->sign($hashedContent));

        $newData = "{$this->fileContent}<!signature:{$encryptedHashedContent}>";

        return $newData;
    }

    public function checkSignature(User $user) : bool {
        $signature = $this->getSignature();
        if (count($signature) == 0) {
            return false;
        }

        $hasher = new Hash('sha256');

        $loc = $signature[0][1];
        $pdfContent = substr($this->fileContent, 0, $loc).substr($this->fileContent, $loc + strlen($signature[0][0]));
        $hashedContent = $hasher->hash($pdfContent);

        return $user->verify($hashedContent, base64_decode($signature[1][0]));
    }
}
