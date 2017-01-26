"use strict";

interface IConfig {
    BASE_API : string,
    APP_DEBUG : boolean,
    SYNC : boolean,
    DATE_FORMAT : string
}

export default <IConfig> {
    BASE_API    : '/api',
    APP_DEBUG   : true,
    SYNC        : false,
    DATE_FORMAT : 'DD MM YYYY'
};
