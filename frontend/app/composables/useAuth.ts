import type { User } from "../stores/auth";

export interface LoginCredentials {
  email: string;
  password: string;
}

export interface RegisterData {
  name: string;
  email: string;
  password: string;
  password_confirmation: string;
}

export const useAuth = () => {
  const config = useRuntimeConfig();
  const authStore = useAuthStore();

  const login = async (credentials: LoginCredentials) => {
    try {
      const response = await $fetch<{
        data: { user: User; token: string };
        message: string;
      }>(`${config.public.apiBase}/api/v1/login`, {
        method: "POST",
        body: credentials,
      });

      authStore.login(response.data.user, response.data.token);
      return response;
    } catch (error: any) {
      throw new Error(error.data?.message || "Login failed");
    }
  };

  const register = async (userData: RegisterData) => {
    try {
      const response = await $fetch<{
        data: { user: User; token: string };
        message: string;
      }>(`${config.public.apiBase}/api/v1/register`, {
        method: "POST",
        body: userData,
      });

      authStore.login(response.data.user, response.data.token);
      return response;
    } catch (error: any) {
      throw new Error(error.data?.message || "Registration failed");
    }
  };

  const logout = async () => {
    try {
      await $fetch(`${config.public.apiBase}/api/v1/logout`, {
        method: "POST",
        headers: {
          Authorization: `Bearer ${authStore.token}`,
        },
      });

      authStore.logout();
    } catch (error: any) {
      // Even if API call fails, logout locally
      authStore.logout();
      throw new Error(error.data?.message || "Logout failed");
    }
  };

  const fetchUser = async () => {
    try {
      const response = await $fetch<{ data: User; message: string }>(
        `${config.public.apiBase}/api/v1/user`,
        {
          headers: {
            Authorization: `Bearer ${authStore.token}`,
          },
        }
      );

      authStore.setUser(response.data);
      return response.data;
    } catch (error: any) {
      authStore.logout(); // Token might be invalid
      throw new Error(error.data?.message || "Failed to fetch user");
    }
  };

  const getCsrfCookie = async () => {
    await $fetch(`${config.public.apiBase}/sanctum/csrf-cookie`, {
      credentials: "include",
    });
  };

  return {
    login,
    register,
    logout,
    fetchUser,
    getCsrfCookie,
  };
};
