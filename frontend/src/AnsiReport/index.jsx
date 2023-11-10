import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { axiosInstance } from '../axios/axiosInstance';

export default function AnsiReport() {
  const { id } = useParams();
  const [project, setProject] = useState(null);
  const navigate = useNavigate();

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await axiosInstance.get(`/Project/${id}/show`);
        if (response.status === 200) {
          setProject(response.data.Project);
          console.log(response.data.Project.customer_id);

          // Additional request using the customer_id
          if (response.data.Project && response.data.Project.customer_id) {
            window.location.href = `http://webapp.smartskills.tn/AppGenerator/backend/api/generate-ansi/${response.data.Project.customer_id}`;
          }
        }
      } catch (error) {
        console.error("Error fetching data:", error);
      }
    };

    // Only fetch data if project is null
    if (!project) {
      fetchData();
    }
  }, [id, project]);

  return (
    <div>index</div>
  );
}
