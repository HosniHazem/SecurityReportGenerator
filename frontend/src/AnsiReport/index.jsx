import React, { useState, useEffect } from 'react';
import { useParams, useNavigate } from 'react-router-dom';
import { axiosInstance } from '../axios/axiosInstance';
import { Spin, Space } from 'antd';

export default function AnsiReport() {
  const { id } = useParams();
  const [project, setProject] = useState(null);
  const [loading, setLoading] = useState(true); // State to manage loading
  const navigate = useNavigate();

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await axiosInstance.get(`/Project/${id}/show`);
        if (response.status === 200) {
          setProject(response.data.Project);
          setLoading(false); // Set loading to false once data is fetched

          // Additional request using the customer_id
          if (response.data.Project && response.data.Project.customer_id) {

            window.location.href = `http://webapp.smartskills.tn/AppGenerator/backend/api/generate-ansi/${response.data.Project.customer_id}`;
          }
        }
      } catch (error) {
        console.error("Error fetching data:", error);
        setLoading(false); // Set loading to false in case of an error
      }
    };

    // Only fetch data if project is null
    if (!project) {
      fetchData();
    }
  }, [id, project]);

  return (
    <Space direction="vertical" style={{ width: '1000%', textAlign: 'center' }}>
      {loading ? (
        <Spin size="large" />
      ) : (
        <div>Loaading ...</div>
      )}
    </Space>
  );
}
