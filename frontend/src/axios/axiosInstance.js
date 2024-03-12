import axios from "axios";


 export const axiosInstance=axios.create({
    baseURL:'http://webapp.smartskills.tn/AppGenerator/backend/api',
    headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`,

    }
})