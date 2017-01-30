"use strict";

import * as Vue from 'vue';
import config from 'config.ts';
import IRepository from 'Contracts/IRepository.ts';

export default class Repository implements IRepository {
    
    public resource : any;
    public resourceName : string;

    constructor(resource : string)
    {
        this.resourceName = resource;
        //noinspection TypeScriptUnresolvedFunction
        this.resource = Vue.resource(config.BASE_API + '/' + resource);
    }
    
    get(id ?: number) : Promise<any>
    {
        if (id) {
            return new Promise((resolve, reject) => {
                this.resource.get({id: id})
                    .then(response => {
                        window[this.resourceName] = response.body;
                        resolve(response);
                    })
                    .catch(response => {
                        console.log(response);
                        reject(response);
                    });
            });
        } else {
            return new Promise((resolve, reject) => {
                this.resource.get()
                    .then(response => {
                        window[this.resourceName] = response.body;
                        resolve(response);
                    })
                    .catch(response => {
                        console.log(response);
                        reject(response);
                    });
            });
        }
    }
    
    save(id : number, data : any) : Promise<any>
    {
        return new Promise((resolve, reject) => {
            this.resource.save({id : id}, {item : data})
                .then(response => {
                    window[this.resourceName][id] = response.body.model;
                    resolve(response);
                })
                .catch(response => {
                    console.log(response);
                    reject(response);
                });
        });
    }

    update(id : number, data : any) : Promise<any>
    {
        return new Promise((resolve, reject) => {
            this.resource.update({id : id}, {item : data})
                .then(response => {
                    window[this.resourceName][id] = response.body.model;
                    resolve(response);
                })
                .catch(response => {
                    console.log(response);
                    reject(response);
                })
        });
    }
    
    delete(id : number) : Promise<any>
    {
        return new Promise((resolve, reject) => {
            this.resource.update({id : id})
                .then(response => {
                    delete window[this.resourceName][id];
                    resolve(response);
                })
                .catch(response => {
                    console.log(response);
                    reject(response);
                })
        });
    }
}