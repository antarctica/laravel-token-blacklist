<?php

namespace Antarctica\LaravelTokenBlacklist\Exception;

use Antarctica\LaravelBaseExceptions\Exception\HttpException;

class BlacklistDuplicate extends HttpException
{
    protected $kind = 'blacklist_duplicate';

    protected $details = [
        "blacklist_error" => [
            "This authentication token has already been blacklisted."
        ]
    ];

    protected $resolution = 'Do not do this again.';
};
