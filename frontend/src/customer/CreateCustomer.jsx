import React,{ useState,useEffect }  from 'react';
import {Navigate, useNavigate,Link} from 'react-router-dom';
import { DataGrid } from "@mui/x-data-grid";
import swal from 'sweetalert';
import axios from 'axios';
import CircularProgress from '@mui/material/CircularProgress';
import Box from '@mui/material/Box';
import "./Add.css";
function CreateCustomer() {
  const navigate = useNavigate();
  const [Customer, setCustomer] = useState([]);
  const [exporting, setExporting] = useState(false); // Add loading state
  const [downloading, setDownloading] = useState(false);

  
  useEffect(() => {
    axios.get(`http://webapp.smartskills.local:8002/api/Customer`,).then((res) => {
      if(res.status === 200){
      setCustomer(res.data.Customer);
 }
    });
  }, []);

  const userColumns = [
    { field: "id", headerName: "ID", width: 70 },
    {
      field: "SN",
      headerName: "SN",
      width: 200,
    },
    {
      field: "LN",
      headerName: "LN",
      width: 160,
    },
    {
      field: "action",
      headerName: "Action",
      width: 400,
      renderCell: (params) => {
        const id=params.row.id;
        return (
          <div className="cellAction"> 
             <Link to={`/updatecustomer/${id}`} style={{ textDecoration: "none" }}>
            <div className="viewButton">Update</div>
          </Link>
                
      
              
          <div
              className="deButton"
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
     await axios.delete(`http://webapp.smartskills.local:8002/api/Customer/${id}/delete`).then(res=>{
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

    
  return (
    <div className="datatable">
    <div className="button-container">
    <Link to="/newcustomer" className="blue-button">
      Add New
    </Link>
    </div> 
     
      <div className="datatable">
        <div className="datatableTitle1">
            <h1>
          Customers
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
          rows={Customer}
          columns={userColumns}
          pageSize={9}
          rowsPerPageOptions={[9]}
          
        />
        )}
      </div>

    </div>
  )
}

export default CreateCustomer