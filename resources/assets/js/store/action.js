/*
* Action 
* This is the example for vuex to let Auth has state
* Understand?yes. but how can i check auth/ 
*/
import Vue from 'vue'
import mutations from './mutations.js'
import * as types from './mutations_type.js'

export const actionAuthChecked = ({ commit }) => {
  console.log('Action authChecked')
  commit(types.AUTHCHECKED)
}

export const setLanguage = ({ commit},payload ) => {
  console.log('Set Language')
  commit(types.SETLANGUAGE,payload)
}

export const setCurrency = ({ commit},payload ) => {
  console.log('Set Currency')
  commit(types.SETCURRENCY,payload)
}

export const setCurrencyRate = ({ commit},payload ) => {
  console.log('Set Currency Rate')
  commit(types.SETCURRENCYRATE,payload)
}

export const setUserIcon = ({ commit},payload ) => {
  console.log('Set User Icon')
  commit(types.SETUSERICON,payload)
}