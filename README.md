# Omnipay: Gtpay

**Gtpay gateway for the Omnipay PHP payment processing library**


[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE.md)
[![Build Status][ico-build]][link-travis]
[![Coverage Status][ico-scrutinizer]][link-scrutinizer]
[![Quality Score][ico-code-quality]][link-code-quality]

[Omnipay](https://github.com/thephpleague/omnipay) is a framework agnostic, multi-gateway payment
processing library for PHP 5.3+. This package implements gtpay support for Omnipay.

## Install

Via Composer

``` bash
$ composer require davidumoh/omnipay-gtpay
```

## Usage

``` php

use Omnipay\Omnipay;

define('HASH_KEY','D3D1D05AFE42AD50818167EAC73C109168A0F108F32645C8B59E897FA930DA44F9230910DAC9E20641823799A107A02068F7BC0F4CC41D2952E249552255710F');

$gateway = Omnipay::create('Gtpay');

        $gateway->setMerchantId('17');
        $gateway->setHashKey(self::HASH_KEY);
        $gateway->setGatewayFirst('no');
        $gateway->setGatewayName(Gateway::GATEWAY_BANK);
        $gateway->setCurrency('NGN');


try {
    $formData = [
        'amount'=>70000.00,
        'items'=>[
            'tithe'=>[
                'label'=>'Tithes',
                'value'=>15000.00
            ],
            'seed'=>[
                'label'=>'Seed Offering',
                'value'=>25000.00
            ],
            'thanksgiving'=>[
                'label'=>'Thanksgiving Offering',
                'value'=>30000.00
            ]
        ],
        'notifyUrl'=>'http://payadmin.celz5.dev/transactions/notify',
        Data::GATEWAY_NAME=>Gateway::GATEWAY_WEBPAY,
        Data::TRANSACTION_MEMO=>'offerings',
        Data::CUSTOMER_NAME=>'Anastasia Umoh',
        Data::CUSTOMER_ID=>1,
        Data::TRANSACTION_ID=>$this->transactionId
        ];

    $response = $gateway->purchase($formData)->send();

    if ($response->isRedirect()) {
        $response->redirect;

    }
} catch (Exception $e) {
    $e->getMessage();
}
```


For general usage instructions, please see the main [Omnipay](https://github.com/thephpleague/omnipay) repository.

## Support

If you are having general issues with Omnipay, we suggest posting on
[Stack Overflow](http://stackoverflow.com/). Be sure to add the
[omnipay tag](http://stackoverflow.com/questions/tagged/omnipay) so it can be easily found.

If you want to keep up to date with release anouncements, discuss ideas for the project,
or ask more detailed questions, there is also a [mailing list](https://groups.google.com/forum/#!forum/omnipay) which
you can subscribe to.

If you believe you have found a bug, please report it using the [GitHub issue tracker](https://github.com/phronesis/Omnipay-Gtpay/issues),
or better yet, fork the library and submit a pull request.

## Testing

``` bash
$ composer test
```

## Security

If you discover any security related issues, please email umohdavid@gmail.com instead of using the issue tracker.

## Credits

- [David Umoh](https://github.com/phronesis)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

[ico-scrutinizer]: https://scrutinizer-ci.com/g/phronesis/Omnipay-Gtpay/badges/coverage.png
[ico-code-quality]: https://scrutinizer-ci.com/g/phronesis/Omnipay-Gtpay/badges/quality-score.png
[ico-version]: https://img.shields.io/packagist/v/davidumoh/omnipay-gtpay.svg?style=flat-square
[ico-build]: https://scrutinizer-ci.com/g/phronesis/Omnipay-Gtpay/badges/build.png
[ico-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/davidumoh/omnipay-gtpay
[link-travis]: https://travis-ci.org/phronesis/Omnipay-Gtpay
[link-scrutinizer]: https://scrutinizer-ci.com/g/phronesis/omnipay-gtpay/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/phronesis/omnipay-gtpay