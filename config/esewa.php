<?php

/*
 * eSewa credentials belong in the web-server environment, never in source
 * control. The documented sandbox credentials below make local testing work;
 * set ESEWA_ENV=live plus your merchant credentials before accepting payments.
 */
return [
    'environment' => getenv('ESEWA_ENV') ?: 'test',
    'product_code' => getenv('ESEWA_PRODUCT_CODE') ?: 'EPAYTEST',
    'secret_key' => getenv('ESEWA_SECRET_KEY') ?: '8gBm/:&EnhH.1/q',
];
