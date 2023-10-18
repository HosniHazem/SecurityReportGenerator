import React, { useRef,useContext , useState, useEffect } from 'react';
import {Navigate, useNavigate,useParams} from 'react-router-dom';
import axios from 'axios';
import DataTable from './Datatable';
function Quality() {
    const { id } = useParams();
    const [Quality, setQuality] = useState([]);
    useEffect(() => {
        axios.get(`http://webapp.smartskills.tn:8002/api/QualityCheck`,).then((res) => {
          if(res.status === 200){
            setQuality(res.data.QC);
     }
        });
      }, []);
      
  return (
    <div>
      <h1>Quality Table</h1>
      <DataTable data={Quality} />
    </div>
  )
}

export default Quality