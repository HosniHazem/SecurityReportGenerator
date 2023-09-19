import React,{ useState,useEffect } from "react";
import { DataGrid } from "@mui/x-data-grid";
import swal from 'sweetalert';
import {Navigate, useNavigate,useParams} from 'react-router-dom';
import { Link } from "react-router-dom";
import CircularProgress from '@mui/material/CircularProgress';
import Box from '@mui/material/Box';

import "./datatable.scss";
import axios from 'axios';

const Projects = () => {
  const navigate = useNavigate();
    const [Project, setProject] = useState([]);
    const [exporting, setExporting] = useState(false); // Add loading state

    
    useEffect(() => {
      axios.get(`http://webapp.smartskills.local:8000/api/getProject`,).then((res) => {
        if(res.status === 200){
        setProject(res.data.Project);
   }
      });
    }, []);
 
    const userColumns = [
      { field: "id", headerName: "ID", width: 70 },
      {
        field: "Nom",
        headerName: "Nom",
        width: 120,
        renderCell: (params) => {
          return (
            
              params.row.Nom
            
          );
        },
      },
      {
        field: "Description",
        headerName: "Description",
        width: 200,
      },
      {
        field: "URL",
        headerName: "URL",
        width: 160,
      },
      {
        field: "action",
        headerName: "Action",
        width: 160,
        renderCell: (params) => {
          const id=params.row.id;
          const name=params.row.Nom;
          return (
            <div className="cellAction">

                <div className="viewButton" onClick={(e) => Select(name,id,e)}>Select/Add</div>
                
      
              
                <div className="deleteButton"  onClick={(e) => Export(id,e)}>Export</div>
              </div>
          );
         
        },
      },
    
      
      
    
    ];
   
    const Select = (name,id,e) => {
      e.persist();
      sessionStorage.setItem('project_id',id);


      navigate("/import");
    }


    const Export = (id,e) => {
      e.persist();
      const project_id = sessionStorage.getItem('project_id');
       const dataToSend = {
          project_id: id,
        };
        setExporting(true);
        axios.post(`http://webapp.smartskills.local:8000/api/generate-word-document/${project_id}`,dataToSend)
          .then((response) => {
           // Assuming the response is in JSON format and contains a 'download_link'
           const downloadLink = response.data.download_link;
          
           // Trigger the download
           downloadFile(downloadLink);
              swal("Exported","Successfully");
          
          })
          .catch((error) => {
            // Handle errors
            console.error('Error sending data:', error);
          })
          .finally(() => {
            // Set exporting to false when export completes (whether successful or not)
            setExporting(false);
          });
        const downloadFile = (url) => {
          const link = document.createElement('a');
          link.href = url;
          link.target = '_blank'; // Open the link in a new tab
          link.download = 'document_name.docx'; // Change the name as needed
          link.click();
        }; 
  };
    return (
        <div className="datatable">
        <div className="datatableTitle1">
            <h1>
          Projects
          </h1>
        </div>
        {exporting ? ( // Conditional rendering based on the exporting state
       <div className="loading">
       <Box sx={{ display: 'flex' }}>
        <CircularProgress />
      </Box>
      </div>
      ) : (
        <DataGrid
          className="datagrid"
          rows={Project}
          columns={userColumns}
          pageSize={9}
          rowsPerPageOptions={[9]}
          
        />
        )}
      </div>
    );
};

export default Projects;