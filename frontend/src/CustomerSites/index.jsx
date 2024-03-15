import React, { useState, useEffect } from "react";
import { axiosInstance } from "../axios/axiosInstance";
import { Form, Input, Button, message, InputNumber } from "antd";
import { useNavigate, useParams } from "react-router-dom";
import { Button as MUIButton } from "@mui/material";

export default function CreateCustomerSite() {
  const { id } = useParams();
  const [project, setProject] = useState(null);
  const navigate=useNavigate();
  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await axiosInstance.get(`/Project/${id}/show`);
        console.log("project",response.data.Project);

        if (response.status === 200) {
          setProject(response.data.Project);
          console.log(response.data.Project);
        }
      } catch (error) {
        console.error("Error fetching data:", error);
      }
    };

    // Invoke the fetchData function
    fetchData();
  }, [id]); // Ensure that 'id' and 'axiosInstance' are added to the dependency array

  const onFinish = async (values) => {
    console.log(values);
    // console.log(project.customer_id);
    console.log("Numero Site:", values.Numero_site);
    console.log("Structure:", values.Structure);
    console.log("Lieu:", values.Lieu);

    try {
      // Send a POST request to your backend API
      const response = await axiosInstance.post("add-customersite", {
        Customer_ID: id,
        Numero_site: values.Numero_site,
        Structure: values.Structure,
        Lieu: values.Lieu,
      });
      console.log("Response:", response.data);

      // Check if the request was successful
      if (response.data.success) {
        message.success("Customer site created successfully");
        navigate('/');
        
      } else {
        message.error("Failed to create customer site");
      }
    } catch (error) {
      console.error("Error creating customer site:", error);
      message.error("An error occurred while creating the customer site");
    }
  };
 
    const handleNavigate=()=>{
      navigate(`customer-sites/${id}`)
    }
  return (
    <div style={{ width: "50%", marginLeft: "40%",marginTop:"10%" }}>
      {" "}
      <Form name="customerSiteForm" onFinish={onFinish} layout="vertical">
        {/* <Form.Item
          label="Customer ID"
          name="Customer_ID"
          rules={[{ required: true, message: "Please enter Customer ID" }]}
        >
          <Input />
        </Form.Item> */}

        <Form.Item
          label="Numero Site"
          name="Numero_site"
          rules={[
            {
              required: true,
              type: "number",
              message: "Please enter Numero Site",
            },
          ]}
        >
          <InputNumber           style={{ width: "50%" }}
 />
        </Form.Item>

        <Form.Item
          label="Structure"
          name="Structure"
          rules={[{ required: true, message: "Please enter Structure" }]}
          style={{ width: "50%" }}
        >
          <Input />
        </Form.Item>

        <Form.Item
          label="Lieu"
          name="Lieu"
          rules={[{ required: true, message: "Please enter Lieu" }]}
          style={{ width: "50%" }}
        >
          <Input />
        </Form.Item>

        <Form.Item >
          <Button type="primary" htmlType="submit">
            Create Customer Site
          </Button>
        </Form.Item>
      </Form>
      <MUIButton onClick={handleNavigate}> View all Customer Sites of This Customer</MUIButton>
    </div>
  );
}
