<?php

use Scubawhere\Repositories\LanguageRepoInterface;
use Scubawhere\Exceptions\Http\HttpUnprocessableEntity;

class LanguageController extends Controller
{
    protected $language_repo;

    public function __construct(LanguageRepoInterface $language_repo)
    {
        $this->language_repo = $language_repo;
    }

    public function index()
    {
        return $this->language_repo->all();
    }

    public function show($id)
    {
        if(!$id) {
            throw new HttpUnprocessableEntity(__CLASS__.__METHOD__, ['The id field is required']);
        }
        return $this->language_repo->get($id);
    }
}