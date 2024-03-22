import React, { useState, useEffect } from 'react';
import { useParams } from 'react-router-dom';
import axios from 'axios';
import DataTable from './Datatable';
import './table.css';

function Quality() {
  const { id } = useParams();
  const [quality, setQuality] = useState([]);
  let parsedData = { project_id: id };

  useEffect(() => {
    axios.post('http://webapp.ssk.lc/AppGenerator/backend/api/QualityCheck', parsedData, {
      headers: {
        Authorization: `Bearer ${localStorage.getItem("token")}`
      }
    })
    .then((res) => {
      console.log(res.data);
      if (res.data.status === 200) {
        setQuality(res.data.QC);
      }
    })
    .catch((error) => {
      console.error("Error fetching data:", error);
    });
  }, []);

  return (
    <div>
      <h1 className='head'>Quality Table</h1>
      <DataTable data={quality} id={id} />
    </div>
  );
}

export default Quality;
