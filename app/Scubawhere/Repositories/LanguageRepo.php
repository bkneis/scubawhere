<?php

namespace Scubawhere\Repositories;

use Scubawhere\Entities\Language;

class LanguageRepo implements LanguageRepoInterface
{
    public function get($id, array $relations = [], $fail = true)
    {
        $language = Language::where('id', '=', $id)->with($relations)->find($id);
        if(is_null($language) && $fail) {
            throw new HttpNotFound(__CLASS__. __METHOD__, ['The language could not be found']);
        }
        return $language;
    }

    public function all(array $relations = [])
    {
        return Language::with($relations)->get();
    }
}