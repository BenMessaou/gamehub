<?php
class GoogleAuthenticator
{
    public function createSecret($length = 16)
    {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < $length; $i++) {
            $secret .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $secret;
    }

    public function getQRCodeGoogleUrl($name, $secret)
    {
        $url = "otpauth://totp/" . urlencode($name) . "?secret=" . $secret . "&issuer=GameHub";
        return "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($url);
    }

    public function verifyCode($secret, $code, $discrepancy = 1)
    {
        $currentTimeSlice = floor(time() / 30);

        for ($i = -$discrepancy; $i <= $discrepancy; $i++) {
            $calculatedCode = $this->getCode($secret, $currentTimeSlice + $i);
            if (hash_equals($calculatedCode, $code)) {
                return true;
            }
        }
        return false;
    }

    private function getCode($secret, $timeSlice)
    {
        $secretKey = $this->base32Decode($secret);

        $time = chr(0).chr(0).chr(0).chr(0).pack('N*', $timeSlice);
        $hash = hash_hmac('SHA1', $time, $secretKey, true);

        $offset = ord(substr($hash, -1)) & 0x0F;
        $hashPart = substr($hash, $offset, 4);

        $value = unpack("N", $hashPart)[1];
        $value = $value & 0x7FFFFFFF;

        return str_pad($value % 1000000, 6, '0', STR_PAD_LEFT);
    }

    private function base32Decode($secret)
    {
        $base32chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $bits = '';
        foreach (str_split(strtoupper($secret)) as $char) {
            if ($char == '=') break;
            $bits .= str_pad(decbin(strpos($base32chars, $char)), 5, '0', STR_PAD_LEFT);
        }

        $bytes = '';
        for ($i = 0; $i + 8 <= strlen($bits); $i += 8) {
            $bytes .= chr(bindec(substr($bits, $i, 8)));
        }
        return $bytes;
    }
}