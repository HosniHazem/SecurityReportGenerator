import React, { useState, useEffect } from "react";
import { useNavigate, useParams } from "react-router-dom";
import { Form, Input, Button, Upload, message, Col, Row } from "antd";
import { UploadOutlined } from "@ant-design/icons";
import axios from "axios";
import swal from "sweetalert";
import Swal from "sweetalert2";
import "./Add.css";

const { Item } = Form;

function UpdateCustom() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [form] = Form.useForm();
  const [CustomerInput, setCustomer] = useState({});
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
              Authorization: `Bearer ${localStorage.getItem("token")}`
            }
          }
        );
  
        if (response.data.status === 200) {
          setCustomer(response.data.Customer);
          console.log("ccc",CustomerInput)
          setFich(response.data.Customer.Logo);
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
  
  

  const handleInput = (e) => {
    const { name, value } = e.target;
    setCustomer({ ...CustomerInput, [name]: value });
  };

  const handleImage = (info) => {
    if (info.file.status === 'done') {
      message.success(`${info.file.name} file uploaded successfully`);
      const fileExtension = info.file.name.split('.').pop();
      if (CustomerInput.SN) {
        setFich(`${CustomerInput.SN}.${fileExtension}`);
      } else {
        Swal.fire({
          icon: 'error',
          title: 'Oops...',
          text: 'You need to fill the SN before!'
        });
      }
    } else if (info.file.status === 'error') {
      message.error(`${info.file.name} file upload failed.`);
    }
  };

  const UpdateCustomer = () => {
    form.validateFields().then((values) => {
      const { SN, LN, Description, SecteurActivité, Categorie, Site_Web, Addresse_mail } = values;
      if (Fich != null) {
        const data = {
          SN,
          LN,
          Description,
          SecteurActivité,
          Categorie,
          Site_Web,
          Addresse_mail,
          Logo: Fich
          // Add other fields here
        };
        axios.post(`http://webapp.ssk.lc/AppGenerator/backend/api/Customer/${id}/update`, data, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`
          }
        }).then((res) => {

          if (res.data.status === 200) {
            console.log(res.data)
            swal("Created", "Customer", "success");
            navigate("/customer_create");
          } else if (res.data.status === 404) {
            swal("Error", CustomerInput.SN, "error");
          }
        }).catch((error) => {
          console.error("Update Customer Error:", error);
        });
      }
    }).catch((errorInfo) => {
      console.log('Failed:', errorInfo);
    });
  };
  
  console.log("customer",CustomerInput)
// Inside the component


// console.log("init",initialValues)


  return (
    <div style={{ width: "50%", margin: "0 auto" ,marginTop:"2%"}}>
      <Form
        name="customer_form"
        onFinish={UpdateCustomer}
        initialValues={CustomerInput}
        layout="vertical"
      >
        <Row gutter={[16, 16]}>
          <Col span={12}>
            <Form.Item
              name="SN"
              label="SN"
              // initialValue={CustomerInput.SN} // Set initial value for SN field

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

export default UpdateCustom;
