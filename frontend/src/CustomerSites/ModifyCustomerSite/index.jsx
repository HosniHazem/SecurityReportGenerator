import React, { useEffect, useState } from 'react'
import { Form, Input, Button, message, InputNumber } from "antd";
import { useParams } from 'react-router-dom';
import { axiosInstance } from '../../axios/axiosInstance';

export default function ModifyCustomerSite() {
    const { customerSiteId } = useParams();
  const [customerSite, setCustomerSite] = useState(null);


  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await axiosInstance.get(`/customer-sites/${customerSiteId}`);
        if (response.status === 200) {
          setCustomerSite(response.data); // Assuming the response.data contains the customer site details
        } else {
          console.error('Failed to fetch customer site details');
        }
      } catch (error) {
        console.error('Error fetching customer site details:', error);
      }
    };

    fetchData(); // Invoke the fetchData function

    // Cleanup function (optional)
    return () => {
      // Any cleanup logic can go here if needed
    };
  }, [customerSiteId]);
  return (
    <div>
        <div style={{ width: "50%", marginLeft: "40%",marginTop:"10%" }}>
      <Form name="customerSiteForm"  layout="vertical">
      

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
      {/* <MUIButton onClick={handleNavigate}> View all Customer Sites of This Customer</MUIButton> */}
    </div></div>
  )
}
