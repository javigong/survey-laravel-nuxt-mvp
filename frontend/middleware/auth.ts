export default defineNuxtRouteMiddleware((to) => {
  // Temporarily disable all auth middleware to debug routing
  return;
});
