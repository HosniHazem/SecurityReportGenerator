import React, { useState, useEffect } from "react";
import { useParams, useNavigate } from "react-router-dom";
import { axiosInstance } from "../axios/axiosInstance";
import { Form, Input, Button, Upload, message } from 'antd';
import { InboxOutlined } from '@ant-design/icons';
import toast from "react-hot-toast";

const { TextArea } = Input;

export default function Anomalie() {
  const { id } = useParams();
  console.log("id is", id);
  const [projectData, setProjectData] = useState(null);
  const [loading, setLoading] = useState(true);
  const navigate = useNavigate();

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

  const getVulns = async () => {
   
  };

  const onFinish = async(values) => {
    console.log(values);
    try {
      const response = await axiosInstance.post(
        "/get-vuln",
        {
          q: values.q,
          id: id,
        },
        {
          headers: {
            "X-Auth":
              "1986ad8c0a5b3df4d7028d5f3c06e936c1dec77fb40364d109ff9c6b70f27bc4a",
          },
        }
      );
      console.log(response.data);
      if (response.data.success) {
        toast.success(response.data.message);
      } else {
        toast.error("wrong");
      }
    } catch (error) {
      toast.error("something wrong");
    }
  };

  const uploadProps = {
    name: 'file',
    multiple: false,
    action: '/upload', // Specify the URL for file upload
    onChange(info) {
      if (info.file.status === 'done') {
        message.success(`${info.file.name} file uploaded successfully`);
      } else if (info.file.status === 'error') {
        message.error(`${info.file.name} file upload failed.`);
      }
    },
  };

  return (
    <div style={{ width: "80%", marginLeft: "10%" }}>
      <h2>Accunetix & OWASZAP Queries</h2>
      <Form onFinish={onFinish}>
        <Form.Item
          label="Query"
          name="q"
          rules={[{ required: true, message: 'Please enter Form 1 field!' }]}
        >
          <TextArea rows={4} />
        </Form.Item>
        <Form.Item>
          <Button type="primary" htmlType="submit">
            Submit
          </Button>
        </Form.Item>
      </Form>

      {/* File Upload Forms */}
      <h2>File Uploads</h2>

      <Form>
        <Form.Item label="Upload File 1" name="file1">
          <Upload.Dragger {...uploadProps}>
            <p className="ant-upload-drag-icon">
              <InboxOutlined />
            </p>
            <p className="ant-upload-text">Click or drag file to this area to upload</p>
          </Upload.Dragger>
        </Form.Item>
      </Form>

      <Form>
        <Form.Item label="Upload File 2" name="file2">
          <Upload.Dragger {...uploadProps}>
            <p className="ant-upload-drag-icon">
              <InboxOutlined />
            </p>
            <p className="ant-upload-text">Click or drag file to this area to upload</p>
          </Upload.Dragger>
        </Form.Item>
      </Form>

      <Form>
        <Form.Item label="Upload File 3" name="file3">
          <Upload.Dragger {...uploadProps}>
            <p className="ant-upload-drag-icon">
              <InboxOutlined />
            </p>
            <p className="ant-upload-text">Click or drag file to this area to upload</p>
          </Upload.Dragger>
        </Form.Item>
      </Form>
    </div>
  );
}
