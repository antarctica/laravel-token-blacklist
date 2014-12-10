<?php

use \Illuminate\Database\Eloquent\Model as Eloquent;

class BlacklistedToken extends Eloquent {

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = array('user_id', 'token', 'created_at', 'updated_at');

    /**
     * To protect against mass assignment attacks white-list some variables
     *
     * @var array
     */
    protected $fillable = array('user_id', 'token', 'expiry');

    /**
     * Relationship with User
     */
    public function user()
    {
        $this->belongsTo('User');
    }
}