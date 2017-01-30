"use strict";

import Repository from "Repositories/Repository.ts";

export default class AddonRepo extends Repository {

    private static _entity : string = 'addon';

    constructor() {
        super(AddonRepo.entity);
    }

    static get entity() : string {
        return this._entity;
    }

}
