<template>
    <management-tab v-if="promises.accommodations"
                    model="Accommodation"
                    :action="form.action"
                    @create="form.create()">

        <ul slot="entity-list" class="entity-list">

            <li v-for="accommodation in accommodations"
                :value="accommodation.id"
                @click="form.update(accommodation)">
                <strong>{{ accommodation.name }}</strong> | {{ priceRange(accommodation) }}
            </li>

        </ul>

        <entity-form slot="entity-form"
                     :form="form"
                     @create="onCreate"
                     @update="onUpdate"
                     @delete="onDelete">

            <in-text   label="Accommodation name :"
                       v-model="form.name"
                       :required="true"/>

            <in-editor label="Accommodation description :"
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
        mixins : [ ManagementTab('accommodations') ],
        data() {
            return {
                form : new Form({
                    resource : 'accommodation',
                    action   : 'Create',
                    errors   : new FormErrors(),
                    data     : {
                        id          : null,
                        name        : '',
                        description : '',
                        capacity    : 1,
                        prices      : [ PriceService.emptyPrice() ]
                    }
                })
            }
        }
    }
</script>
