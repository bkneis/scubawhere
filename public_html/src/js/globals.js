"use strict";

import Vue from 'vue';
import * as Alert from 'Components/Alert.vue';
import * as ManagementTab from 'Layouts/ManagementTab.vue';
import * as InText from 'Components/Forms/Inputs/InText.vue';
import * as EntityForm from 'Components/Forms/EntityForm.vue';
import * as InEditor from 'Components/Forms/Inputs/InEditor.vue';
import * as InNumber from 'Components/Forms/Inputs/InNumber.vue';
import * as InPrices from 'Components/Forms/Inputs/InPrices.vue';
import * as InSelect from 'Components/Forms/Inputs/InSelect.vue';
import * as InCheck from 'Components/Forms/Inputs/InCheck.vue';

Vue.component('alert', Alert);
Vue.component('in-text', InText);
Vue.component('in-editor', InEditor);
Vue.component('in-prices', InPrices);
Vue.component('in-number', InNumber);
Vue.component('in-select', InSelect);
Vue.component('entity-form', EntityForm);
Vue.component('management-tab', ManagementTab);
Vue.component('in-check', InCheck);