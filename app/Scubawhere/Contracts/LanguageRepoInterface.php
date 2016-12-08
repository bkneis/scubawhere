<?php
namespace Scubawhere\Contracts;

interface LanguageRepoInterface
{
    public function get($id, array $relations = [], $fail = true);

    public function all(array $relations = []);
}