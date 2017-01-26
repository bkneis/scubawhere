"use strict";

import { IFormErrors } from 'Contracts/IForm.ts';

export default class FormErrors implements IFormErrors {
    
    errors : any;

    constructor() 
    {
        this.errors = {};
    }

    get(field : string) : any
    {
        if (this.errors[field]) {
            return this.errors[field][0];
        }
    }

    any() : boolean 
    {
        return Object.keys(this.errors).length > 0;
    }

    clear(field ?: string) : void 
    {
        if (field) {
            delete this.errors[field];
            return;
        }
        this.errors = {};
    }

    has(field : string) : boolean
    {
        return this.errors.hasOwnProperty(field);
    }

    all() : any
    {
        return this.errors;
    }

    record(errors : Array<string>) : void 
    {
        this.errors = errors;
    }

}
