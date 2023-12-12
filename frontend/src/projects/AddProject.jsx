import React, { useState, useEffect } from "react";
import { useNavigate } from "react-router-dom";
import { Grid } from "@mui/material";
import { styled } from "@mui/system";
import { ValidatorForm } from "react-material-ui-form-validator";
import axios from "axios";
import swal from "sweetalert";
import { Span } from "../projects/Typography";
import { Form, Input, Button, Select, Col, Row } from "antd";

import TextField from "@mui/material/TextField";
import "./add.css";

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

function AddProject() {
  const navigate = useNavigate();
  const [form] = Form.useForm();
  const { Option } = Select;

  const [value, setValue] = React.useState(null);
  const [ProjectInput, setProject] = useState({
    Nom: null,
    URL: null,
    Description: null,
    customer_id: null,
    year: null,
    error_list: [],
  });
  const [customer, setCustomer] = useState([]);

  const handleInput = (e) => {
    e.persist();

    setProject({ ...ProjectInput, [e.target.name]: e.target.value });
  };
  const currentDate = new Date();

  useEffect(() => {
    axios
      .get(`http://webapp.smartskills.tn/AppGenerator/backend/api/Customer`)
      .then((res) => {
        if (res.status === 200) {
          setCustomer(res.data.Customer);
        }
      });
  }, []);

  const [error, setError] = useState([]);

  const onFinish = (values) => {
    console.log("values are", values);
    try {
      const response = axios.post(
        `http://webapp.smartskills.tn/AppGenerator/backend/api/Project/create`,
        values
      );

      // if (response.data.success) {
      //   swal("Created", "Project", "success");

      //   navigate("/");
      // } else {
      //   swal("Error", ProjectInput.SN, "error");
      // }
    } catch (error) {
      swal("sometnig went wrong");
      console.log(error);
    }
  };

  const validateYear = (_, value) => {
    const currentYear = new Date().getFullYear();
    const isNumber = !isNaN(value);
    const isInRange = value >= 2000 && value <= currentYear;

    if (isNumber && isInRange) {
      return Promise.resolve();
    }

    if (!isNumber) {
      return Promise.reject("Veuillez entrer un nombre valide!");
    }

    if (!isInRange) {
      return Promise.reject(
        `Veuillez entrer un nombre entre 2000 et ${currentYear}!`
      );
    }

    return Promise.reject("Veuillez entrer une année valide!");
  };

  const initialValues = {
    Nom: "Sample Nom",
    URL: "http://example.com",
    Description: "Sample Description",
    iterationKey: "ABC",
    Year: 2023,
  };

  return (
    <Container>
      <Form form={form} onFinish={onFinish} initialValues={initialValues} layout="vertical">
        <Row gutter={[16, 16]}>
          <Col span={12}>
            <Form.Item
              label="Nom"
              name="Nom"
              rules={[{ required: true, message: "Veuillez entrer un Nom!" }]}
            >
              <Input />
            </Form.Item>{" "}
          </Col>
          <Col span={12}>
            {" "}
            <Form.Item
              label="URL"
              name="URL"
              rules={[{ required: true, message: "Veuillez entrer un URL!" }]}
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
                { required: true, message: "Veuillez entrer une Description!" },
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
              rules={[{ required: true, message: "Please select a customer!" }]}
            >
              <Select
                placeholder="Select a customer"
                onChange={(value) =>
                  form.setFieldsValue({ customer_id: value })
                }
              >
                {customer.map((customer) => (
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
              rules={[
                {
                  required: true,
                  message: "Veuillez entrer un Method Version!",
                },
              ]}
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
          rules={[
            { required: true, message: "Veuillez entrer un nombre valide!" },
          ]}
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
  );
}

export default AddProject;
