import React, { useState, useEffect } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { axiosInstance } from "../axios/axiosInstance";
import { Form, Input, Button, Upload, message, Col, Row, Modal,Table } from "antd";
import { InboxOutlined, UploadOutlined } from "@ant-design/icons";
import toast from "react-hot-toast";
import AfterANomalie from "../AfterAnomalie";
import axios from "axios";
const { TextArea } = Input;

export default function Anomalie() {
  const { id } = useParams();
  console.log("id is", id);
  const [projectData, setProjectData] = useState(null);
  const [loading, setLoading] = useState(true);
  const [vm,setVm]=useState();
  const navigate = useNavigate();
  const [htmlFile, setHtmlFile] = useState(null);
  const [hclFile, setHclFile] = useState(null);
  const [isModalVisible, setIsModalVisible] = useState(false);
  const [accuentixNumber, setAccunetixNumber] = useState(0);
  const [invicti, setInvicti] = useState(0);
  const [hcl, setHcl] = useState(0);
  const [owaszap,setOwaszap]=useState(0);
  const [accessKey,setAccessKey]=useState("");
  const showModal = () => {
    setIsModalVisible(true);
  };

  const handleOk = () => {
    setIsModalVisible(false);
  };

  const handleCancel = () => {
    setIsModalVisible(false);
  };

  const normHtmlFile = (e) => {
    if (Array.isArray(e)) {
      return e;
    }
    if (e && e.fileList && e.fileList[0]) {
      setHtmlFile(e.fileList[0].originFileObj);
    }
    return e && e.fileList;
  };

  const normHclFile = (e) => {
    if (Array.isArray(e)) {
      return e;
    }
    if (e && e.fileList && e.fileList[0]) {
      setHclFile(e.fileList[0].originFileObj);
    }
    return e && e.fileList;
  };

  // useEffect(() => {
  //   const fetchData = async () => {
  //     try {
  //       const response = await axiosInstance.get(`Project/${id}/show`);
  //       setProjectData(response.data.Project);
  //       console.log(projectData);
  //       setLoading(false);
  //     } catch (error) {
  //       console.error("Error fetching project data:", error);
  //       // Handle error, for example, redirect to an error page
  //     }
  //   };

  //   fetchData();
  // }, [id, navigate]);

useEffect(()=>{
  const fetchVm = async () => {
    try {
      const response = await axiosInstance.get(`vmtype`);
      console.log("vm resp",response.data.vm);
      setVm(response.data.Vm);
      if (vm && vm.length > 0) {
        console.log("accessKey:", vm[0].accessKey);
        setAccessKey(vm[0].accessKey);
        console.log(accessKey);
      } else {
        console.error("Error fetching project data: Empty or undefined vm array");
      }    } catch (error) {
      console.error("Error fetching project data:", error);
      // Handle error, for example, redirect to an error page
    }
  };

  fetchVm();
},[])



  useEffect(() => {
    console.log("isModalVisible:", isModalVisible);
  }, [isModalVisible]);

  const onFinish = async (values) => {
    console.log(values);
  
    try {
      if (values.q) {
        const responseQuery = await axiosInstance.post(
          "/get-vuln",
          {
            q: values.q,
            id: id,
          },
          {
            headers: {
              "X-Auth":
                accessKey,
            },
          }
        );
  
        console.log(responseQuery.data);
  
        if (responseQuery.data.success) {
          toast.success(responseQuery.data.message);
          setAccunetixNumber(responseQuery.data.data);
        } else {
          toast.error("wrong");
        }
      }
    } catch (error) {
      toast.error("something wrong with query");
      console.log(error);
    }
    
    try {
      if (values.q) {

        const responseQuery2 = await axios.post(
          "http://webapp.smartskills.tn/AppGenerator/backend/api/owaszap",
          {
            q: values.q,
            id: id,
          },
        );
  
        console.log(responseQuery2.data);
  
        if (responseQuery2.data) {
          toast.success(responseQuery2.data.message);
          setOwaszap(responseQuery2.data.data);
        } else {
          toast.error("wrong");
        }
      }
    } catch (error) {
      toast.error("something wrong with query");
      console.log(error);
    }



    try {
      // Send the file1 value to the first endpoint with id
      if (values.file1 && values.file1[0].originFileObj) {
        const formDataFile1 = new FormData();
        formDataFile1.append("vuln", values.file1[0]?.originFileObj, "vuln.html");
  
        const responseFile1 = await axiosInstance.post(
          `/vuln-from-html/${id}`,
          formDataFile1,
          {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          }
        );
  
        console.log(responseFile1.data);
  
        if (responseFile1.data.success) {
          toast.success(responseFile1.data.message);
          setInvicti(responseFile1.data.number);
        } else {
          toast.error("wrong");
        }
      }
    } catch (error) {
      toast.error("something wrong with file1");
      console.log(error);
    }
  
    try {
      // Send the file2 value to the second endpoint without id
      if (values.file2 && values.file2[0]?.originFileObj) {
        const formDataFile2 = new FormData();
        formDataFile2.append("vuln", values.file2[0]?.originFileObj);
    
        const responseFile2 = await axiosInstance.post(
          `/vuln-from-hcl/${id}`,
          formDataFile2,
          {
            headers: {
              "Content-Type": "multipart/form-data",
            },
          }
        );
    
        console.log(responseFile2.data);
    
        if (responseFile2.data.success) {
          toast.success(responseFile2.data.message);
          setHcl(responseFile2.data.number);
        } else {
          toast.error("wrong");
        }
      } else {
        console.log("File 2 is not present. Skipping the request.");
      }
      showModal();


    } catch (error) {
      toast.error("something wrong with file2");
      console.log(error);
    }
    
    
  };
  
  



  const columns = [
    {
      title: 'ip',
      dataIndex: 'ip',
      key: 'ip',
    },
    {
      title: 'answer',
      dataIndex: 'answer',
      key: 'answer',
      render: (text, record) => (
        <span style={{ color: text === 'Online' ? 'green' : 'red' }}>
          {text}
        </span>
      ),
    },
    {
      title: 'Type',
      dataIndex: 'Type',
      key: 'Type',
    },
  ];

  return (
    <div style={{ width: "80%", marginLeft: "10%" }}>
          <Table dataSource={vm} columns={columns}  pagination={false} bordered style={{
            marginTop:"10%"
          }} />

      <h2>Accunetix & OWASZAP Queries</h2>

      <Form onFinish={onFinish} layout="vertical">
        <Form.Item
          label="Query"
          name="q"
          // rules={[{ required: true, message: "Please enter Form 1 field!" }]}
        >
          <TextArea rows={4} />
        </Form.Item>

        <h2>File Uploads</h2>
        <div style={{ alignContent: "center" }}>
          <Row>
            <Col span={8}>
              <Form.Item
                name="file1"
                label="Invicti"
                valuePropName="fileList"
                getValueFromEvent={normHtmlFile}
              >
                <Upload
                  name="file1"
                  beforeUpload={(file) => {
                    setHtmlFile(file);
                    return false; // Returning false prevents automatic upload
                  }}
                >
                  <Button icon={<UploadOutlined />} style={{ width: "200%" }}>
                    Upload HTML
                  </Button>
                </Upload>
              </Form.Item>
            </Col>
            <Col span={8}>
              <Form.Item
                name="file2"
                label="HCL"
                valuePropName="fileList"
                getValueFromEvent={normHclFile}
              >
                <Upload
                  name="file2"
                  beforeUpload={(file) => {
                    setHclFile(file);
                    return false; // Returning false prevents automatic upload
                  }}
                >
                  <Button
                    icon={<UploadOutlined />}
                    style={{ width: "200%", marginLeft: "6%" }}
                  >
                    Upload HCL{" "}
                  </Button>
                </Upload>
              </Form.Item>
            </Col>
            <Col span={8}>
              <Form.Item>
                <Button
                  type="primary"
                  htmlType="submit"
                  style={{ width: "100%", marginTop: "9%" }}
                >
                  Submit
                </Button>
              </Form.Item>
            </Col>
          </Row>
        </div>
      </Form>
      <Modal
        title="Submitted Values"
        visible={isModalVisible}
        onOk={handleOk}
        onCancel={handleCancel}
      >
        <AfterANomalie
          accuentixNumber={accuentixNumber}
          invicti={invicti}
          hcl={hcl}
          owaszap={owaszap}
        />
      </Modal>
    </div>
  );
}
