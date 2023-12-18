import React, { useState, useEffect } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { axiosInstance } from "../axios/axiosInstance";
import { Form, Input, Button, Upload, message } from "antd";
import { InboxOutlined, UploadOutlined } from "@ant-design/icons";
import toast from "react-hot-toast";

const { TextArea } = Input;

export default function Anomalie() {
  const { id } = useParams();
  console.log("id is", id);
  const [projectData, setProjectData] = useState(null);
  const [loading, setLoading] = useState(true);
  const navigate = useNavigate();
  const [htmlFile, setHtmlFile] = useState(null);
  const [hclFile, setHclFile] = useState(null);

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

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await axiosInstance.get(`Project/${id}/show`);
        setProjectData(response.data.Project);
        console.log(projectData);
        setLoading(false);
      } catch (error) {
        console.error("Error fetching project data:", error);
        // Handle error, for example, redirect to an error page
      }
    };

    fetchData();
  }, [id, navigate]);

  const getVulns = async () => {};
  const onFinish = async (values) => {
    console.log(values);

    try {
      // const responseQuery = await axiosInstance.post(
      //   "/get-vuln",
      //   {
      //     q: values.q,
      //     id: id,
      //   },
      //   {
      //     headers: {
      //       "X-Auth":
      //         "1986ad8c0a5b3df4d7028d5f3c06e936c1dec77fb40364d109ff9c6b70f27bc4a",
      //     },
      //   }
      // );

      // console.log(responseQuery.data);

      // if (responseQuery.data.success) {
      //   toast.success(responseQuery.data.message);
      // } else {
      //   toast.error("wrong");
      // }

      // Send the file1 value to the first endpoint with id
      const formDataFile1 = new FormData();
      formDataFile1.append("vuln", values.file1[0]?.originFileObj, "vuln.html");
      console.log(values.file1[0]?.originFileObj);
      const responseFile1 = await axiosInstance.post(
        "/vuln-from-html",
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
      } else {
        toast.error("wrong");
      }

      // Send the file2 value to the second endpoint without id
      const formDataFile2 = new FormData();
      formDataFile2.append("vuln", values.file2[0]?.originFileObj);

      const responseFile2 = await axiosInstance.post(
        "/vuln-from-hcl",
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
      } else {
        toast.error("wrong");
      }
    } catch (error) {
      toast.error("something wrong");
    }
  };

  return (
    <div style={{ width: "80%", marginLeft: "10%" }}>
      <h2>Accunetix & OWASZAP Queries</h2>

      <Form onFinish={onFinish}>
        <Form.Item
          label="Query"
          name="q"
          rules={[{ required: true, message: "Please enter Form 1 field!" }]}
        >
          <TextArea rows={4} />
        </Form.Item>

        <h2>File Uploads</h2>

        <Form.Item
          name="file1"
          label="HTML"
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
            <Button icon={<UploadOutlined />} style={{ width: "140%" }}>
              Upload HCL{" "}
            </Button>
          </Upload>
        </Form.Item>

        <Form.Item>
          <Button type="primary" htmlType="submit">
            Submit
          </Button>
        </Form.Item>
      </Form>
    </div>
  );
}
