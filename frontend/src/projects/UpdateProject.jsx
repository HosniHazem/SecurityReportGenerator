import React, { useState, useEffect } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { Form, Input, Button, Select, Row, Col } from "antd";
import axios from "axios";
import toast from "react-hot-toast";
import { axiosInstance } from "../axios/axiosInstance";
import { styled } from "@mui/system";

const { Option } = Select;


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

function UpdateProject() {


  const navigate = useNavigate();
  const { id } = useParams(); // Assuming you have the project ID in the route params

  const [form] = Form.useForm();
  const [project, setProject] = useState(null);
  const [customers, setCustomers] = useState([]);


  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await axiosInstance.get(`http://webapp.ssk.lc/AppGenerator/backend/api/Project/${id}/show`);
        console.log("res",response.data);
        if (response.data.success) {
          console.log("proj",response.data.Project);
          setProject(response.data.Project)
        }
      } catch (error) {
        console.log(error);
      }
    };
  
    fetchData();
  }, [id]);
  
      const handleInput = (e) => {
        e.persist();
        setProject({ ...project, [e.target.name]: e.target.value });
      };
      
      useEffect(() => {
        axios.get(`http://webapp.ssk.lc/AppGenerator/backend/api/Customer`).then((res) => {
          if (res.status === 200) {
            setCustomers(res.data.Customer);
          }
        });
      }, []);
      

      const [error,setError] = useState([]);


     const onFinish = async (values) => {
      console.log("values",values)
    try {
      const response=await axiosInstance.post(`Project/${id}/update`,values);
      console.log(response.data.status)
      if(response.data.status===200){
        toast.success(response.data.message)
        navigate(-1);
      }

    } catch (error) { 
      console.log("error",error)
      
    }
  };

  return (
    <div>
      <h1>Update Project</h1>
      {project && (
         <Container>
         <Form form={form} onFinish={onFinish}  layout="vertical" initialValues={project}>
           <Row gutter={[16, 16]}>
             <Col span={12}>
               <Form.Item
                 label="Nom"
                 name="Nom"
                //  rules={[{ required: true, message: "Veuillez entrer un Nom!" }]}
               >
                 <Input />
               </Form.Item>{" "}
             </Col>
             <Col span={12}>
               {" "}
               <Form.Item
                 label="URL"
                 name="URL"
                //  rules={[{ required: true, message: "Veuillez entrer un URL!" }]}
               >
                 <Input />
               </Form.Item>
             </Col>
           </Row>
           <Row gutter={[16, 16]}>
             <Col span={12}>
               {" "}
               <Form.Item
                 label="Description"
                 name="Description"
                 rules={[
                  //  { required: true, message: "Veuillez entrer une Description!" },
                 ]}
               >
                 <Input />
               </Form.Item>{" "}
             </Col>
             <Col span={12}>
               {" "}
               <Form.Item
                 label="Customer"
                 name="customer_id"
                //  rules={[{ required: true, message: "Please select a customer!" }]}
               >
                 <Select
                   placeholder="Select a customer"
                   onChange={(value) =>
                     form.setFieldsValue({ customer_id: value })
                   }
                 >
                   {customers.map((customer) => (
                     <Option value={customer.id} key={customer.id}>
                       {customer.LN}
                     </Option>
                   ))}
                 </Select>
               </Form.Item>
             </Col>
           </Row>
           <Row gutter={[16, 16]}>
             <Col span={12}>
               {" "}
               <Form.Item
                 label="Iteration Key"
                 name="iterationKey"
                 rules={[
                   {
                     required: true,
                     message: "Veuillez entrer un Iteration Key!",
                   },
                 ]}
               >
                 <Input />
               </Form.Item>
             </Col>
             <Col span={12}>
               {" "}
               <Form.Item
                 label="Method Version"
                 name="methodVersion"
                //  rules={[
                //    {
                //      required: true,
                //      message: "Veuillez entrer un Method Version!",
                //    },
                //  ]}
               >
                 <Select placeholder="Sélectionnez une version" allowClear>
                   <Option value="1.3">1.3-Standard</Option>
                   <Option value="2.1">2.1-Standard</Option>
                 </Select>
               </Form.Item>
             </Col>
           </Row>
           <Row gutter={[16, 16]}> 
           <Col span={12}>
   
           <Form.Item
             label="Year"
             name="year"
            //  rules={[
            //    { required: true, message: "Veuillez entrer un nombre valide!" },
            //  ]}
           >
             <Input type="number" />
           </Form.Item>
           </Col>
         
           <Col span={12}>
   
           <Form.Item >
             <Button type="primary" htmlType="submit" style={{marginTop:"5%",width:"100%"}}>
               Submit
             </Button>
           </Form.Item>
           </Col>
   
   
           </Row>
         </Form>
       </Container>
      )}
    </div>
  );
}

export default UpdateProject