<?php
namespace App\Service;

use Monolog\DateTimeImmutable;

class JWTService
{
    public function generate(array $header, array $payload, string $secret, int $validity=10800): string
    {
        /**
         * Generates a JWT token
         * @param array $header
         * @param array $payload
         * @param string $secret
         * @param int $validity
         * @return string
         */
        //token will be valid for 3 hours
        if($validity > 0) {
            $now = new \DateTimeImmutable();
            $exp = $now->getTimeStamp() + $validity;
            //iat = issued at
            $payload['iat'] = $now->getTimeStamp();
            //exp = expiration time
            $payload['exp'] = $exp;

        }

        // Encode header
        $header = base64_encode(json_encode($header));
        // Encode payload
        $payload = base64_encode(json_encode($payload));

        $header = str_replace(['+', '/', '='], ['-', '_', ''], $header);
        $payload = str_replace(['+', '/', '='], ['-', '_', ''], $payload);

        $secret = base64_encode($secret);
        // Create signature
        $signature = hash_hmac('sha256', $header . '.' . $payload, $secret, true);
        // Encode signature
        $signature = base64_encode($signature);

        $signature = str_replace(['+', '/', '='], ['-', '_', ''], $signature);

        // Create JWT
        return $header . '.' . $payload . '.' . $signature;
    }

    //
    public function isValid(string $token): bool
    {
        /**
         * Checks if a JWT token is valid
         * @param string $token
         * @return bool
         */
        return preg_match(
                '/^[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+\.[a-zA-Z0-9\-\_\=]+$/',
                $token
            ) === 1;
    }

    public function getPayload(string $token): array{
        /**
         * Returns the payload of a JWT token
         * @param string $token
         * @return array
         */
        if(!$this->isValid($token)){
            return [];
        }
        $payload = explode('.', $token)[1];
        $payload = base64_decode($payload);
        $payload = json_decode($payload, true);
        return $payload;
    }

    public function getHeader(string $token): array{
        /**
         * Returns the header of a JWT token
         * @param string $token
         * @return array
         */
        if(!$this->isValid($token)){
            return [];
        }
        $header = explode('.', $token)[0];
        $header = base64_decode($header);
        $header = json_decode($header, true);
        return $header;
    }

    public function isExpired(string $token): bool{
        /**
         * Checks if a JWT token is expired
         * @param string $token
         * @return Boolean
         */
        if(!$this->isValid($token)){
            return true;
        }
        $payload = $this->getPayload($token);
        if(!isset($payload['exp'])){
            return true;
        }
        $now = new \DateTimeImmutable();
        $exp = $payload['exp'];
        return $now->getTimeStamp() > $exp;
    }

    public function check(string $token, string $secret): bool{
        /**
         * Checks if a JWT token is valid
         * @param string $token
         * @param string $secret
         * @return Boolean
         */
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);
        $verifToken = $this->generate($header, $payload, $secret, 0);
        return $verifToken === $token;
    }
}
