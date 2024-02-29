import React from 'react'
import { Form, Input, Button, message } from 'antd';
import axios from 'axios';
import { axiosInstance } from '../axios/axiosInstance';
import toast from 'react-hot-toast';


export default function Register() {
    const [form] = Form.useForm();

    const onFinish = async (values) => {
      const { firstName, lastName, email, password } = values;
      const name = `${firstName} ${lastName}`;
  
      try {
        // Make API request to register user
        const response = await axiosInstance.post('auth/register', {
          name,
          email,
          password,
          password_confirmation: password,
        });
  console.log(response.data);
        if(response.data.success){
            toast.success('registered with success')
        }
        else {
            toast.error(response.data.message);
        }

      } catch (error) {
        // Handle registration error
        console.error('Registration failed:', error.response.data);
        message.error('Registration failed');
      }
    };
  
    return (
        <div style={{ width: "50%", margin: "0 auto" ,marginTop:"2%"}}>
        <Form form={form} onFinish={onFinish} layout="vertical">
        <Form.Item
          name="firstName"
          label="First Name"
          rules={[{ required: true, message: 'Please enter your first name' }]}
        >
          <Input placeholder="Enter your first name" />
        </Form.Item>
  
        <Form.Item
          name="lastName"
          label="Last Name"
          rules={[{ required: true, message: 'Please enter your last name' }]}
        >
          <Input placeholder="Enter your last name" />
        </Form.Item>
  
        <Form.Item
          name="email"
          label="Email"
          rules={[
            { required: true, message: 'Please enter your email' },
            { type: 'email', message: 'Please enter a valid email address' },
          ]}
        >
          <Input placeholder="Enter your email" />
        </Form.Item>
  
        <Form.Item
          name="password"
          label="Password"
          rules={[{ required: true, message: 'Please enter your password' }]}
        >
          <Input.Password placeholder="Enter your password" />
        </Form.Item>
  
        <Form.Item
          name="confirmPassword"
          label="Confirm Password"
          dependencies={['password']}
          rules={[
            { required: true, message: 'Please confirm your password' },
            ({ getFieldValue }) => ({
              validator(_, value) {
                if (!value || getFieldValue('password') === value) {
                  return Promise.resolve();
                }
                return Promise.reject('The passwords do not match');
              },
            }),
          ]}
        >
          <Input.Password placeholder="Confirm your password" />
        </Form.Item>
  
        <Form.Item>
          <Button type="primary" htmlType="submit" style={{width:"100%"}}>
            Register
          </Button>
        </Form.Item>
      </Form>
      <p> Already have an account ?</p>
      </div>
    );
}
