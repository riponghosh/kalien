import Vue from 'vue'
import * as types from './mutations_type.js'

// state
export const state = {
	auth: false,
	storelanguage:'',
	currencyUnit:'',
	userIcon:'',
	currencyRate:Object
}

// mutations
export const mutations = {
	[types.AUTHCHECKED] (state) {
		state.auth = true
	},
	[types.SETLANGUAGE] (state,payload) {
		state.storelanguage=payload;	
	},
	[types.SETCURRENCY] (state,payload) {
		state.currencyUnit=payload;	
	},
	[types.SETCURRENCYRATE] (state,payload) {
		state.currencyRate=payload;	
	},
	[types.SETUSERICON] (state,payload) {
		state.userIcon=payload;	
	},
	changeLanguage(state,payload){
		state.storelanguage=payload;
	}
}