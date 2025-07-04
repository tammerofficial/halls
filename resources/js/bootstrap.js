import axios from "axios";

// Set baseURL to Laravel server port
axios.defaults.baseURL = "http://localhost:8000";
axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
axios.defaults.headers.common["Accept"] = "application/json";
axios.defaults.headers.common["Content-Type"] = "application/json";

console.log("Axios baseURL set to:", axios.defaults.baseURL);

const token = document.head.querySelector("meta[name=\"csrf-token\"]");
if (token) {
    axios.defaults.headers.common["X-CSRF-TOKEN"] = token.content;
}

const authToken = localStorage.getItem("auth_token");
if (authToken) {
    axios.defaults.headers.common["Authorization"] = `Bearer ${authToken}`;
}

axios.interceptors.request.use(
    config => {
        console.log("Making request to:", config.url, "with data:", config.data);
        return config;
    },
    error => {
        console.error("Request error:", error);
        return Promise.reject(error);
    }
);

axios.interceptors.response.use(
    response => {
        console.log("Response received:", response.status, response.data);
        return response;
    },
    error => {
        console.error("Response error:", error.response?.status, error.response?.data);
        
        if (error.response?.status === 401) {
            localStorage.removeItem("auth_token");
            localStorage.removeItem("user");
            delete axios.defaults.headers.common["Authorization"];
            window.location.href = "/auth/login";
        }
        
        return Promise.reject(error);
    }
);

window.axios = axios;
