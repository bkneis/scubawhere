"use strict";

import config from 'config.ts';

/**
 * A class to handle client side logging.
 * 
 * This class is to manage the logging within the client side
 * applications. In development / beta users, config.APP_DEBUG will be set to
 * true, if so this will log the errors.
 * 
 * @todo Submit to sentry
 */
export default class Logger {
    
    public static log(...something: Array<any>): void
    {
        console.log(...something);
    }
    
    public static warn(...something: Array<any>): void
    {
        if (config.APP_DEBUG) {
            console.warn(...something);
        }
    }

    public static err(...something: Array<any>): void
    {
        if (config.APP_DEBUG) {
            console.log(...something);
        }
    }
    
}