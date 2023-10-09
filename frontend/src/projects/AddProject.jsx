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
import { Span } from '../projects/Typography'
import { MDBInput } from "mdbreact";
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


function AddProject() {
    const navigate = useNavigate();
    const [value, setValue] = React.useState(null);
    const [ProjectInput, setProject] = useState({
      Nom:null,
      URL:null,
      Description:null,
      customer_id:null,
      year:null,
      error_list: [],
      });
      const [Customer, setCustomer] = useState([]);

      const handleInput = (e) => {
        e.persist();
       
        setProject({...ProjectInput, [e.target.name]: e.target.value });
    }

    useEffect(() => {
      axios.get(`http://webapp.smartskills.local:8002/api/Customer`,).then((res) => {
        if(res.status === 200){
        setCustomer(res.data.Customer);
   }
      });
    }, []);
   

      const [error,setError] = useState([]);

   



      const AddProject = (e) => {

          e.preventDefault();
          
         
              const  data = {

                  Nom: ProjectInput.Nom,
                  URL: ProjectInput.URL,
                  Description: ProjectInput.Description,
                  customer_id: ProjectInput.customer_id,
                  year: ProjectInput.year,

              }
console.log(data);
      axios.post(`http://webapp.smartskills.local:8002/api/Project/create`, data).then(res=>{
          if(res.data.status === 200)
          {
              
              swal("Created","Project","success");
             navigate('/');
          }
          else if(res.data.status === 404)
          {
              swal("Error",ProjectInput.SN,"error");
          }
          else if(res.data.status === 422)
          {
            swal("All fields are mandetory","","error");
            setProject({...ProjectInput, error_list: res.data.validate_err });  
          }
      });
   
  }
  return (

    <Container >
    <div className="Container">

        <ValidatorForm onSubmit={AddProject} onError={() => null} encType="multipart/form-data">
         
                  <label htmlFor="exampleFormControlInput1" className="item">Nom :</label>
                      <input type="text" name="Nom" onChange={handleInput}  className="form-control" htmlFor="exampleFormControlInput1" value={ProjectInput.Nom}  />

                  <label htmlFor="exampleFormControlInput1" className="item">URL :</label>
                      <input type="text" name="URL" onChange={handleInput}  className="form-control" htmlFor="exampleFormControlInput1" value={ProjectInput.URL}  />
                  
                  <label htmlFor="exampleFormControlInput1" className="item">Description :</label>
                      <input type="text" name="Description" onChange={handleInput}  className="form-control" htmlFor="exampleFormControlInput1" value={ProjectInput.Description}  />
                  
                      <div className="form-group">
    <label className="item">Customer :</label>
    <select
                        name="customer_id"
                        className="form-control"
                        onChange={handleInput}
                        value={Customer.id}
                      >
                        <option value="DEFAULT"></option>
                        {Customer.map((item,index) => {
                          return (
                            <option value={item.id} key={index}>
                              {item.LN}
                            </option>
                          );
                        })}
                      </select>
                      <span className="text-danger">{Customer.error_list}</span>
  </div>

                  <label htmlFor="exampleFormControlInput1" className="item">year :</label>
                      <input type="text" name="year" onChange={handleInput}  className="form-control" htmlFor="exampleFormControlInput1" value={ProjectInput.year}  />


<div className="item"></div>

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

export default AddProject