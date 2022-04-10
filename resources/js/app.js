require('./bootstrap')

import { createApp } from 'vue'
import { createStore } from 'vuex'

// Create a new store instance.
const store = createStore({
  state () {
    return {
      current_file: null
    }
  },
  mutations: {
    changeCurrentFile (state, payload) {
      state.current_file = payload
    }
  }
})

import Home from './components/HomeComponent'

import Toaster from '@meforma/vue-toaster'

const app = createApp({})

app.use(store)
app.use(Toaster)

app.component('home', Home)

app.mount('#app')