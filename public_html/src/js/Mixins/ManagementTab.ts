"use strict";

import * as Vue from 'vue';
import * as _ from 'lodash';
import Tab from 'Mixins/Tab.ts';
import PriceService from 'Services/PriceService.ts';
import RepoFactory from 'Repositories/RepositoryFactory.ts';

/**
 * A vue mixin to be used within the management tabs.
 * 
 * Given an entity string, the mixin will perform create, update and
 * delete tasks using the API resource /api/{entity}. In addition, when
 * created it will retrieve all the models to display in the entity list.
 * 
 * Note: If the methods in this mixin need to be overridden, please
 * use @overload tag.
 * 
 * @param entities
 * @parent Tab
 * @returns {*}
 */
export default function(entities : string) : any
{
    return {
        // Inherit the parent mixin
        mixins : [ Tab() ],
        data() {
            let data = {
                promises : {}
            };
            data[entities] = [];
            data.promises[entities] = false;
            return data;
        },
        // Load all the entities into the data object, for example, if entities
        // is accommodations, it requests GET /api/accommodations and puts them
        // into this.accommodations.
        created() {
            let repo = RepoFactory.make(entities);
            repo.get()
                .then(response => {
                    this[entities] = _.keyBy(response.body, 'id');
                    this.promises[entities] = true;
                });
        },
        methods: {
            // Helper method to generate price range string shown in the entity list
            priceRange(model : any) {
                return PriceService.priceRange(model.prices);
            },
            /*
             * Event handler fired after the 'create' event from the EntityForm
             */
            onCreate(id : number) {
                this.form.submit()
                    .then(response => {
                        let entity = response.body.model;
                        //noinspection TypeScriptUnresolvedFunction
                        Vue.set(this[entities], entity.id, entity);
                    })
                    .catch(error => {
                        if (error.didNotSubmit) {
                            return;
                        }
                        console.log(error);
                    });
            },
            onUpdate(id : number) {
                this.form.submit()
                    .then(response => {
                        let entity = response.body.model;
                        //noinspection TypeScriptUnresolvedFunction
                        Vue.set(this[entities], entity.id, entity);
                    })
                    .catch(error => {
                        if (error.didNotSubmit) {
                            return;
                        }
                        console.log(error);
                    });
            },
            onDelete(id : number) {
                this.form.action = 'Delete';
                this.form.submit()
                    .then(response => {
                        //noinspection TypeScriptUnresolvedFunction
                        Vue.delete(this[entities], id);
                    })
                    .catch(error => {
                        if (error.didNotSubmit) {
                            return;
                        }
                        console.log(error);
                    });
            }
        }
    };
}
