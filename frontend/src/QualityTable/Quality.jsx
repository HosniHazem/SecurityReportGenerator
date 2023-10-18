import React, { useRef,useContext , useState, useEffect } from 'react';
import {Navigate, useNavigate,useParams} from 'react-router-dom';
import axios from 'axios';
import DataTable from './Datatable';
function Quality() {
    const { id } = useParams();
    console.log(id);
    const [Quality, setQuality] = useState([]);
    let parsedData = {};
    parsedData.project_id = id;
     
    useEffect(() => {
      // Function to fetch data when the component mounts
      const fetchData = async () => {
        try {
          const response = await axios.post('http://webapp.smartskills.local:8002/api/QualityCheck', parsedData);
  
          if (response.status === 200) {
            setQuality(response.data.QC);
          }
        } catch (error) {
          console.error('Error fetching data:', error);
        }
      };
  
      // Call the fetchData function when the component mounts
      fetchData();
  
      // Cleanup function (optional)
      // You can return a function from useEffect to perform cleanup (e.g., clear timers, cancel subscriptions)
      // This function will be called when the component is unmounted
      return () => {
        // Cleanup code (if needed)
      };
    }, []); 
    console.log(Quality);
      
  return (
    <div>
      <h1>Quality Table</h1>
      <DataTable data={Quality} />
    </div>
  )
}

export default Quality