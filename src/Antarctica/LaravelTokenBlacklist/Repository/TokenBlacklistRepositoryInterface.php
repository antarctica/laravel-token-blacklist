<?php

namespace Antarctica\LaravelTokenBlacklist\Repository;

use Antarctica\LaravelBaseRepositories\Repository\BaseRepositoryInterface;

interface TokenBlacklistRepositoryInterface extends BaseRepositoryInterface {

    /**
     * Check if a given token is considered blacklisted
     *
     * @param string $token
     * @return mixed
     */
    public function check($token);

    /**
     * Try to find a blacklisted token entity by its associated token
     *
     * @param string $token
     * @return array
     */
    public function findByToken($token);

    /**
     * Find all blacklisted tokens that have naturally expired, and so no longer need to be tracked
     *
     * @return array
     */
    public function findAllExpired();

    /**
     * Delete all blacklisted token entities where the associated token has naturally expired, and so no longer needs to be tracked
     * @return mixed
     */
    public function deleteAllExpired();
}