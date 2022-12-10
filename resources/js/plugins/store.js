import Vue from 'vue'
import Vuex from 'vuex'
import VuexPersistence from 'vuex-persist'

Vue.use(Vuex)

const vuexLocal = new VuexPersistence({
  key: 'activos',
  storage: window.localStorage,
})

export default new Vuex.Store({
  state: {
    loading: false,
    loggedIn: false,
    acccessToken: '',
    tokenType: '',
    user: {},
    role: {},
    store: {},
    permissions: [],
  },
  getters: {
    loading(state) {
      return state.loading
    },
    isLoggedIn(state) {
      return state.loggedIn
    },
    token(state) {
      return {
        value: state.acccessToken,
        type: state.tokenType
      }
    },
    user(state) {
      return {
        ...state.user,
        permissions: state.permissions,
      }
    },
    role(state) {
      return state.role
    },
    store(state) {
      return state.store
    },
  },
  mutations: {
    loading(state, data) {
      state.loading = data
    },
    login(state, data) {
      Vue.prototype.$http.defaults.headers.common['Authorization'] = `${data.token_type} ${data.access_token}`
      state.acccessToken = data.access_token
      state.tokenType = data.token_type
      state.user = data.user
      state.role = data.role
      state.store = data.store
      state.permissions = data.permissions
      state.loggedIn = true
    },
    logout(state) {
      state.acccessToken = ''
      state.tokenType = ''
      state.user = {}
      state.role = {}
      state.store = {}
      state.permissions = []
      state.loggedIn = false
    },
  },
  actions: {
    loading({commit}, data) {
      commit('loading', data)
    },
    login({commit}, data) {
      return new Promise(async (resolve, reject) => {
        try {
          let response = await axios.post('auth', data)
          commit('login', response.data.payload)
          resolve(response)
        } catch(error) {
          commit('logout')
          reject(error)
        }
      })
    },
    changeStore({commit}, data) {
      return new Promise(async (resolve, reject) => {
        try {
          let response = await axios.patch('auth', data)
          commit('logout')
          commit('login', response.data.payload)
          resolve(response)
        } catch(error) {
          commit('logout')
          reject(error)
        }
      })
    },
    logout({commit}) {
      return new Promise(async (resolve, reject) => {
        try {
          let response = await axios.post('logout')
          commit('logout')
          resolve(response)
        } catch(error) {
          commit('logout')
          reject(error)
        }
      })
    },
  },
  plugins: [vuexLocal.plugin],
})
