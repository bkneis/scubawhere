<template>
    <management-tab v-if="promises.addons"
                    model="Addons"
                    :action="form.action"
                    @create="form.create()">

        <ul slot="entity-list" class="entity-list">

            <li v-for="addon in addons"
                :value="addon.id"
                @click="form.update(addon)">
                <strong>{{ addon.name }}</strong> | {{ priceRange(addon) }}
            </li>

        </ul>

        <entity-form slot="entity-form"
                     :form="form"
                     @create="onCreate"
                     @update="onUpdate"
                     @delete="onDelete">

            <in-text   label="Addon name :"
                       v-model="form.name"
                       :required="true"/>

            <in-editor label="Addon description :"
                       v-model="form.description"
                       :required="false"/>

            <in-prices label="Price per night :"
                       v-model="form.prices"
                       :required="true"/>

        </entity-form>

    </management-tab>
</template>

<script type="text/babel">
    import FormErrors from 'Services/FormErrors.ts';
    import Form from 'Services/EntityForm.ts';
    import ManagementTab from 'Mixins/ManagementTab.ts';
    import PriceService from 'Services/PriceService.ts';

    export default {
        mixins : [ ManagementTab('addons') ],
        data() {
            return {
                form : new Form({
                    resource : 'addon',
                    action   : 'Create',
                    errors   : new FormErrors(),
                    data     : {
                        id          : null,
                        name        : '',
                        description : '',
                        prices      : [ PriceService.emptyPrice() ]
                    }
                })
            }
        },
        methods: {
            // @overload
            priceRange() {
                return 'dsfsdf';
            }
        }
    }
</script>
