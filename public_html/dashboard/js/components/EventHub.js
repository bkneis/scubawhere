/**
 * This empty vue instance acts as an event bus to commincte between
 * components. Events are emitted to this global vue instance, and then
 * accessed via any opther vue instance subscribing to this instance.
 *
 * For example
 *
 * Component A :
 * eventHub.$emit('add-user')
 *
 * Component B :
 * evenntHub.$on('add-user', function () { // do stuff } );
 *
 * More info : https://vuejs.org/v2/guide/migration.html#dispatch-and-broadcast-replaced
 */

var eventHub = new Vue();