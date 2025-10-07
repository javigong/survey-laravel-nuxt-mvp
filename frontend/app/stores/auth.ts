import { defineStore } from 'pinia'

export interface User {
  id: number
  name: string
  email: string
  email_verified_at: string | null
  created_at: string
  updated_at: string
}

export interface AuthState {
  user: User | null
  token: string | null
  isAuthenticated: boolean
}

export const useAuthStore = defineStore('auth', {
  state: (): AuthState => ({
    user: null,
    token: null,
    isAuthenticated: false,
  }),

  getters: {
    getUser: (state) => state.user,
    getToken: (state) => state.token,
    isLoggedIn: (state) => state.isAuthenticated,
  },

  actions: {
    setUser(user: User) {
      this.user = user
      this.isAuthenticated = true
    },

    setToken(token: string) {
      this.token = token
      // Store token in localStorage for persistence
      if (process.client) {
        localStorage.setItem('auth_token', token)
      }
    },

    login(user: User, token: string) {
      this.setUser(user)
      this.setToken(token)
    },

    logout() {
      this.user = null
      this.token = null
      this.isAuthenticated = false

      // Remove token from localStorage
      if (process.client) {
        localStorage.removeItem('auth_token')
      }
    },

    initializeAuth() {
      // Initialize auth state from localStorage on app start
      if (process.client) {
        const token = localStorage.getItem('auth_token')
        if (token) {
          this.token = token
          this.isAuthenticated = true
          // TODO: Fetch user data from API if needed
        }
      } else {
        // During SSR, check if we have a token in the request
        // This is a simplified approach - in a real app you'd want to verify the token
        this.isAuthenticated = !!this.token
      }
    },
  },
})
