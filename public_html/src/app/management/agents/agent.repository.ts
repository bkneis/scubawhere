import { Injectable } from '@angular/core';
import {Http, Response} from "@angular/http";
import {Observable} from "rxjs/Rx";
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/map';
import 'rxjs/add/observable/throw';
import {Agent} from "./agent";

@Injectable()
export class AgentRepository {

    private url: string = '/api/agent/all';

    constructor(private http: Http) { }

    get(): Observable<Agent>
    {
        //noinspection TypeScriptValidateTypes
        return this.http.get(this.url)
            .map(this.extractData)
            .catch(this.handleError);
    }

    extractData(res: Response): Object
    {
        return res.json() || {};
    }

    handleError(res: Response | any)
    {
        return Observable.throw(res);
    }

}

export class MockAgentRepository {

    get() {
        return Observable.of([
            { name: 'Darth Vader', branch_name: 'Death star' },
            { name: 'Luke Skywalker', branch_name: 'Rebel base camp' }
        ]);
    }
}
