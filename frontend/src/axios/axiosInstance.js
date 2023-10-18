import axios from "axios";


 export const axiosInstance=axios.create({
    baseURL:'http://webapp.smartskills.tn:8002/api'
})