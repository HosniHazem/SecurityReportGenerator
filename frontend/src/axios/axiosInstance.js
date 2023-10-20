import axios from "axios";


 export const axiosInstance=axios.create({
    baseURL:'http://webapp.smartskills.local/AppGenerator/backend/api'
})