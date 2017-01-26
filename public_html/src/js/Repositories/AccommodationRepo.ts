"use strict";

import Repository from "Repositories/Repository.ts";

export default class AccommodationRepo extends Repository {

    private static _entity : string = 'accommodation';

    constructor() {
        super(AccommodationRepo.entity);
    }
    
    static get entity() : string {
        return this._entity;
    }

}
