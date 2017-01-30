"use strict";

import * as Vue from 'vue';

/**
 * Wrapper class to use Laravel like interface for event handling.
 *
 * Note : Typescript complains about Vue.prototype.$bus not existing.
 * I believe this is because I am attaching it to the Vue prototype
 * in the main js file, and therefore its typing is not updated.
 */
export default class Event {
    
    public static listen(event: string, callback: Function): void
    {
        //noinspection TypeScriptUnresolvedVariable,TypeScriptValidateJSTypes
        Vue.prototype.$bus.on(event, callback);
    }
    
    public static fire(event: string, ...args: Array<any>): void
    {
        //noinspection TypeScriptUnresolvedVariable
        Vue.prototype.$bus.emit(event, args);
    }
    
}