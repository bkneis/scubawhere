import { Injectable } from '@angular/core';
import {Http, Response, Headers, RequestOptions} from '@angular/http';

import { Observable } from 'rxjs/Observable';
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/map';
import 'rxjs/add/observable/throw';

@Injectable()
export class AuthService {

    private loginUrl = '/api/login';
    private logoutUrl = '/api/logout';

    constructor (private http: Http) {}

    login(username: string, password: string): Observable<any>
    {
        //let headers = new Headers({'Content-type': 'application/json'});
        //let options = new RequestOptions({headers});
        //noinspection TypeScriptValidateTypes
        return this.http
            .post(this.loginUrl, {username, password, _token: ''}/*, options*/)
            .map(this.extractData)
            .catch(this.handleError);
    }

    private extractData(res: Response): Object
    {
        let body = res.json();
        return body;
    }

    private handleError(error: Response | any)
    {
        let errMsg: string;
        let errors: Array<string>;
        if (error instanceof Response) {
            const body = error.json();
            errors = body.errors;
            console.log(errors);
        }
        return Observable.throw(errors[0]);
    }

}