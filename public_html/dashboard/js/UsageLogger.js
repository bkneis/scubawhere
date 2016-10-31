/**
 * UsageLogger is a tool used to track and log user activity on a web applicaation.
 */
//use 'strict';

(function() {

    let tab;

    let usages;

    if(window.location.hash) {
        tab = window.location.hash.substring(1);
    } else {
        tab = window.location.href;
    }

    let store;

    // check if html5 local storage
    if (typeof(Storage) !== "undefined") {
        store = localstorage;
    } else {
        window.usageLogger = {};
        store = window.usageLogger;
    }

    setInterval(function() {
        let tab_now = store.getItem('tab');
        let time = 0;
        if(tab_now === tab) {
            time = parseInt(store.getItem('time')) + 10;
            store.setItem('time', time);
        } else {
            
        }
    }, 10);

});

