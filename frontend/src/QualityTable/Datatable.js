import React, { useState, useEffect } from "react";
import './table.css'; // Import your CSS file for styling
import swal from 'sweetalert';
import axios from 'axios';
import Avatar from '@mui/material/Avatar';
import Stack from '@mui/material/Stack';
import FolderIcon from '@mui/icons-material/Folder';
import { useParams , Link } from 'react-router-dom';
import Box from '@mui/material/Box';
import Fade from '@mui/material/Fade';
import Button from '@mui/material/Button';
import CircularProgress from '@mui/material/CircularProgress';
import Typography from '@mui/material/Typography';
import Dialog from '@mui/material/Dialog';
import DialogActions from '@mui/material/DialogActions';
import DialogContent from '@mui/material/DialogContent';
import DialogContentText from '@mui/material/DialogContentText';
import DialogTitle from '@mui/material/DialogTitle';
import CloseIcon from '@mui/icons-material/Close';
import useMediaQuery from '@mui/material/useMediaQuery';
import { useTheme } from '@mui/material/styles';
import IconButton from '@mui/material/IconButton';
import { Span } from "../projects/Typography";
import { styled } from "@mui/system";
import { ValidatorForm } from "react-material-ui-form-validator";




const Container = styled("div")(({ theme }) => ({
  margin: "30px",
  [theme.breakpoints.down("sm")]: {
    margin: "16px",
  },
  "& .breadcrumb": {
    marginBottom: "20px",
    [theme.breakpoints.down("sm")]: {
      marginBottom: "16px",
    },
  },
}));
const DataTable = ({ data ,id}) => {
  const project_name=sessionStorage.getItem('project_name');
  const [Project, setProject] = useState([]);
  const [Fich, setFich] = useState(null);
  const [open, setOpen] = React.useState(false);
  const [picture, setPicture] = useState({
    attach: "",
  });
  if (!data || data.length === 0) {
    // Handle the case where data is undefined or an empty array
    return      <div>
    <Box
      sx={{
        display: 'flex',
        alignItems: 'center',
        justifyContent: 'center',
        height: '70vh', // Optional: Set the height of the container
      }}
    >
      <CircularProgress />
      <Box
      component="div"
      sx={{
        marginLeft: '8px', // Adjust the left margin as needed
        padding: '8px', // Optional: Add padding for better visual appearance
        color: '#1976d2', // Optional: Set text color
      }}
    >
     <strong>Loading</strong>
    </Box>
    </Box>
   
  </div>;
  }
  const handleInput = (e) => {
    e.persist();

    setProject({ ...Project, [e.target.name]: e.target.value });
  };

  const handleImage = (e) => {
    e.preventDefault();
    setPicture({ attach: e.target.files[0] });

    setFich(e.target.files[0].name);
  };
  console.log(id)

  const getCurrentDateTime = () => {
    const currentDateTime = new Date();
    return currentDateTime.toLocaleString(); // Adjust the date and time format as needed
  };
  const UpdateProject = (e) => {
    e.preventDefault();
     if (Fich != null) {
      const formData = new FormData();
      formData.append("attach", picture.attach);
      if (picture.attach) {
        axios
          .post("http://webapp.ssk.lc/AppGenerator/backend/api/Uploadfile", formData)
          .then((res) => {
            if (res.status === 200) {
            } else if (res.status === 422) {
            }
          });
      }
    } 
 

    const data = {
      QualityCheckedMessage: Project.QualityCheckedMessage,
      Preuve: Fich,
      QualityChecked: 1,
      QualityCheckedDateTime : getCurrentDateTime()
    };
    console.log(data);
     axios
      .put(`http://webapp.ssk.lc/AppGenerator/backend/api/Project/${id}/update`, data)
      .then((res) => {
        if (res.data.status === 200) {
          swal("Updated", "success");
          window.location.reload();
        } else if (res.data.status === 404) {
          swal("Error", Project.SN, "error");
        } else if (res.data.status === 422) {
          swal("All fields are mandetory", "", "error");
          setProject({ ...Project, error_list: res.data.validate_err });
        }
      }); 
  };



  const Action = (cellData) => {

    let parsedData = {};
    parsedData.project_id = id;
   axios.post(`http://webapp.ssk.lc/AppGenerator/backend/api/${cellData}`,parsedData)
    .then((response) => {
      if(response.data.status===200){
        swal("Request","Done","Successfuly");
      }
    }
    ) 
  };

  const handleClickOpen = () => {
    setOpen(true);
  };
  const handleClose = () => {
    setOpen(false);
  };


  // Assuming data has at least one row
  const headers = data[0];

  return (
    <div className="table-container">
       <div className='project'>
       <Dialog open={open} onClose={handleClose} maxWidth={"sm"} fullWidth={"false"}  >
<DialogTitle>QA Feedback</DialogTitle>
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

    <Container>
      <div className="Container">
        <ValidatorForm
          onSubmit={UpdateProject}
          onError={() => null}
          encType="multipart/form-data"
        >
        
        <label htmlFor="exampleFormControlInput1" className="item">
            QualityCheckedMessage :
          </label>
          <textarea
                    name="QualityCheckedMessage"
                    onChange={handleInput}
                    className="form-control"
                    htmlFor="exampleFormControlInput1"
                    value={Project.QualityCheckedMessage}
                  />

          <div className="item"></div>
          <Button className="upload" variant="contained" component="label">
            Upload File
            <input type="file" name="attach" onChange={handleImage} hidden />
          </Button>

          <div className="item">{Fich}</div>
          <div className="item"></div>
          <Button
            type="submit"
            className="button5"
          >
            <Span sx={{ pl: 1, textTransform: "capitalize" }}>Update</Span>
          </Button>
          <Button

            className="button5"
          >
            <Span sx={{ pl: 1, textTransform: "capitalize" }}>Cancel</Span>
          </Button>
        </ValidatorForm>
      </div>
    </Container>

        
       
          
        </DialogContent>
     
   
</Dialog>

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
            <tr key={rowIndex} style={data.slice(1)[rowIndex][2] === 'Danger !!!' ? { backgroundColor: 'red', color: 'white' } : data.slice(1)[rowIndex][2] === 'Information' ? { backgroundColor: 'blue', color: 'white' } : null} >
              {rowData.map((cellData, cellIndex) => (
              <td key={cellIndex} >
            {cellIndex >= 2 ? (
              cellData === 'Danger !!!' ? (
                <div style={{ backgroundColor: 'red',color : 'white' }}>{cellData}</div>
              ) : cellData.includes('/') ? (
                <a href={`http://webapp.ssk.lc/AppGenerator/backend/api${cellData}?prj_id=${id}&fieldsValue=${data.slice(1)[rowIndex][cellIndex-1]}`} target="_blank" rel="noopener noreferrer">{cellData}</a>
              ) : cellData === 'Information' ? (
                <div style={{ backgroundColor: 'blue',color : 'white' }}>{cellData}</div>
              ) : (
                cellData
              )
            ) : (
              cellData
            )}
          </td>
              ))}
            </tr>
          ))}
        </tbody>
      </table>
      {
        data ?
        <div>
        <Link to={`/`} style={{ textDecoration: "none" }}>
      <button className='button3'>Back</button>
          </Link>
          <button className='button4' onClick={()=>handleClickOpen()}>QA  Validation</button>
          </div> : null
      }
    </div>
  );
};

export default DataTable;
