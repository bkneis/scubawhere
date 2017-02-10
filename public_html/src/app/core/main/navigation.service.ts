import { Injectable } from '@angular/core';
import {EventEmitter} from "events";

@Injectable()
export class NavigationService {
  
  constructor(private emitter: EventEmitter) { }
  
  fire(event: string, ...args: Array<any>): void
  {
    this.emitter.emit(event, args);
  }
  
  listen(event: string, callback: () => void): void
  {
    this.emitter.on(event, callback);
  }

}
