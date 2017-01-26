"use strict";

interface IRepository {

    resource : any;
    resourceName : string;

    get(id ?: number) : Promise<any>
    save(id : number, data : any) : Promise<any>
    update(id : number, data : any) : Promise<any>
    delete(id : number) : Promise<any>

}

export default IRepository;