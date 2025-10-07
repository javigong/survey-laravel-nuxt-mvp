import type { Survey, Question } from "../stores/survey";
import { useAuthStore } from "../stores/auth";
import { useRuntimeConfig } from "nuxt/app";
import { $fetch } from "ofetch";

export interface CreateSurveyData {
  title: string;
  description?: string;
  status?: "draft" | "published" | "closed";
}

export interface SurveyFilters {
  title?: string;
  status?: string;
}

export interface CreateQuestionData {
  survey_id: number;
  title: string;
  description?: string;
  type: Question["type"];
  options?: string[];
  validation_rules?: Record<string, any>;
  is_required?: boolean;
  order?: number;
}

export const useSurvey = () => {
  const config = useRuntimeConfig();
  const authStore = useAuthStore();

  const getHeaders = () => ({
    Authorization: `Bearer ${authStore.token}`,
    "Content-Type": "application/json",
  });

  const fetchSurveys = async (filters?: SurveyFilters) => {
    try {
      const params = new URLSearchParams();

      if (filters?.title) params.append("filter[title]", filters.title);
      if (filters?.status) params.append("filter[status]", filters.status);

      // Include questions in the response
      params.append("include", "questions");

      const queryString = params.toString();
      const url = `${config.public.apiBase}/api/v1/surveys${
        queryString ? `?${queryString}` : ""
      }`;

      const response = await $fetch<{
        data: Survey[];
        links: any;
        meta: any;
      }>(url, {
        headers: getHeaders(),
      });

      return response;
    } catch (error: any) {
      throw new Error(error.data?.message || "Failed to fetch surveys");
    }
  };

  const createSurvey = async (surveyData: CreateSurveyData) => {
    try {
      const response = await $fetch<{
        data: Survey;
        message: string;
      }>(`${config.public.apiBase}/api/v1/surveys`, {
        method: "POST",
        headers: getHeaders(),
        body: surveyData,
      });

      return response.data;
    } catch (error: any) {
      throw new Error(error.data?.message || "Failed to create survey");
    }
  };

  const getSurvey = async (id: number) => {
    try {
      const response = await $fetch<{
        data: Survey;
        message: string;
      }>(`${config.public.apiBase}/api/v1/surveys/${id}`, {
        headers: getHeaders(),
      });

      return response.data;
    } catch (error: any) {
      throw new Error(error.data?.message || "Failed to fetch survey");
    }
  };

  const updateSurvey = async (
    id: number,
    surveyData: Partial<CreateSurveyData>
  ) => {
    try {
      const response = await $fetch<{
        data: Survey;
        message: string;
      }>(`${config.public.apiBase}/api/v1/surveys/${id}`, {
        method: "PUT",
        headers: getHeaders(),
        body: surveyData,
      });

      return response.data;
    } catch (error: any) {
      throw new Error(error.data?.message || "Failed to update survey");
    }
  };

  const deleteSurvey = async (id: number) => {
    try {
      await $fetch(`${config.public.apiBase}/api/v1/surveys/${id}`, {
        method: "DELETE",
        headers: getHeaders(),
      });
    } catch (error: any) {
      throw new Error(error.data?.message || "Failed to delete survey");
    }
  };

  // Question management functions
  const fetchQuestions = async (surveyId: number) => {
    try {
      const response = await $fetch<{
        data: Question[];
      }>(`${config.public.apiBase}/api/v1/surveys/${surveyId}/questions`, {
        headers: getHeaders(),
      });

      return response.data;
    } catch (error: any) {
      throw new Error(error.data?.message || "Failed to fetch questions");
    }
  };

  const createQuestion = async (questionData: CreateQuestionData) => {
    try {
      const response = await $fetch<{
        data: Question;
        message: string;
      }>(
        `${config.public.apiBase}/api/v1/surveys/${questionData.survey_id}/questions`,
        {
          method: "POST",
          headers: getHeaders(),
          body: questionData,
        }
      );

      return response.data;
    } catch (error: any) {
      throw new Error(error.data?.message || "Failed to create question");
    }
  };

  const updateQuestion = async (
    questionId: number,
    questionData: Partial<CreateQuestionData>
  ) => {
    try {
      const response = await $fetch<{
        data: Question;
        message: string;
      }>(`${config.public.apiBase}/api/v1/questions/${questionId}`, {
        method: "PUT",
        headers: getHeaders(),
        body: questionData,
      });

      return response.data;
    } catch (error: any) {
      throw new Error(error.data?.message || "Failed to update question");
    }
  };

  const deleteQuestion = async (questionId: number) => {
    try {
      await $fetch(`${config.public.apiBase}/api/v1/questions/${questionId}`, {
        method: "DELETE",
        headers: getHeaders(),
      });
    } catch (error: any) {
      throw new Error(error.data?.message || "Failed to delete question");
    }
  };

  const reorderQuestions = async (surveyId: number, questionIds: number[]) => {
    try {
      await $fetch(
        `${config.public.apiBase}/api/v1/surveys/${surveyId}/questions/reorder`,
        {
          method: "POST",
          headers: getHeaders(),
          body: { question_ids: questionIds },
        }
      );
    } catch (error: any) {
      throw new Error(error.data?.message || "Failed to reorder questions");
    }
  };

  return {
    fetchSurveys,
    createSurvey,
    getSurvey,
    updateSurvey,
    deleteSurvey,
    fetchQuestions,
    createQuestion,
    updateQuestion,
    deleteQuestion,
    reorderQuestions,
  };
};
