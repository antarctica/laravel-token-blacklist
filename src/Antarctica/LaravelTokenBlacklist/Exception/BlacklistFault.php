<?php

namespace Antarctica\LaravelTokenBlacklist\Exception;

use Antarctica\LaravelBaseExceptions\Exception\HttpException;

class BlacklistFault extends HttpException
{
    protected $kind = 'unknown_blacklist_fault';

    protected $resolution = 'Try again, or wait for the token to expire naturally. If needed, contact the API maintainer for assistance.';

    protected $resolutionURLs = ['mailto:basweb@bas.ac.uk'];
}
