import React,{ useState,useEffect } from "react";
import { useNavigate   } from 'react-router-dom';
import {
  Button,
  Grid,
} from '@mui/material'
import { styled } from '@mui/system'
import { ValidatorForm} from 'react-material-ui-form-validator'
import axios from 'axios';
import swal from 'sweetalert';
import Swal from 'sweetalert2'

import { Span } from '../projects/Typography'
import TextField from '@mui/material/TextField';
import "./Add.css";

const Container = styled('div')(({ theme }) => ({
    margin: '30px',
    [theme.breakpoints.down('sm')]: {
        margin: '16px',
    },
    '& .breadcrumb': {
        marginBottom: '20px',
        [theme.breakpoints.down('sm')]: {
            marginBottom: '16px',
        },
    },
}))


function AddCustom() {
    const navigate = useNavigate();
    const [value, setValue] = React.useState(null);
    const [CustomerInput, setCustomer] = useState({
      SN:null,
      LN:null,
      Logo:null,
      error_list: [],
      });

      const handleInput = (e) => {
        e.persist();
       
        setCustomer({...CustomerInput, [e.target.name]: e.target.value });
    }
    const [Fich, setFich] = useState(null);

      const [picture,setPicture] = useState({
        attach:""
      });

      const [error,setError] = useState([]);

      const handleImage = (e) => {
        e.preventDefault();
        setPicture({attach : e.target.files[0]});
        const fileType = e.target.files[0]["type"];
        
        const fileExtension = e.target.files[0].name.split('.').pop();
        if(CustomerInput.SN){
          setFich(CustomerInput.SN+"."+fileExtension);
        }else {
          Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'You need to fill the SN before!'
          })
        }
        
  console.log(Fich);
       
      }


      const AddCustomer = (e) => {
        if(Fich!=null){
        const formData = new FormData();
    formData.append('attach',picture.attach);
    formData.append('name',CustomerInput.SN);
   console.log(formData);
     axios.post('http://webapp.smartskills.tn/AppGenerator/backend/api/imageProfil',formData).then(res=>{
       if(res.status=== 200){

       }
       else if (res.status=== 422){
       }
     },
     )
    }  
          e.preventDefault();
          
         
              const  data = {
                  SN: CustomerInput.SN,
                  LN:CustomerInput.LN,
                  Logo:Fich,
              }

      axios.post(`http://webapp.smartskills.tn/AppGenerator/backend/api/Customer/create`, data).then(res=>{
          if(res.data.status === 200)
          {
              
              swal("Created","Customer","success");
             navigate('/customer');
          }
          else if(res.data.status === 404)
          {
              swal("Error",CustomerInput.SN,"error");
          }
          else if(res.data.status === 422)
          {
            swal("All fields are mandetory","","error");
            setCustomer({...CustomerInput, error_list: res.data.validate_err });  
          }
      });
   
  }
  return (

    <Container >
    <div className="Container">

        <ValidatorForm onSubmit={AddCustomer} onError={() => null} encType="multipart/form-data">
         
                  <label htmlFor="exampleFormControlInput1" className="item">SN :</label>
                      <input type="text" name="SN" onChange={handleInput}  className="form-control" htmlFor="exampleFormControlInput1" value={CustomerInput.SN}  />

                  <label htmlFor="exampleFormControlInput1" className="item">LN :</label>
                      <input type="text" name="LN" onChange={handleInput}  className="form-control" htmlFor="exampleFormControlInput1" value={CustomerInput.LN}  />


<div className="item"></div>
<Button 
className="upload"
variant="contained"
component="label"
>

Upload File
<input
  type="file"
  name="attach"
  onChange={handleImage}
  hidden
/>
</Button>

<div className="item">{Fich}</div>
<div className="item"></div>
            <Button color="primary" variant="contained" type="submit" className="item" >
                <Span sx={{ pl: 1, textTransform: 'capitalize' }}>
                ADD
                </Span>
            </Button>
  </ValidatorForm>
        </div>

    </Container>

  )
}

export default AddCustom