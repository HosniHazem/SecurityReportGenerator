import axios from "axios";


 export const axiosInstance=axios.create({
    baseURL:'http://webapp.smartskills.local:8002/api'
})