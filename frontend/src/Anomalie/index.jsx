import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { axiosInstance } from '../axios/axiosInstance';
import { Spin, Space, Button } from 'antd';
import toast from 'react-hot-toast';

export default function Anomalie() {
  const { id } = useParams();
  console.log("id is",id);
  const [projectData, setProjectData] = useState(null);
  const [loading, setLoading] = useState(true);
  const navigate = useNavigate();

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await axiosInstance.get(`Project/${id}/show`);
        setProjectData(response.data.Project);
        console.log(projectData);
        setLoading(false);
      } catch (error) {
        console.error('Error fetching project data:', error);
        // Handle error, for example, redirect to an error page
      }
    };

    fetchData();
  }, [id, navigate]);


  const getVulns=async()=>{
    try {

        const response = await axiosInstance.post("/get-vuln", {
            q: "Sodexo",
            //q:projectData.Name
            id: id,
          }, {
            headers: {
              'X-Auth': "1986ad8c0a5b3df4d7028d5f3c06e936c1dec77fb40364d109ff9c6b70f27bc4a",
            },
          });
          console.log(response.data)
    if(response.data.success){
        toast.success(response.data.message);
    }
    else {
        toast.error("wrong")
    }

        
    } catch (error) {
        toast.error("wrong")

    }
    


  }





  return (
    <div style={{ display: 'flex', justifyContent: 'center', alignItems: 'center', height: '100vh' }}>
      <Button type='primary' onClick={getVulns}>Get Vulnerabilités</Button>
    </div>
  );
}
