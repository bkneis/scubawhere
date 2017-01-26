"use strict";

import * as Vue from 'vue';
import * as _ from 'lodash';
import config from 'config.ts';
import Alert from 'Services/Alert.ts';
import { IForm, IFormErrors, IFormOptions } from 'Contracts/IForm.ts';

export default class Form implements IForm {

    originalData : any;
    resource : any;
    errors : IFormErrors;
    action : string;
    
    constructor(options : IFormOptions) {
        // Clone the data object so we have a reference to the form's default values when resetting
        this.originalData = _.cloneDeep(options.data);
        
        // Create the vue resource to query to API
        //noinspection TypeScriptUnresolvedFunction
        this.resource = Vue.resource(config.BASE_API + '/' + options.resource + '{/id}');
        
        // Proxy the data fields through the form object so we can reference easier
        // e.g. form.name instead of form.data.name
        for (let field in options.data) {
            if (options.data.hasOwnProperty(field)) {
                this[field] = options.data[field];
            }
        }
        
        // Set the errors and action
        // The action is what determines if the form will POST, PUT or DELETE
        this.errors = options.errors;
        this.action = options.action;
    }

    /**
     * Reset all the form's data to clear the inputs
     *
     * We need to perform a deepClone on any non primitive variables,
     * otherwise we will change the un mutable default values.
     */
    reset() {
        this.errors.clear();
        for (let field in this.originalData) {
            if (this.originalData.hasOwnProperty(field)) {
                // We perform a check to ensure the field is not a primitive
                // as deepClone is an expensive operation
                if (typeof this.originalData[field] === 'object') {
                    this[field] = _.cloneDeep(this.originalData[field]);
                } else {
                    this[field] = this.originalData[field];
                }
            }
        }
    }

    /**
     * Return the data to be submitted by the form
     * @returns {Object} data
     */
    data() {
        let data = {
            id : null
        };
        for (let field in this.originalData) {
            if (this.originalData.hasOwnProperty(field)) {
                data[field] = this[field];
            }
        }
        return data;
    }

    /**
     * Replace the form's current data with a new model
     * @param data
     */
    fill(data : any) {
        for (let field in this.originalData) {
            if (this.originalData.hasOwnProperty(field)) {
                this[field] = data[field];
            }
        }
    }

    /**
     * Method to call on form submission
     * 
     * We need to determine which resource method to
     * use based on our action variable.
     * 
     * @note Could we only have the logic in the switch cases
     * adding data to an array then use the resource at the end
     * similar to this.resource[method](data) ??
     */
    submit() {
        let data = this.data();
        switch (this.action) {
            case ('Update'):
                return new Promise((resolve, reject) => {
                    this.resource.update({ id : data.id }, data)
                        .then(response => {
                            this.onSuccess(response);
                            resolve(response);
                        })
                        .catch(error => {
                            this.onFailure(error);
                            reject(error);
                        });
                });
            case ('Delete'):
                let confirmed = confirm('Are you sure you want to delete this accommodation? If you proceed then this accommodation will be deleted from any packages');
                if (confirmed) {
                    return new Promise((resolve, reject) => {
                        this.resource.delete({ id : data.id })
                            .then(response => {
                                this.onSuccess(response);
                                resolve(response);
                            })
                            .catch(error => {
                                this.onFailure(error);
                                reject(error);
                            });
                    });
                }
                return new Promise((resolve, reject) => {
                    let error = {
                        didNotSubmit : true
                    };
                    reject(error);
                });
            default:
                return new Promise((resolve, reject) => {
                    this.resource.save(data)
                        .then(response => {
                            this.onSuccess(response);
                            resolve(response);
                        })
                        .catch(error => {
                            this.onFailure(error);
                            reject(error);
                        });
                });
        }
    }

    /**
     * Callback for a successful form submission
     * @param response
     */
    onSuccess(response : any) {
        Alert.success(response.body.status);
        document.body.scrollTop = document.documentElement.scrollTop = 0;
        this.create();
    }

    /**
     * Callback for an unsuccessful form submission 
     * @param response
     */
    onFailure(response : any) {
        Alert.error('There were a few problems with the form');
        document.body.scrollTop = document.documentElement.scrollTop = 0;
        this.errors.record(response.body.errors);
    }

    /**
     * Update the form's action and clear its inputs
     */
    create() {
        this.action = 'Create';
        this.reset();
    }

    /**
     * Update the form's action and fill it with the model's data
     * @param model
     */
    update(model : any) {
        this.action = 'Update';
        this.fill(model);
    }

}