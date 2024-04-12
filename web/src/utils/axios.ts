/**
 * axios setup to use mock service
 */

import axios from "axios";

const baseUrl = `${import.meta.env.VITE_API_LINK}`;
const axiosServices = axios.create({
  baseURL: baseUrl,
  timeout: 1000,
});

// interceptor for http
axiosServices.interceptors.response.use(
  (response) => response,
  (error) =>
    Promise.reject((error.response && error.response.data) || "Wrong Services")
);

export default axiosServices;
