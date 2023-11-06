import React,{ useState,useEffect } from "react";
import { DataGrid } from "@mui/x-data-grid";
import swal from 'sweetalert';
import {Navigate, useNavigate,useParams} from 'react-router-dom';
import { Link } from "react-router-dom";
import CircularProgress from '@mui/material/CircularProgress';
import Box from '@mui/material/Box';
import {encode} from 'html-entities';

import "./datatable.scss";
import axios from 'axios';

const Projects = () => {
  const navigate = useNavigate();
    const [Project, setProject] = useState([]);
    const [exporting, setExporting] = useState(false); // Add loading state
    const [downloading, setDownloading] = useState(false);

    
    useEffect(() => {
      axios.get(`http://webapp.ssk.lc/AppGenerator/backend/api/Project`,).then((res) => {
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
        width: 600,
        renderCell: (params) => {
          const id=params.row.id;
          const name=params.row.Nom;
          return (
            <div className="cellAction">

           
         
             <Link to={`/updateproject/${id}`} style={{ textDecoration: "none" }}>
            <div className="upButton">Update</div>
          </Link>
                
      
              
          <div
              className="doButton"
              onClick={(e) => {
                if (
                  window.confirm(
                    'Do you want to delete it?'
                  )
                ) {
                  handleDelete(e, params.row.id);
                }
              }}
              
              
            >
              
              Delete
            </div>
              
            
              </div>
          );
         
        },
      },
    
      
      
    
    ];

    const handleDelete = async (e,id) => {

      e.preventDefault();
       await axios.delete(`http://webapp.ssk.lc/AppGenerator/backend/api/Project/${id}/delete`).then(res=>{
        if(res.status === 200)
          {
            
              swal("Deleted!",res.data.message,"success");
              window.location.reload();
          }
          else if(res.data.status === 404)
          {
              swal("Error",res.data.message,"error");
              
          }
      });
    };
  
    const Select = (name,id,e) => {
      e.persist();
      sessionStorage.setItem('project_id',id);
      sessionStorage.setItem('project_name',name);


      navigate("/import2");
    }


    const Export = (id, e) => {
      e.persist();
      setDownloading(true);
      const project_id = sessionStorage.getItem('project_id');
      const dataToSend = {
        project_id: id,
      };
      setExporting(true);
      
      axios.post(`http://webapp.ssk.lc/AppGenerator/backend/api/generate-word-document/`, dataToSend, {
        responseType: 'blob', // Set responseType to 'blob' to indicate binary data
      })
        .then((response) => {
          // Use response.data as the blob
          const blob = new Blob([response.data], { type: 'application/octet-stream' });
    
          // Create a URL for the blob
          const url = window.URL.createObjectURL(blob);
    
          // Create a temporary <a> element to trigger the download
          const a = document.createElement('a');
          a.href = url;
          a.download = 'downloaded_files.zip';
          document.body.appendChild(a);
          a.click();
    
          // Remove the temporary <a> element and revoke the URL to free up resources
          document.body.removeChild(a);
          window.URL.revokeObjectURL(url);
    
          setDownloading(false);
          swal("Exported", "Successfully");
        })
        .catch((error) => {
          // Handle errors
          console.error('Error sending data:', error);
          swal("Problem", "Detected");
          setDownloading(false);
        })
        .finally(() => {
          // Set exporting to false when export completes 
          setExporting(false);
        });
    };
    
    const Export2 = (name,id, e) => {
      e.persist();
      setDownloading(true);
      const project_id = sessionStorage.getItem('project_id');
      const dataToSend = {
        project_id: [id],
        annex_id: [1,2,3,4,5,6,7,8],
        ZipIt: "oui"
      };
      console.log(dataToSend);
      
      setExporting(true);
      
      axios.post(`http://webapp.ssk.lc/AppGenerator/backend/api/generate-annexe3/`, dataToSend, {
        responseType: 'blob', // Set responseType to 'blob' to indicate binary data
      })
        .then((response) => {
          // Use response.data as the blob
          const blob = new Blob([response.data], { type: 'application/octet-stream' });
    
          // Create a URL for the blob
          const url = window.URL.createObjectURL(blob);
    
          // Create a temporary <a> element to trigger the download
          const a = document.createElement('a');
          a.href = url;
          a.download = name+'downloaded_files.zip';
          document.body.appendChild(a);
          a.click();
    
          // Remove the temporary <a> element and revoke the URL to free up resources
          document.body.removeChild(a);
          window.URL.revokeObjectURL(url);
    
          setDownloading(false);
          swal("Exported", "Successfully");
        })
        .catch((error) => {
          // Handle errors
          console.error('Error sending data:', error);
          swal("Problem", "Detected");
          setDownloading(false);
        })
        .finally(() => {
          // Set exporting to false when export completes 
          setExporting(false);
        });
        
    };
    const Export3 = (name,id, e) => {
      e.persist();
      setDownloading(true);
      const project_id = sessionStorage.getItem('project_id');
      const dataToSend = {
        project_id: id,
        filename: name
      };
      console.log(dataToSend);
      
      setExporting(true);
      
      axios.post(`http://webapp.ssk.lc/AppGenerator/backend/api/generateExcelDocument/`, dataToSend, {
        responseType: 'blob', // Set responseType to 'blob' to indicate binary data
      })
        .then((response) => {
          // Use response.data as the blob
          const blob = new Blob([response.data], { type: 'application/octet-stream' });
    
          // Create a URL for the blob
          const url = window.URL.createObjectURL(blob);
    
          // Create a temporary <a> element to trigger the download
          const a = document.createElement('a');
          a.href = url;
          a.download = name+'.csv';
          document.body.appendChild(a);
          a.click();
    
          // Remove the temporary <a> element and revoke the URL to free up resources
          document.body.removeChild(a);
          window.URL.revokeObjectURL(url);
    
          setDownloading(false);
          swal("Exported", "Successfully");
        })
        .catch((error) => {
          // Handle errors
          console.error('Error sending data:', error);
          swal("Problem", "Detected");
          setDownloading(false);
        })
        .finally(() => {
          // Set exporting to false when export completes 
          setExporting(false);
        });
        
    };
    
    
    return (
        <div className="datatable">
            <div className="button-container">
    <Link to="/newproject" className="blue-button">
      Add New
    </Link>
    </div> 
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