import React,{ useState,useEffect } from "react";
import { useNavigate,useParams} from 'react-router-dom';
import {
  Button,
  Grid,
} from '@mui/material'
import { styled } from '@mui/system'
import { ValidatorForm} from 'react-material-ui-form-validator'
import axios from 'axios';
import swal from 'sweetalert';
import { Span } from './Typography'

import TextField from '@mui/material/TextField';
import "./add.css";

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


function UpdateProject() {


  const { id } = useParams();
    const navigate = useNavigate();
    const [value, setValue] = React.useState(null);
    const [ProjectInput, setProject] = useState([]);
    const [Customer, setCustomer] = useState([]);
    const [Fich, setFich] = useState(null);
      useEffect(() => {
        axios.get(`http://webapp.ssk.lc/AppGenerator/backend/api/Project/${id}/show`).then((res) => {
          if(res.data.status === 200){
            setProject(res.data.Project);
     } else if(res.data.status === 404){
      
     }
        });
      }, [id]);
      const handleInput = (e) => {
        e.persist();
       
        setProject({...ProjectInput, [e.target.name]: e.target.value });
    }
    useEffect(() => {
      axios.get(`http://webapp.ssk.lc/AppGenerator/backend/api/Customer`,).then((res) => {
        if(res.status === 200){
        setCustomer(res.data.Customer);
   }
      });
    }, []);

      const [error,setError] = useState([]);


      const UpdateProject = (e) => {
      
          e.preventDefault();
          
         
          const  data = {

            Nom: ProjectInput.Nom,
            URL: ProjectInput.URL,
            Description: ProjectInput.Description,
            customer_id: ProjectInput.customer_id,
            year: ProjectInput.year,

        }
console.log(data);
      axios.put(`http://webapp.ssk.lc/AppGenerator/backend/api/Project/${id}/update`, data).then(res=>{
          if(res.data.status === 200)
          {
              
              swal("Updated","Project","success");
             navigate('/customer_create');
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

        <ValidatorForm onSubmit={UpdateProject} onError={() => null} encType="multipart/form-data">
         
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
                        value={ProjectInput.customer_id}
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

export default UpdateProject