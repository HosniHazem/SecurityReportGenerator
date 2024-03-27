import React, { useEffect, useState } from 'react';
import { axiosInstance } from '../../axios/axiosInstance';
import { useNavigate, useParams } from 'react-router-dom';
import { Form, Input, Button, DatePicker, InputNumber, Row, Col } from 'antd';
import toast from 'react-hot-toast';

const { Item } = Form;

export default function AddVuln() {
    const { id } = useParams();
    const [attributes, setAttributes] = useState(null);
    const navigate=useNavigate();
    useEffect(() => {
        axiosInstance
            .get(`/vuln-attributes`)
            .then((response) => {
                if (response.status === 200) {
                    setAttributes(response.data);
                }
            })
            .catch((error) => {
                console.error("Error fetching data:", error);
            });
    }, []);

    const handleSubmit = async (values) => {
        // Filter out empty or null values from the form data
        const formData = Object.keys(values).reduce((acc, key) => {
            if (values[key] !== undefined && values[key] !== '') {
                acc[key] = values[key];
            }
            return acc;
        }, {});
    
        // Add logic to handle form submission
        formData.ID_Projet = id;
    
        try {
            const response = await axiosInstance.post('/vulns', formData);
            console.log(response.data);
            if (response.data.success) {
                toast.success(response.data.message);
                navigate(-1);
            }
        } catch (error) {
            // Extract the error message from the error object
            const errorMessage = error.response ? error.response.data.message : 'An error occurred';
            toast.error(errorMessage);
        }
    };
    
    

    return (
        <div>
            <h2>Add Vulnerability</h2>
            <Form layout="vertical" onFinish={handleSubmit}>
                {attributes && (
                    <Row gutter={16}>
                        {attributes
                            .filter(attribute => !['id', 'Plugin ID', 'ID_Projet'].includes(attribute.name))
                            .map((attribute, index) => (
                                <Col span={12} key={index}>
                                    <Item
                                        label={attribute.name}
                                        name={attribute.name}
                                    >
                                        {renderInput(attribute)}
                                    </Item>
                                </Col>
                            ))}
                    </Row>
                )}
                <Item>
                    <Button type="primary" htmlType="submit">
                        Submit
                    </Button>
                </Item>
            </Form>
        </div>
    );
}

// Function to render input fields based on attribute type
const renderInput = (attribute) => {
    switch (attribute.type) {
        case 'string':
            return <Input />;
        case 'float':
            return <InputNumber />;
        case 'integer':
            return <InputNumber />;

        case 'date':
            return <DatePicker />;
        // Add more cases for other attribute types as needed
        default:
            return null;
    }
};
