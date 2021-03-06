<?php

namespace Scubawhere\Repositories;

use Scubawhere\Context;
use Scubawhere\Entities\User;
use Scubawhere\Exceptions\Http\HttpUnauthorized;
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
        $users = User::onlyOwners()->get();
        $current_user_id = \Auth::user()->id;
        foreach ($users as $user) {
            if($user->id === $current_user_id) {
                $user->active = true;
                break;
            }
        }
        return $users;
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

    public function update($data)
    {
        $user = \Auth::user();

        /*if($id !== $user->id) {
            throw new HttpUnauthorized(__CLASS__.__METHOD__, ['Unauthroized. You cannot update a user that is not you']);
        }*/

        $user->fill($data);

        $validator = \Validator::make($data, User::rules($user->id));
        if($validator->fails()) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, $validator->errors()->all());
        }

        $user->save();

        return $user;
    }

}