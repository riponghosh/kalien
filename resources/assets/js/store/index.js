//Remember index's name cannot change.
import Vue from 'vue/dist/vue'
import Vuex from 'vuex'
import {state, mutations} from './mutations.js'
import * as getters from './getter.js'
import * as actions from './action.js'
Vue.use(Vuex);

export default new Vuex.Store({
	state,
	mutations,
	getters,
	actions,

	strict: true //this mode mean cannot modify the state directly go to the default.js
});
