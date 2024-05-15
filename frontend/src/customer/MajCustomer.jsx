import React, { useState, useEffect } from "react";
import { useNavigate, useParams } from "react-router-dom";
import { Form, Input, Button, Upload, message, Col, Row } from "antd";
import { UploadOutlined } from "@ant-design/icons";
import axios from "axios";
import swal from "sweetalert";
import Swal from "sweetalert2";
import "./Add.css";
import { axiosInstance } from "../axios/axiosInstance";
import toast from "react-hot-toast";

export default function () {
  const { id } = useParams();
  const navigate = useNavigate();
  const [form] = Form.useForm();
  const [CustomerInput, setCustomer] = useState();
  const [Fich, setFich] = useState(null);
  const [logoFile, setLogoFile] = useState(null);
  const [organigrammeFile, setOrganigrammeFile] = useState(null);
  const [networkDesignFile, setNetworkDesignFile] = useState(null);
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

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await axios.get(
          `http://webapp.ssk.lc/AppGenerator/backend/api/Customer/${id}/show`,
          {
            headers: {
              Authorization: `Bearer ${localStorage.getItem("token")}`,
            },
          }
        );

        if (response.data.success) {
          console.log(response.data.data);
          setCustomer(response.data.data);
          const initialValue = response.data.data;
          setFich(response.data.data.Logo);
        } else if (response.data.status === 404) {
          // Handle not found error
        }
      } catch (error) {
        // Handle error
        console.error("Error fetching customer data:", error);
      }
    };

    fetchData();
  }, [id]);

  if (CustomerInput) {
    var initialValues = {
      SN: CustomerInput.SN,
      LN: CustomerInput.LN,
      Description: CustomerInput.Description,
      SecteurActivité: CustomerInput.SecteurActivité,
      Categorie: CustomerInput.Categorie,
      Site_Web: CustomerInput["Site Web"],
      Addresse_mail: CustomerInput["Addresse mail"],
    };
  }
  const UpdateCustomer =async (values) => {
    console.log("values",values)
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
    try {

        const response=await axios.post(`http://webapp.ssk.lc/AppGenerator/backend/api/Customer/${id}/update` ,formData, {
            headers: {
              "Content-Type": "multipart/form-data",
              Authorization: `Bearer ${localStorage.getItem("token")}`,
            },
          })
          console.log("res",response.data)
          if(response.data.success){
            toast.success("customer updated succeffully")
            navigate(-1)
          }

        

    } catch (error) {
        console.log(error);
    }




  };

  console.log("init", initialValues);

  return (
    <div style={{ width: "50%", margin: "0 auto", marginTop: "2%" }}>
      {initialValues && (
        <Form
          name="customer_form"
          onFinish={UpdateCustomer}
          layout="vertical"
          initialValues={initialValues} // Add initialValues prop here
        >
          <p> {initialValues.SN}</p>
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
                rules={[
                  {
                    type: "email",
                    message: "Please enter a valid email address",
                  },
                  { required: false }, // Since it's not required, set required to false
                ]}
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
                  <Button icon={<UploadOutlined />} style={{ width: "200%" }}>
                    Upload Logo
                  </Button>
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
                  <Button icon={<UploadOutlined />} style={{ width: "140%" }}>
                    Upload Organigramme
                  </Button>
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
                  <Button icon={<UploadOutlined />} style={{ width: "140%" }}>
                    Upload Network Design
                  </Button>
                </Upload>
              </Form.Item>
            </Col>
          </Row>

          <Form.Item>
            <Button
              type="primary"
              htmlType="submit"
              style={{ marginLeft: "0%", width: "100%" }}
            >
              Submit
            </Button>
          </Form.Item>
        </Form>
      )}
    </div>
  );
}
