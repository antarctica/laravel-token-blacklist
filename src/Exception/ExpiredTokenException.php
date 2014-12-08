<?php

namespace Antarctica\LaravelTokenBlacklist\Exception;

class ExpiredTokenException extends \Antarctica\LaravelTokenAuth\Exception\Token\ExpiredTokenException {

    protected $statusCode = 401;

    protected $kind = 'expired_authentication_token';

    protected $details = [
        "blacklist_error" => [
            "The authentication token given has expired and cannot be blacklisted (there would be no point)."
        ]
    ];

    protected $resolution = 'If you wanted to blacklist this token you do\'t need to do anything as this token can no longer be used.';
}
