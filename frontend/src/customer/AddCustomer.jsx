import React, { useEffect, useState } from "react";
import { useNavigate, useParams } from "react-router-dom";
import { styled } from "@mui/system";
import axios from "axios";
import swal from "sweetalert";
import Swal from "sweetalert2";
import { Form, Input, Button, Upload, message, Col, Row } from "antd";
import { UploadOutlined } from "@ant-design/icons";
import { Span } from "../projects/Typography";
import "./Add.css";
import { axiosInstance } from "../axios/axiosInstance";

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

function AddCustom() {
  const navigate = useNavigate();
  const [logoFile, setLogoFile] = useState(null);
  const [organigrammeFile, setOrganigrammeFile] = useState(null);
  const [networkDesignFile, setNetworkDesignFile] = useState(null);
  const {id}=useParams();
  const [form] = Form.useForm();
  const [customer,setCustomer]=useState(null);


  useEffect(() => {
    // Fetch customer details when component mounts
    const fetchCustomerDetails = async () => {
      try {
        const response = await axiosInstance.get(
          `Customer/${id}/show`
        );
          console.log(response.data);
        if (response.data.status === 200) {
          // Set form values with the received customer data
          setCustomer(response.data.Customer);
          console.log(customer);
        } 
      } catch (error) {
        // Handle errors
        console.error(error);
      }
    };

    fetchCustomerDetails();
  }, [id, form]);


  const normLogoFile = (e) => {
    if (Array.isArray(e)) {
      return e;
    }
    if (e && e.fileList && e.fileList[0]) {
      setLogoFile(e.fileList[0].originFileObj);
    }
    return e && e.fileList;
  };

  const normOrganigrammeFile = (e) => {
    if (Array.isArray(e)) {
      return e;
    }
    if (e && e.fileList && e.fileList[0]) {
      setOrganigrammeFile(e.fileList[0].originFileObj);
    }
    return e && e.fileList;
  };

  const normNetworkDesignFile = (e) => {
    if (Array.isArray(e)) {
      return e;
    }
    if (e && e.fileList && e.fileList[0]) {
      setNetworkDesignFile(e.fileList[0].originFileObj);
    }
    return e && e.fileList;
  };

  const onFinish = async (values) => {
    console.log("values",values)
    try {
      const formData = new FormData();

      Object.entries(values).forEach(([key, value]) => {
        if (value !== undefined && value !== null && value !== "") {
          // Append key-value pair to FormData
          if (key === "Logo" || key === "Organigramme" || key === "Network_Design") {
            formData.append(key, value[0]?.originFileObj);
          } else {
            formData.append(key, value);
          }
        }
      });
// Assuming values.Logo, values.Organigramme, and values.Network_Design are arrays



      const response = await axiosInstance.post(
        `Customer/${id}/update`,
        formData,
        {
          headers: {
            "Content-Type": "multipart/form-data",
          },
        }
      );
        console.log(response.data)
      // Handle the response from your Laravel backend
      if (response.data.status === 200) {
        Swal.fire({
          title: "Customer Updated Successfully",
          icon: "success",
        });
        navigate("/customer");
      } else {
        Swal.fire({
          title: "Error creating Customer",
        });
      }
    } catch (error) {
      // Handle errors
      console.error(error);
    }
  };

  // const initialValues = {
  //   SN: customer?.SN,
  //   LN: customer?.LN ,
  //   Description: customer?.Description,
  //   SecteurActivité: "Initial Secteur d'Activité Value",
  //   Categorie: "Initial Catégorie Value",
  //   Site_Web: "Initial Site Web Value",
  //   Addresse_mail: "ali@gmail.com",
  // };

  return (
    <div style={{ width: "50%", margin: "0 auto" ,marginTop:"2%"}}>
      <Form
        name="customer_form"
        onFinish={onFinish}
        // initialValues={initialValues}
        layout="vertical"
      >
        <Row gutter={[16, 16]}>
          <Col span={12}>
            <Form.Item
              name="SN"
              label="SN"
              // rules={[{ required: true, message: "Please enter SN" }]}
            >
              <Input />
            </Form.Item>
          </Col>
          <Col span={12}>
            <Form.Item
              name="LN"
              label="LN"
              // rules={[{ required: true, message: "Please enter LN" }]}
            >
              <Input />
            </Form.Item>
          </Col>
        </Row>

        <Row gutter={[16, 16]}>
          <Col span={12}>
            <Form.Item
              name="Description"
              label="Description"
              // rules={[{ required: true, message: "Please enter Description" }]}
            >
              <Input />
            </Form.Item>
          </Col>
          <Col span={12}>
            <Form.Item
              name="SecteurActivité"
              label="Secteur d'Activité"
              // rules={[
              //   { required: true, message: "Please enter Secteur d'Activité" },
              // ]}
            >
              <Input />
            </Form.Item>
          </Col>
        </Row>

        <Row gutter={[16, 16]}>
          <Col span={12}>
            <Form.Item
              name="Categorie"
              label="Catégorie"
              // rules={[{ required: true, message: "Please enter Catégorie" }]}
            >
              <Input />
            </Form.Item>
          </Col>
          <Col span={12}>
            <Form.Item
              name="Site_Web"
              label="Site Web"
              // rules={[{ required: true, message: "Please enter Site Web" }]}
            >
              <Input />
            </Form.Item>
          </Col>
        </Row>

        <Row gutter={[16, 16]}>
          <Col span={12}>
            <Form.Item
              name="Addresse_mail"
              label="Adresse Mail"
              // rules={[{ required: true, message: "Please enter Adresse Mail" }]}
            >
              <Input />
            </Form.Item>
          </Col>
          <Col span={12}>
            <Form.Item
              name="Logo"
              label="Logo"
              valuePropName="fileList"
              getValueFromEvent={normLogoFile}
            >
              <Upload
                name="logo"
                beforeUpload={(file) => {
                  setLogoFile(file);
                  return false; // Returning false prevents automatic upload
                }}
              >
                <Button icon={<UploadOutlined />} style={{width:"200%"}} >Upload Logo</Button>
              </Upload>
            </Form.Item>
          </Col>
        </Row>

        <Row gutter={[16, 16]}>
          <Col span={12}>
            <Form.Item
              name="Organigramme"
              label="Organigramme"
              valuePropName="fileList"
              getValueFromEvent={normOrganigrammeFile}
            >
              <Upload
                name="organigramme"
                beforeUpload={(file) => {
                  setOrganigrammeFile(file);
                  return false; // Returning false prevents automatic upload
                }}
              >
                <Button icon={<UploadOutlined />} style={{width:"140%"}}>Upload Organigramme</Button>
              </Upload>
            </Form.Item>
          </Col>
          <Col span={12}>
            <Form.Item
              name="Network_Design"
              label="Network Design"
              valuePropName="fileList"
              getValueFromEvent={normNetworkDesignFile}
            >
              <Upload
                name="network_design"
                beforeUpload={(file) => {
                  setNetworkDesignFile(file);
                  return false; // Returning false prevents automatic upload
                }}
                listType="picture"
              >
                <Button icon={<UploadOutlined />} style={{width:"140%"}}>Upload Network Design</Button>
              </Upload>
            </Form.Item>
          </Col>
        </Row>

        <Form.Item>
          <Button type="primary" htmlType="submit" style={{marginLeft:"0%" ,width:"100%"}}>
            Submit
          </Button>
        </Form.Item>
      </Form>
    </div>
  );
}

export default AddCustom;
