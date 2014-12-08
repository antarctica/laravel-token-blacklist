<?php

namespace Antarctica\LaravelTokenBlacklist\Repository;

use Antarctica\LaravelBaseRepositories\Repository\BaseRepositoryEloquent;
use BlacklistedToken;  // TODO: Include model somehow
use Carbon;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;

use Lions\Exception\Token\BlacklistedTokenException;  // TODO: Circular dependency(!)
use Lions\Exception\Token\ExpiredTokenException;  // TODO: Circular dependency(!)

use Antarctica\LaravelTokenBlacklist\Exception\BlacklistDuplicate;
use Antarctica\LaravelTokenBlacklist\Exception\BlacklistFault;

use Lions\Service\Token\TokenServiceInterface;  // TODO: Circular dependency(!)

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
     * @return array
     * @throws BlacklistDuplicate
     * @throws BlacklistFault
     * @throws \Lions\Exception\TokenBlacklist\ExpiredTokenException
     */
    public function create(array $attributes)
    {
        $token = $attributes['token'];

        $blacklistedToken = [
            'user_id' => $this->Token->getSubject($token),
            'token' => $token,
            'expiry' => Carbon::createFromTimeStamp($this->Token->getExpiry($token))
        ];

        try {
            // This will raise an expired token exception if the token has expired (no point blacklisting something that won't work anyway)
            $this->Token->getExpiry($token);

            // Tokens can only be blacklisted once, so if a token is already in the database we should return an error.
            $this->findByToken($token);
            throw new BlacklistDuplicate();
        }
        catch (ExpiredTokenException $exception)
        {
            throw new \Lions\Exception\TokenBlacklist\ExpiredTokenException();
        }
        catch (ModelNotFoundException $exception)
        {
            // In this case we *want* this exception to the thrown, but to ignore its usual significance and carry on.
        }

        try {
            // Call to standard (parent) create method
            parent::create($blacklistedToken);
        } catch (QueryException $exception) {
            throw new BlacklistFault('Unable to blacklist token.');
        }

        // Return value is not used in this case
        return [];
    }

    /**
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
     * Deletes any blacklisted tokens that will have expired naturally and so no longer need to be considered.
     */
    public function deleteAllExpired()
    {
        $expiredBlacklistedTokens = $this->findAllExpired();

        foreach ($expiredBlacklistedTokens as $expiredBlacklistedToken)
        {
            $this->delete($expiredBlacklistedToken['id']);
        }
    }
}