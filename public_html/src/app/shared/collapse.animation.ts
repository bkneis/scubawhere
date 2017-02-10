import {style, state, animate, transition} from "@angular/core";

const collapse = [
    state('true', style({
        height: '100%',
        opacity: 1
    })),
    state('false', style({
        height: '0',
        opacity: 0
    })),
    transition('0 => 1', animate('200ms')),
    transition('1 => 0', animate('100ms'))
];

export default collapse;
