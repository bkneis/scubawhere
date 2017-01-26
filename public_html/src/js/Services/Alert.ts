"use strict";

import * as Vue from 'vue';

const defaultWidth: string = '50%';

/**
 * Class with static helper methods to submit alert events
 * to the App component to display a bootstrap alert
 */
export default class Alert {
    
    static success(text: string, width: string = defaultWidth) {
        //noinspection TypeScriptUnresolvedVariable
        Vue.prototype.$bus.$emit('alert', 'success', text, width);
    }
    
    static error(text: string, width: string = defaultWidth) {
        //noinspection TypeScriptUnresolvedVariable
        Vue.prototype.$bus.$emit('alert', 'danger', text, width);
    }
    
    static info(text: string, width: string = defaultWidth) {
        //noinspection TypeScriptUnresolvedVariable
        Vue.prototype.$bus.$emit('alert', 'info', text, width);
    }

    static warn(text: string, width: string = defaultWidth) {
        //noinspection TypeScriptUnresolvedVariable
        Vue.prototype.$bus.$emit('alert', 'warning', text, width);
    }
    
}