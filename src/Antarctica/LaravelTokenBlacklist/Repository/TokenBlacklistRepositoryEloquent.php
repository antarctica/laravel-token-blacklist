<?php

namespace Antarctica\LaravelTokenBlacklist\Repository;

use Antarctica\LaravelBaseRepositories\Repository\BaseRepositoryEloquent;
use Antarctica\LaravelBaseExceptions\Exception\InvalidArgumentTypeException;

use BlacklistedToken;

use Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Antarctica\LaravelTokenAuth\Exception\Token\BlacklistedTokenException;
use Antarctica\LaravelTokenBlacklist\Exception\ExpiredTokenException;
use Antarctica\LaravelTokenBlacklist\Exception\BlacklistDuplicate;
use Antarctica\LaravelTokenBlacklist\Exception\BlacklistFault;
use Antarctica\LaravelTokenAuth\Service\Token\TokenServiceInterface;

class TokenBlacklistRepositoryEloquent extends BaseRepositoryEloquent implements TokenBlacklistRepositoryInterface {

    /**
     * @var BlacklistedToken
     */
    protected $model;
    /**
     * @var TokenServiceInterface
     */
    private $Token;

    /**
     * @param BlacklistedToken $model
     * @param TokenServiceInterface $Token
     */
    function __construct(BlacklistedToken $model, TokenServiceInterface $Token)
    {
        $this->model = $model;
        $this->Token = $Token;
    }

    /**
     * @param array $attributes
     * @throws BlacklistDuplicate
     * @throws BlacklistFault
     * @throws \Antarctica\LaravelTokenAuth\Exception\Token\ExpiredTokenException
     * @return array
     */
    public function create(array $attributes)
    {
        $token = $attributes['token'];

        $blacklistedToken = [
            'user_id' => $this->Token->getSubject($token),
            'token' => $token,
            'expiry' => Carbon::createFromTimeStamp($this->Token->getExpiry($token))
        ];

        try
        {
            // This will raise an expired token exception if the token has expired (no point blacklisting something that won't work anyway)
            $this->Token->getExpiry($token);

            // Tokens can only be blacklisted once, so if a token is already in the database we should return an error.
            $this->findByToken($token);
            throw new BlacklistDuplicate();
        }
        catch (ExpiredTokenException $exception)
        {
            throw new \Antarctica\LaravelTokenAuth\Exception\Token\ExpiredTokenException();
        }
        catch (ModelNotFoundException $exception)
        {
            // In this case we *want* this exception to the thrown, but to ignore its usual significance and carry on.
        }

        try
        {
            $result = $this->model->create($blacklistedToken);

            return $this->export($result);
        }
        catch (QueryException $exception)
        {
            throw new BlacklistFault('Unable to blacklist token.');
        }
    }

    /**
     * Check if a given token is considered blacklisted
     *
     * TODO: Use export() method
     *
     * @param $token
     * @return bool
     * @throws BlacklistedTokenException
     */
    public function check($token)
    {
        $blacklistedToken = $this->model
            ->where('user_id', $this->Token->getSubject($token))
            ->where('expiry', '>', Carbon::now())
            ->whereToken($token)
            ->first();

        if ($blacklistedToken === null) {
            return true;
        }

        throw new BlacklistedTokenException();
    }

    /**
     * Try to find a blacklisted token entity by its associated token
     *
     * TODO: Use export() method
     *
     * @param string $token
     * @return array
     */
    public function findByToken($token)
    {
        $blacklistedToken = $this->model->whereToken($token)->first();

        if ($blacklistedToken === null)
        {
            throw new ModelNotFoundException();
        }

        return $blacklistedToken->toArray();
    }

    /**
     * Find all blacklisted tokens that have naturally expired, and so no longer need to be tracked
     *
     * TODO: Use export() method
     *
     * Returns any blacklisted tokens that will have expired naturally and so no longer need to be considered.
     *
     * @return array
     */
    public function findAllExpired()
    {
        $expiredBlacklistedTokens = $this->model->where('expiry', '<', Carbon::now())->get();

        return $expiredBlacklistedTokens->toArray();
    }

    /**
     * Delete all blacklisted token entities where the associated token has naturally expired, and so no longer needs to be tracked
     */
    public function deleteAllExpired()
    {
        $expiredBlacklistedTokens = $this->findAllExpired();

        foreach ($expiredBlacklistedTokens as $expiredBlacklistedToken)
        {
            $this->delete($expiredBlacklistedToken['id']);
        }
    }

    /**
     * Converts results into a common array format
     *
     * TODO: Replace with improved version when ready.
     *
     * @param $resultSet
     * @return array
     * @throws InvalidArgumentTypeException
     */
    protected function export($resultSet)
    {
        if (is_object($resultSet))
        {
            // Exporting depends on what needs exporting (i.e. what class are we exporting)
            switch (get_class($resultSet)) {
                case 'Illuminate\Database\Eloquent\Collection':

                    return $this->exportEloquentCollection($resultSet);
                    break;

                case 'BlacklistedToken':

                    return $this->exportBlacklistedToken($resultSet);
                    break;
            }
        }

        if (is_array($resultSet))
        {
            // Arrays are already in the right format but need to be wrapped in a data key

            return [
                'data' => $resultSet
            ];
        }

        throw new InvalidArgumentTypeException(
            $argumentName = 'repository_export',
            $valueOfCorrectArgumentType = [],
            $argumentValue = $resultSet
        );
    }

    /**
     * Export a Blacklisted Token
     *
     * @param BlacklistedToken $resultSet
     * @return array
     */
    protected function exportBlacklistedToken(BlacklistedToken $resultSet)
    {
        return [
            'data' => $resultSet->toArray()
        ];
    }
}