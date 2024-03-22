import React, { useState } from 'react';
import { useParams } from 'react-router-dom';
import { Form, Input, Button, message } from 'antd';
import { axiosInstance } from '../../axios/axiosInstance';

export default function AddRmProccess() {
    const { idIteration } = useParams();
    const [loading, setLoading] = useState(false);

    const onFinish = async (values) => {
        setLoading(true);
        console.log(values);
        const token = localStorage.getItem('token'); // Retrieve the token from local storage

        try {
            const response = await axiosInstance.post('/rm-processus-domains', { ...values, 
                ID_ITERATION:idIteration }, {
                headers: {
                    Authorization: `Bearer ${token}` // Set the token in the request header
                }
            });
            console.log(response.data);
            message.success('RM Process added successfully');
            // You can redirect the user or perform any other action upon successful submission
        } catch (error) {
            console.error('Error adding RM Process:', error);
            message.error('Failed to add RM Process');
        }
        setLoading(false);
    };

    return (
        <div style={{ width: "50%", marginLeft: "20%",marginTop:"10%" }}>
        <h1>Add RM Process</h1>
            <Form
                name="addRMProcessForm"
                onFinish={onFinish}
                layout="vertical"
            >
                <Form.Item
                    name="Processus_domaine"
                    label="Processus domaine"
                    rules={[{ required: true, message: 'Please enter the name' }]}
                >
                    <Input />
                </Form.Item>
                <Form.Item
                    name="Description"
                    label="Description"
                    rules={[{ required: true, message: 'Please enter the description' }]}
                >
                    <Input.TextArea />
                </Form.Item>
                <Form.Item>
                    <Button type="primary" htmlType="submit" loading={loading}>
                        Submit
                    </Button>
                </Form.Item>
            </Form>
        </div>
    );
}
