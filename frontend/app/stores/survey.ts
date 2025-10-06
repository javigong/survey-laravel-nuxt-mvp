import { defineStore } from 'pinia'

export interface Survey {
  id: number
  user_id: number
  title: string
  description: string | null
  status: 'draft' | 'published' | 'closed'
  created_at: string
  updated_at: string
}

export interface SurveyState {
  surveys: Survey[]
  currentSurvey: Survey | null
  loading: boolean
  error: string | null
}

export const useSurveyStore = defineStore('survey', {
  state: (): SurveyState => ({
    surveys: [],
    currentSurvey: null,
    loading: false,
    error: null,
  }),

  getters: {
    getSurveys: (state) => state.surveys,
    getCurrentSurvey: (state) => state.currentSurvey,
    getSurveyById: (state) => (id: number) => state.surveys.find(survey => survey.id === id),
    isLoading: (state) => state.loading,
    getError: (state) => state.error,
  },

  actions: {
    setLoading(loading: boolean) {
      this.loading = loading
    },

    setError(error: string | null) {
      this.error = error
    },

    setSurveys(surveys: Survey[]) {
      this.surveys = surveys
    },

    addSurvey(survey: Survey) {
      this.surveys.unshift(survey) // Add to beginning of array
    },

    updateSurvey(updatedSurvey: Survey) {
      const index = this.surveys.findIndex(survey => survey.id === updatedSurvey.id)
      if (index !== -1) {
        this.surveys[index] = updatedSurvey
      }
    },

    removeSurvey(id: number) {
      this.surveys = this.surveys.filter(survey => survey.id !== id)
    },

    setCurrentSurvey(survey: Survey | null) {
      this.currentSurvey = survey
    },

    clearSurveys() {
      this.surveys = []
      this.currentSurvey = null
    },
  },
})
