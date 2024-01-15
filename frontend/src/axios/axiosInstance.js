import axios from "axios";


 export const axiosInstance=axios.create({
    baseURL:'http://webapp.preprod.ssk.lc/AppGenerator/backend/api'
})