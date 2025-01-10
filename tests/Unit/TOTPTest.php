<?php

use OTPHP\{Factory, OTPInterface, TOTP, TOTPInterface};
use OTPHP\InternalClock;

test('that true is true', function () {
    $otp = TOTP::createFromSecret('JDDK4U6G3BJLEZ7Y', new InternalClock());
    $otp->setPeriod(20);
    $otp->setDigest('sha512');
    $otp->setDigits(8);
    $otp->setEpoch(100);
    $otp->setLabel('alice@foo.bar');
    $otp->setIssuer('My Project');
    $otp->setParameter('foo', 'bar.baz');

    expect('otpauth://totp/My%20Project%3Aalice%40foo.bar?algorithm=sha512&digits=8&epoch=100&foo=bar.baz&issuer=My%20Project&period=20&secret=JDDK4U6G3BJLEZ7Y')
        ->toBe($otp->getProvisioningUri());
});

test('classic otp challenge and verify',  function () {
    $secret = null;
    $period = 600;
    $digits = 4;
    $label = config('app.name');

    $totp1 = TOTP::create(...compact('secret', 'period', 'digits'));
    $totp1->setLabel($label);
    $provisioning_url = $totp1->getProvisioningUri();
    $pin1 = $totp1->now();
    $pin2 = $totp1->now();

    $totp2 = Factory::loadFromProvisioningUri(($provisioning_url));
    expect($totp2->verify($pin1))->toBeTrue();
});
