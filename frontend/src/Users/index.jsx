import React, { useState } from 'react';
import { Form, Input, Button, message, Card } from 'antd';
import axios from 'axios';
import { axiosInstance } from '../axios/axiosInstance';
import toast from 'react-hot-toast';

export default function CreateUser() {
    
    const [loading, setLoading] = useState(false);

    const onFinish = async (values) => {
      setLoading(true);
      try {
        const response = await axiosInstance.post('/create-user', values);
        console.log(response)
        if(response.data.success){
            toast.success(response.data.message)
        }
        else {
            toast.error(response.data.message)
        }
      } catch (error) {
        message.error(error.response.data.message);
      }
      setLoading(false);
    };
  
    return (
        <div style={{ width: "30%", margin: "0 auto" ,marginTop:"5%" ,borderRadius:'2px',}}>
            <Card>
                <h1> Create a new user </h1>
      <Form
        name="createUserForm"
        layout="vertical"
        onFinish={onFinish}
      >
        <Form.Item
          name="email"
          label="Email"
          rules={[
            { required: true, message: 'Please input email!' },
            { type: 'email', message: 'Please enter a valid email address!' },
          ]}
        >
          <Input />
        </Form.Item>
  
        <Form.Item
          name="name"
          label="Name"
          rules={[{ required: true, message: 'Please input name!' }]}
        >
          <Input />
        </Form.Item>
  
        <Form.Item>
          <Button type="primary" htmlType="submit" loading={loading}>
            Create User
          </Button>
        </Form.Item>
      </Form>
      </Card>
      </div>
    );
  };
