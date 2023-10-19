import React from 'react';
import './table.css'; // Import your CSS file for styling
import swal from 'sweetalert';
import axios from 'axios';
import Avatar from '@mui/material/Avatar';
import Stack from '@mui/material/Stack';
import FolderIcon from '@mui/icons-material/Folder';

const DataTable = ({ data ,id}) => {
  if (!data || data.length === 0) {
    // Handle the case where data is undefined or an empty array
    return <div>Data Not Ready</div>;
  }
 const project_name=sessionStorage.getItem('project_name');
  console.log(id)
  const Action = (cellData) => {
    let parsedData = {};
    parsedData.project_id = id;
   axios.post(`http://webapp.smartskills.local/AppGenerator/backend/api/${cellData}`,parsedData)
    .then((response) => {
      if(response.data.status===200){
        swal("Request","Done","Successfuly");
      }
    }
    ) 
  };

  // Assuming data has at least one row
  const headers = data[0];

  return (
    <div className="table-container">
       <div className='project'>
       <Stack direction="row" spacing={2}>
      <Avatar>
        <FolderIcon />  
      </Avatar>
      
      <h3>Project Name:</h3>
      <span>{project_name}</span>
   
      </Stack>
      </div>
      <table className="data-table">
        <thead>
          <tr>
            {headers.map((header, index) => (
              <th key={index}>{header}</th>
            ))}
          </tr>
        </thead>
        <tbody>
          {data.slice(1).map((rowData, rowIndex) => (
            <tr key={rowIndex}>
              {rowData.map((cellData, cellIndex) => (
                <td key={cellIndex}>
                  {cellIndex === 2 ? (
                   <div className='Pick'  onClick={(e) => Action(cellData)}>  {cellData}</div>
                  ) : (
                    cellData
                  )}
                </td>
              ))}
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};

export default DataTable;
