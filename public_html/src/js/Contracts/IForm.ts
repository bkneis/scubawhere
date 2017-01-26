"use strict";

export interface IFormErrors {
    errors : any;
    get(field : string) : string;
    any() : boolean;
    clear(field ?: string) : void;
    has(field : string) : boolean;
    all() : any;
    record(errors : Array<string>) : void;
}

export interface IFormOptions {
    data : any;
    resource : string;
    errors : IFormErrors;
    action : string;
}

export interface IForm {
    originalData : any;
    resource : any;
    errors : IFormErrors;
    action : string;
    reset() : void;
    data() : any;
    fill(data : any): void;
    submit() : Promise<any>;
    onSuccess(response : any) : void;
    onFailure(response : any) : void;
    create() : void;
    update(model : any) : void;
}
