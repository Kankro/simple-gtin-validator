GTIN Validator
==============

Validates GTIN (Global Trade Item Number) codes by calculating checksums. Supports both Python 2 and 3.

GTIN comprises of GTIN-8, GTIN-12, GTIN-13 and GTIN-14 codes. EAN, UPC and ISBN can be thought of as subsets of GTIN. For more information, see: http://www.gs1.org/barcodes/technical/idkeys/gtin  and  http://en.wikipedia.org/wiki/Global_Trade_Item_Number

Copied from python version of charithe
-----
https://github.com/charithe/gtin-validator

Usage
-----
```
require_once "./GtinValidator.php";
$gValidator = new GtinValidator();

$gitin = '9780552133265';
var_dump($gValidator->isValidGtin($gitin));

$gitin = '978-0-552-13326-5';
var_dump($gValidator->isValidGtin($gitin));

$gitin = 9780552133265;
var_dump($gValidator->isValidGtin($gitin));
```
