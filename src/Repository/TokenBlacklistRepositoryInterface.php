<?php

namespace Antarctica\LaravelTokenBlacklist\Repository;

use Antarctica\LaravelBaseRepositories\Repository\BaseRepositoryInterface;

interface TokenBlacklistRepositoryInterface extends BaseRepositoryInterface {

    /**
     * @param string $token
     * @return mixed
     */
    public function check($token);

    /**
     * @param string $token
     * @return array
     */
    public function findByToken($token);

    /**
     * @return array
     */
    public function findAllExpired();

    /**
     * @return mixed
     */
    public function deleteAllExpired();
}