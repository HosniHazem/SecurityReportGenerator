import React, { useRef,useContext , useState, useEffect } from 'react';
import { DataGrid } from "@mui/x-data-grid";
import swal from 'sweetalert';
import {Navigate, useNavigate,useParams} from 'react-router-dom';
import { Link } from "react-router-dom";
import CircularProgress from '@mui/material/CircularProgress';
import Box from '@mui/material/Box';
import {encode} from 'html-entities';
import axios from 'axios';
import Button from '@mui/material/Button';
import Dialog from '@mui/material/Dialog';
import DialogActions from '@mui/material/DialogActions';
import DialogContent from '@mui/material/DialogContent';
import DialogContentText from '@mui/material/DialogContentText';
import DialogTitle from '@mui/material/DialogTitle';
import useMediaQuery from '@mui/material/useMediaQuery';
import { useTheme } from '@mui/material/styles';
import IconButton from '@mui/material/IconButton';
import CloseIcon from '@mui/icons-material/Close';
import Nessus from '../Nessus';
import "./datatable.scss";
import { green } from '@mui/material/colors';

function useDialogState() {
  const [open, setOpen] = React.useState(false);
  return {open, setOpen};
}
const Dashboard = () => {
  const navigate = useNavigate();
    const [Project, setProject] = useState([]);
    const [Vm, setVm] = useState([]);
    const [exporting, setExporting] = useState(false); // Add loading state
    const [downloading, setDownloading] = useState(false);
    const {open, setOpen} = useDialogState();
    const [selectedIp, setSelectedIp] = useState('');
    const theme = useTheme();
    const fullScreen = useMediaQuery(theme.breakpoints.down('md'));
    
    useEffect(() => {
      axios.get(`http://webapp.smartskills.tn:8002/api/getProject`,).then((res) => {
        if(res.status === 200){
        setProject(res.data.Project);
   }
      });
    }, []);
    useEffect(() => {
      axios.get(`http://webapp.smartskills.tn:8002/api/get_vm`,).then((res) => {
        if(res.status === 200){
          const inputObject = res.data.Vm;
          const outputArray = Object.keys(inputObject).map(key => ({
            id: key,
            ...inputObject[key],
          }));
          setVm(outputArray);

   }
      });
    }, []);

    const userColumns = [
      { field: "id", headerName: "ID", width: 30 },
      {
        field: "Nom",
        headerName: "Nom",
        width: 100,
        renderCell: (params) => {
          return (
            
              params.row.Nom
            
          );
        },
      },

      {
        field: "Import",
        headerName: "Import",
        width: 100,
        renderCell: (params) => {
          const id=params.row.id;
          const name=params.row.Nom;
          return (
            <div className="cellAction">

                <div className="Pick"  onClick={(e) => Popup(name,id,e)}>Import</div>

               
              </div>
          );
         
        },
      },
      {
        field: "Export",
        headerName: "Export",
        width: 600,
        renderCell: (params) => {
          const id=params.row.id;
          const name=params.row.Nom;
          return (
            <div className="cellAction">

              
        <div className="deleteButton"  onClick={(e) => Export(id,e)}>Export</div>

        <div className="deButton"  onClick={(e) => Export2(name,id,e)}>Export Annexe</div>

        <div className="EButton"  onClick={(e) => Export3(name,id,e)}>Export Excel</div>
              </div>
          );
         
        },
      },
    
      
      
    
    ];

    const handleDelete = async (e,id) => {

      e.preventDefault();
       await axios.delete(`http://webapp.smartskills.tn:8002/api/Project/${id}/delete`).then(res=>{
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
    const handleClickOpen = () => {
      setOpen(true);
    };
  
    const handleClose = () => {
      setOpen(false);
    };
  
    const Select = (name,id,e) => {
      e.persist();
      sessionStorage.setItem('project_id',id);
      sessionStorage.setItem('project_name',name);


      navigate("/import");
    }
    const Popup = (name,id,e) => {
      e.persist();
      sessionStorage.setItem('project_id',id);
      sessionStorage.setItem('project_name',name);
      setOpen(true);
    
    }

    const handleSelect = (ip) => {
      // Store the selected IP in session storage
      sessionStorage.setItem('selectedIp', ip);
      setSelectedIp(ip);
    };
  
    const handleCheck = () => {
      // Handle the logic for the checked button
      console.log('Checked button clicked');
    };

    const Export = (id, e) => {
      e.persist();
      setDownloading(true);
      const project_id = sessionStorage.getItem('project_id');
      const dataToSend = {
        project_id: id,
      };
      setExporting(true);
      
      axios.post(`http://webapp.smartskills.tn:8002/api/generate-word-document/`, dataToSend, {
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
      
      axios.post(`http://webapp.smartskills.tn:8002/api/generate-annexe3/`, dataToSend, {
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
      
      axios.post(`http://webapp.smartskills.tn:8002/api/generateExcelDocument/`, dataToSend, {
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
    const cellStyle = {
      padding: '10px',
      textAlign: 'center',
      border: '1px solid black',
    };
   
   
    
    return (
<div>
   
<Dialog open={open} onClose={handleClose} maxWidth={"md"} fullWidth={"false"}  >
<DialogTitle>Import Nessus</DialogTitle>
<IconButton
          aria-label="close"
          onClick={handleClose}
          sx={{
            position: 'absolute',
            right: 8,
            top: 8,
            color: (theme) => theme.palette.grey[500],
          }}
        >
          <CloseIcon />
        </IconButton>

        <DialogContent >
       
       < Nessus />

        <DialogActions>
          <Button onClick={handleClose}>Cancel</Button>

        </DialogActions>
       
          
        </DialogContent>
     
   
</Dialog>


<div className="datatable">   









<table style={{ borderCollapse: 'collapse', width: '15%' }}>
      <thead>
        <tr>
          <th>URL</th>
          <th>Status</th>
          <th>Select</th>
        </tr>
      </thead>

      <tbody>
        {Vm.map((url) => (
          <tr
            key={url.ip}
            style={{ backgroundColor: url.answer === 'Online' ? 'green' : 'red' }}
          >
            <td style={cellStyle}>{url.ip}</td>
            <td style={cellStyle}>{url.answer}</td>
            <td style={cellStyle}>
              <input
                type="radio"
                name="selectedIp"
                value={url.ip}
                checked={url.ip === selectedIp}
                onChange={() => handleSelect(url.ip)}
              />
            </td>
          </tr>
        ))}
      </tbody>
  
    </table>


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
      </div>
    );
};

export default Dashboard;