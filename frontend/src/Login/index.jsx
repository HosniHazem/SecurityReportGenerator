import React from 'react'
import { Form, Input, Button, message, Card } from 'antd';
import axios from 'axios';
import { axiosInstance } from '../axios/axiosInstance';
import toast from 'react-hot-toast';
import { useNavigate } from "react-router-dom";
import img from '../img/logo.png'

export default function Login() {
    const [form] = Form.useForm();
    const navigate=useNavigate();

    const handleNavigation=()=>{
        navigate('/register');
    }

    const onFinish = async (values) => {
      const {  email, password } = values;
  
      try {
        // Make API request to register user
        const response = await axiosInstance.post('auth/login', {
          email,
          password,
        });
  console.log(response.data);
        if(response.data.success){
            toast.success('login with success');
            localStorage.setItem("token",response.data.access_token);
            navigate('/')
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
        <div style={{ width: "30%", margin: "0 auto" ,marginTop:"5%" ,borderRadius:'2px',}}>
          <Card>
            <img src={img} style={{width:"50%" ,marginLeft:"25%"}} />
            <h1 style={{width:"100%" ,marginLeft:"8%"}} > Welcome to webapp2</h1>
          <Form form={form} onFinish={onFinish} layout="vertical">
       
  
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
 
       {/* <Form.Item
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
       </Form.Item> */}
 
       <Form.Item>
         <Button type="primary" htmlType="submit" style={{width:"100%"}}>
           Login
         </Button>
       </Form.Item>
     </Form>

          </Card>
      
      </div>
    );
}
