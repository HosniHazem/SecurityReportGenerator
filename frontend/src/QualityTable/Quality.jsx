import React, { useRef,useContext , useState, useEffect } from 'react';
import {Navigate, useNavigate,useParams} from 'react-router-dom';
import axios from 'axios';
import DataTable from './Datatable';
import './table.css'
function Quality() {
    const { id } = useParams();

    const [Quality, setQuality] = useState([]);
    let parsedData = {};
    parsedData.project_id = id;
    useEffect(() => {
   axios.post('http://webapp.smartskills.tn/AppGenerator/backend/api/QualityCheck', parsedData).then((res) => {
console.log(res.data);
          if (res.data.status === 200) {
            setQuality(res.data.QC);
          }
      
      });

    }, []); 

  return (
    <div>
      <h1 className='head'>Quality Table</h1>
      <DataTable data={Quality} id={id} />
    </div>
  )
}

export default Quality