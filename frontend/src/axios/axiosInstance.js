import axios from "axios";


 export const axiosInstance=axios.create({
    baseURL:'http://webapp.ssk.lc/AppGenerator/backend/api'
})