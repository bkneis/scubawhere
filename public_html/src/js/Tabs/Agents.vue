<template>
    <management-tab v-if="promises.agents"
                    model="Agents"
                    :action="form.action"
                    @create="form.create()">

        <ul slot="entity-list" class="entity-list">

            <li v-for="agent in agents"
                :value="agent.id"
                @click="form.update(agent)">
                <strong>{{ agent.name }}</strong> | {{ agent.branch_name }}
            </li>

        </ul>

        <entity-form slot="entity-form"
                     :form="form"
                     @create="onCreate"
                     @update="onUpdate"
                     @delete="onDelete">

            <in-text label="Agent Name:" v-model="form.name" :required="true"/>
            <in-text label="Agent's Website:" v-model="form.website"/>
            <in-text label="Branch Name:" v-model="form.branch_name" :required="true"/>
            <in-editor label="Branch Address:" v-model="form.branch_address" :required="true"/>
            <in-text label="Branch Telephone:" v-model="form.branch_telephone"/>
            <in-text label="Branch Email:" v-model="form.email"/>
            <in-check label="Does the agent have different billing details?" v-model="hasDifferentBillingDetails"/>
            <div class="form-row">
                <label>
                    <input type="checkbox" v-model="hasDifferentBillingDetails">
                    Does the agent have different billing details?
                </label>
            </div>
            <div v-if="hasDifferentBillingDetails" class="dashed-border">
                <h3>Billing Information</h3>
            </div>
            <in-number label="Commission (%):" v-model="form.commission" :required="true"/>
            <in-select label="Terms of business with the agent:" :options="businessTerms"/>

        </entity-form>

    </management-tab>
</template>

<script type="text/babel">
    import Form from 'Services/EntityForm.ts';
    import FormErrors from 'Services/FormErrors.ts';
    import ManagementTab from 'Mixins/ManagementTab.ts';

    export default {
        mixins : [ ManagementTab('agents') ],
        data() {
            return {
                hasDifferentBillingDetails: false,
                businessTerms: [
                    { name: 'The agent recieves the full amount of the booking, then you invoice them' },
                    { name: 'The agent only takes the deposit amount, then the rest is paid to you' },
                    { name: 'The agent is banned from making any bookings for you' }
                ],
                form : new Form({
                    resource : 'agent',
                    action   : 'Create',
                    errors   : new FormErrors(),
                    data     : {
                        id             : null,
                        name           : '',
                        website        : '',
                        branch_name    : '',
                        branch_address : '',
                        branch_phone   : '',
                        branch_email   : '',
                        billing_address: '',
                        commission     : 0
                    }
                })
            }
        }
    }
</script>
