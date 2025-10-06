import type { Survey } from '~/stores/survey'

export interface CreateSurveyData {
  title: string
  description?: string
  status?: 'draft' | 'published' | 'closed'
}

export interface SurveyFilters {
  title?: string
  status?: string
}

export const useSurvey = () => {
  const config = useRuntimeConfig()
  const authStore = useAuthStore()

  const getHeaders = () => ({
    Authorization: `Bearer ${authStore.token}`,
    'Content-Type': 'application/json',
  })

  const fetchSurveys = async (filters?: SurveyFilters) => {
    try {
      const params = new URLSearchParams()

      if (filters?.title) params.append('filter[title]', filters.title)
      if (filters?.status) params.append('filter[status]', filters.status)

      const queryString = params.toString()
      const url = `${config.public.apiBase}/api/v1/surveys${queryString ? `?${queryString}` : ''}`

      const response = await $fetch<{
        data: Survey[]
        links: any
        meta: any
      }>(url, {
        headers: getHeaders(),
      })

      return response
    } catch (error: any) {
      throw new Error(error.data?.message || 'Failed to fetch surveys')
    }
  }

  const createSurvey = async (surveyData: CreateSurveyData) => {
    try {
      const response = await $fetch<{
        data: Survey
        message: string
      }>(`${config.public.apiBase}/api/v1/surveys`, {
        method: 'POST',
        headers: getHeaders(),
        body: surveyData,
      })

      return response.data
    } catch (error: any) {
      throw new Error(error.data?.message || 'Failed to create survey')
    }
  }

  const getSurvey = async (id: number) => {
    try {
      const response = await $fetch<{
        data: Survey
        message: string
      }>(`${config.public.apiBase}/api/v1/surveys/${id}`, {
        headers: getHeaders(),
      })

      return response.data
    } catch (error: any) {
      throw new Error(error.data?.message || 'Failed to fetch survey')
    }
  }

  const updateSurvey = async (id: number, surveyData: Partial<CreateSurveyData>) => {
    try {
      const response = await $fetch<{
        data: Survey
        message: string
      }>(`${config.public.apiBase}/api/v1/surveys/${id}`, {
        method: 'PUT',
        headers: getHeaders(),
        body: surveyData,
      })

      return response.data
    } catch (error: any) {
      throw new Error(error.data?.message || 'Failed to update survey')
    }
  }

  const deleteSurvey = async (id: number) => {
    try {
      await $fetch(`${config.public.apiBase}/api/v1/surveys/${id}`, {
        method: 'DELETE',
        headers: getHeaders(),
      })
    } catch (error: any) {
      throw new Error(error.data?.message || 'Failed to delete survey')
    }
  }

  return {
    fetchSurveys,
    createSurvey,
    getSurvey,
    updateSurvey,
    deleteSurvey,
  }
}
