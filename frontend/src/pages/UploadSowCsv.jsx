import React, { useState } from "react";
import { Upload, message, Form, Button } from "antd";
import { InboxOutlined } from "@ant-design/icons";
import axios from "axios"; // Import axios
import { axiosInstance } from "../axios/axiosInstance";
import image from '../img/image.png'
import { useNavigate } from "react-router-dom";
const { Dragger } = Upload;

export default function UploadSowCsv({ projectId }) {
  const token = localStorage.getItem("token");
  const [form] = Form.useForm();
  const [file, setFile] = useState(null);
const navigate=useNavigate()
  const onFinish = async () => {
    try {
      const formData = new FormData();
      formData.append("csv_file", file);

      const response = await axiosInstance.post(
        `/insert-sow/${projectId}`,
        formData,
        {
          headers: {
            Authorization: `Bearer ${token}`,
            "Content-Type": "multipart/form-data",
          },
        }
      );
      // message.success(`${file.name} file uploaded successfully`);
      if(response.data.success){
         message.success(response.data.message);
          navigate(`/view-sow/${projectId}`)
      }
      else {
        message.error(response.data.message)
      }

    } catch (error) {
      message.error(`${file.name} file upload failed.`);
    }
  };

  const normFile = (e) => {
    if (Array.isArray(e)) {
      return e;
    }
    return e && e.fileList;
  };

  return (
    <div>
      {/* Display the image */}
      <img src={image} alt="Example CSV" style={{ maxWidth: "100%" }} />
      <p> The Csv File should be on this Form ,Other Structures won't be saved 
        P.S:Column 1:Type (Apps , Ext,R_S,PC)  </p>


      <Form form={form} onFinish={onFinish} initialValues={{ remember: true }}>
        <Form.Item name="upload" valuePropName="fileList" getValueFromEvent={normFile}>
          <Dragger
            name="file"
            action=""
            accept=".csv"
            multiple={false}
            beforeUpload={(file) => {
              setFile(file);
              return false;
            }}
          >
            <p className="ant-upload-drag-icon">
              <InboxOutlined />
            </p>
            <p className="ant-upload-text">Click or drag CSV file to upload</p>
          </Dragger>
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
