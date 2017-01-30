"use strict";

import Event from 'Services/Event.ts';

const defaultWidth: string = '50%';

/**
 * Class with static helper methods to submit alert events
 * to the App component to display a bootstrap alert
 */
export default class Alert {
    
    static success(text: string, width: string = defaultWidth) {
        Event.fire('alert', 'success', text, width);
    }
    
    static error(text: string, width: string = defaultWidth) {
        Event.fire('alert', 'danger', text, width);
    }
    
    static info(text: string, width: string = defaultWidth) {
        Event.fire('alert', 'info', text, width);
    }

    static warn(text: string, width: string = defaultWidth) {
        Event.fire('alert', 'warning', text, width);
    }
    
}