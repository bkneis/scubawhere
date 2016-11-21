<?php

namespace Scubawhere\Repositories;

use Scubawhere\Context;
use Scubawhere\Entities\User;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;


class UserRepo
{

    /**
     * Get all the users that belong to the company of the current context
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUsersInContext()
    {
        return User::onlyOwners()->get();
    }

    public function create($data, $password)
    {
        $password = \Hash::make($password);

        $user           = new User($data);
        $user->password = $password;

        if(!$user->validate()) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, $user->errors()->all());
        }

        Context::get()->users()->save($user);

        return $user;
    }

}