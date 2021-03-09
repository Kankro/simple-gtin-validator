<?php

class GtinValidator
{
    public function isValidGtin($code)
    {
        // Validates any GTIN-8, GTIN-12, GTIN-13 or GTIN-14 code.
        $cleanedCode = $this->clean($code);
        return $this->isValidCode($cleanedCode);
    }

    public function addCheckDigit($code)
    {
        // Adds a check digit to the end of code.
        $cleanedCode = $this->clean($code);
        $cleanedCode = str_pad($cleanedCode, 13, "0", STR_PAD_LEFT);
        return $cleanedCode . (string) $this->gtinChecksum($cleanedCode);
    }

    protected function clean($code)
    {
        if (is_numeric($code)) {
            $code = (string)$code;
        } elseif (is_string($code)) {
            $code = trim(str_replace("-", "", $code));
        } else {
            throw new Exception("Expected string or integer type as input parameter");
        }
        return $code;
    }

    protected function isValidCode($code)
    {
        if (!is_numeric($code) || !in_array(strlen($code), [8,12,13,14])) {
            return false;
        }
        return $this->isGtinChecksumValid(str_pad($code, 14, "0", STR_PAD_LEFT));
    }

    protected function gtinChecksum($code)
    {
        $total = 0;
        $array = [];
        for ($i=0; $i < strlen($code); $i++) {
            $array[] = $code[$i];
        }
        foreach ($array as $k => $c) {
            if ($k % 2 == 1) {
                $total = $total + (int)$c;
            } else {
                $total = $total + (3 * (int)$c);
            }
        }
        $check_digit = (10 - ($total % 10)) % 10;
        return $check_digit;
    }

    protected function isGtinChecksumValid($code)
    {
        return (int)substr($code, strlen($code)-1, strlen($code)) == $this->gtinChecksum(substr($code, 0, -1));
    }
}
