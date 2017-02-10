import { Injectable } from '@angular/core';
import {Http, Response} from "@angular/http";
import {Observable} from "rxjs/Rx";
import 'rxjs/add/operator/catch';
import 'rxjs/add/operator/map';
import 'rxjs/add/observable/throw';

@Injectable()
export class CompanyRepository {
  
  private url: string = '/api/company';

  constructor(private http: Http) { }
  
  get(): Observable<any>
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
