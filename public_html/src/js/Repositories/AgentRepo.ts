"use strict";

import * as Vue from 'vue';
import Repository from "Repositories/Repository.ts";

export default class AgentRepo extends Repository {

    private static _entity : string = 'agent';

    constructor() {
        super(AgentRepo.entity);
    }

    static get entity() : string {
        return this._entity;
    }
    
    //@overload
    public get(): Promise<any>
    {
        return new Promise((resolve, reject) => {
            Vue.http.get('/api/agent/all')
                .then(response => {
                    resolve(response);
                }, response => {
                    reject(response);
                });
        });
    }

}
