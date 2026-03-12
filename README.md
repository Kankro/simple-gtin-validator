# simple-gtin-validator

Lightweight PHP library for validating GTIN (Global Trade Item Number) values and generating check digits.

GTIN is the umbrella format behind common barcode identifiers such as GTIN-8, UPC, EAN, and GTIN-14. This package normalizes the input, applies prefix filtering, and verifies the checksum used by the GTIN standard.

## Contents

- [Requirements](#requirements)
- [Installation](#installation)
- [What the library does](#what-the-library-does)
- [Quick start](#quick-start)
- [Working flow](#working-flow)
- [API reference](#api-reference)
- [Usage examples](#usage-examples)
- [Prefix filtering](#prefix-filtering)
- [Development workflow](#development-workflow)
- [Limitations](#limitations)
- [License](#license)
- [Credits](#credits)

## Requirements

- PHP 7.4 (PHP 8+ not yet supported)
- Composer for installation and autoloading

## Installation

Install the package with Composer:

```bash
composer require kankro/simple-gtin-validator
```

Then load Composer's autoloader in your project:

```php
require __DIR__ . '/vendor/autoload.php';
```

## What the library does

The `GtinValidator` class provides two public operations:

- `isValidGtin($code)` validates a GTIN-style value
- `addCheckDigit($code)` appends a GTIN check digit to an incomplete code

Supported GTIN lengths for validation:

- GTIN-8
- GTIN-12
- GTIN-13
- GTIN-14

Accepted input styles:

- Numeric strings such as `'036000291452'` (recommended)
- Strings containing hyphens such as `'978-0-552-13326-5'`
- Numeric values such as `884571375091` (convenience only; use them only when you are sure there are no leading zeroes and the value stays within `PHP_INT_MAX` on your platform)

> For best results, always pass GTINs as strings. Numeric types can drop leading zeroes or be reformatted in ways that change the value being validated.

## Quick start

```php
<?php

require __DIR__ . '/vendor/autoload.php';

use Kankro\SimpleGtinValidator\GtinValidator;

$validator = new GtinValidator();

var_dump($validator->isValidGtin('884571375091'));      // true
var_dump($validator->isValidGtin('9780552133265'));     // false
var_dump($validator->addCheckDigit('1234567890123'));   // "12345678901231"
```

## Working flow

### Validation flow

When you call `isValidGtin($code)`, the validator works in this order:

1. **Normalize the input**
   - Numeric values are converted to strings
   - String inputs are trimmed and hyphens are removed
2. **Check the prefix**
   - The validator rejects codes that begin with configured restricted prefixes
3. **Validate format**
   - The code must be numeric
   - The length must be 8, 12, 13, or 14 digits
4. **Verify the check digit**
   - The value is left-padded to 14 digits
   - The GTIN checksum is calculated and compared with the last digit

### Check digit generation flow

When you call `addCheckDigit($code)`, the validator:

1. Normalizes the input
2. Left-pads the value to 13 digits
3. Calculates the GTIN checksum
4. Returns the normalized value with the new check digit appended

This means short inputs are padded with leading zeroes before the check digit is generated.

## API reference

### `isValidGtin($code): bool`

Validates a GTIN code after normalization, prefix checks, and checksum verification.

**Parameters**

- `$code`: string or numeric scalar input (other types are not supported)

**Returns**

- `true` when the code passes prefix, length, numeric, and checksum validation
- `false` when the code is not acceptable after normalization, prefix filtering, and checksum verification

**Throws**

- An exception or fatal error if `$code` is neither a string nor a numeric value (this occurs during input normalization)

### `addCheckDigit($code): string`

Generates and appends a GTIN check digit.

**Parameters**

- `$code`: string or numeric input representing a code without its final check digit

**Returns**

- A string with the computed check digit appended
- The input is padded on the left to 13 digits before the result is produced

## Usage examples

### Validate a standard GTIN

```php
use Kankro\SimpleGtinValidator\GtinValidator;

$validator = new GtinValidator();

var_dump($validator->isValidGtin('036000291452')); // true
var_dump($validator->isValidGtin('042100005264')); // true
```

### Validate input with hyphens

```php
var_dump($validator->isValidGtin('978-0-552-13326-5')); // false
```

Hyphens are removed before validation, so this behaves the same as validating `9780552133265`.

### Understand a rejected code

```php
var_dump($validator->isValidGtin('9780552133265')); // false
```

This value is mathematically well-formed as an ISBN-style identifier, but the default prefix filter rejects `978` and `979`.

### Generate a check digit

```php
var_dump($validator->addCheckDigit('1234567890123')); // "12345678901231"
var_dump($validator->addCheckDigit('1234567'));       // "00000012345670"
```

## Prefix filtering

The validator includes a public static prefix list that is checked before checksum validation:

```php
\Kankro\SimpleGtinValidator\GtinValidator::$gtinPrefixList;
```

By default, the list excludes these prefixes:

- `200-299`
- `951`
- `952`
- `960-969`
- `977`
- `978-979`
- `980`
- `981-984`
- `99`

This is important because some values may have a valid checksum but still return `false` due to business rules in the prefix list.

If your use case needs different behavior, you can adjust the static list before validating:

```php
use Kankro\SimpleGtinValidator\GtinValidator;

GtinValidator::$gtinPrefixList = [
    [200, 299],
    951,
    952,
];
```

## Development workflow

This repository is intentionally minimal and currently does not include a dedicated automated test suite.

Useful local checks:

```bash
composer validate --no-check-publish
php -l src/GtinValidator.php
```

Typical workflow for local changes:

1. Install dependencies with Composer if needed
2. Update the source or documentation
3. Run `composer validate --no-check-publish`
4. Run `php -l src/GtinValidator.php`
5. Manually verify usage with a small PHP script or `php -r`

## Limitations

- Input should be a string or numeric value
- Validation behavior depends on the configured prefix list, not only on checksum correctness
- `addCheckDigit()` left-pads inputs shorter than 13 digits before appending the final digit; inputs longer than 13 digits are not truncated, so the returned value can exceed 14 digits
- The package is focused on simple validation logic and does not provide barcode parsing or formatting helpers

## License

Apache-2.0

## Credits

- Original concept adapted from the Python version by charithe:
  https://github.com/charithe/gtin-validator
- GTIN and prefix reference material:
  - https://www.gs1.org/barcodes/technical/idkeys/gtin
  - https://www.gs1.org/standards/id-keys/company-prefix
