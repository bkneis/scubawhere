<template>
    <div class="form-row">
        <label v-if="label" class="field-label">{{label}}</label>
        <div v-for="(price, index) in value" class="row">
            <div class="col-md-3">
                <div class="input-group">
                    <span class="input-group-addon">
                        <span>{{currency}}</span>
                    </span>
                    <input type="number"
                           min="0"
                           step="0.01"
                           v-model="price.decimal_price"
                           @input="updateValue"
                           :class="{ 'requiredPriceInput' : isRequired }"
                           style="width: 100%"
                           title=""/>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <date-time label="from :" v-model="price.from" :options="datePickerOptions"/>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <date-time label="until :" v-model="price.until" :options="datePickerOptions"/>
                </div>
            </div>
            <div class="col-md-1">
                <button class="btn btn-danger remove-price" @click.prevent="removePrice(index)">X</button>
            </div>
        </div>
        <button class="btn btn-default btn-sm" @click.prevent="addPrice">Add another price change</button>
    </div>
</template>
<script type="text/babel">
    import config from 'config.ts';
    import DateTime from 'vue2-datetime-picker/src';
    import PriceService from 'Services/PriceService.ts';

    export default {
        components: { DateTime },
        props: {
            label: {},
            value: {
                default: () => [ PriceService.emptyPrice() ]
            },
            required: {}
        },
        data() {
            return {
                currency: window.company.currency.symbol,
                datePickerOptions: {format: config.DATE_FORMAT}
            }
        },
        methods: {
            removePrice(index) {
                if (this.value.length > 1) {
                    this.value.splice(index, 1);
                }
            },
            addPrice() {
                this.value.push(PriceService.emptyPrice());
            },
            updateValue() {
                this.$emit('input', this.value);
            }
        },
        computed: {
            // @todo Change this to use this.$el.querySelector on inputs
            isRequired() {
                return this.required && (this.value.length > 0);
            }
        }
    }
</script>
