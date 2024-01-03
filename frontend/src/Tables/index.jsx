import React, { useState, useEffect } from 'react';
import { axiosInstance } from '../axios/axiosInstance';
import { Select, Table, Modal, Input, Button } from 'antd';
import './index.css';

const { Option } = Select;

export default function TablesClone() {
  const [tables, setTables] = useState(null);
  const [selectedTable, setSelectedTable] = useState(null);
  const [attributes, setAttributes] = useState(null);
  const [data, setData] = useState(null);
  const [inputValue, setInputValue] = useState('');
  const [isModalVisible, setIsModalVisible] = useState(false);

  useEffect(() => {
    const fetchTables = async () => {
      try {
        const response = await axiosInstance.get(`all-tables`);
        setTables(response.data);
      } catch (error) {
        console.error('Error fetching project data:', error);
      }
    };

    fetchTables();
  }, []);

  useEffect(() => {
    console.log('attributes are', attributes);
  }, [attributes]);

  const displayAttributes = async (selectedTab) => {
    try {
      const response = await axiosInstance.post('all-attributes', {
        name: selectedTab,
      });

      setData(response.data.data);
      setAttributes(response.data.attributes);

    } catch (error) {
      console.error('Error fetching attributes data:', error);
    }
  };

  const handleTableChange = (value) => {
    setSelectedTable(value);
    displayAttributes(value);
  };

  const handleInputChange = (e) => {
    setInputValue(e.target.value);
  };

  const showModal = () => {
    setIsModalVisible(true);
  };

  const handleModalSubmit = () => {
    // Send the value to handleInput function
    handleInput(inputValue);

    // Close the modal
    setIsModalVisible(false);
  };

  const handleInput = (value) => {
    // Handle the input value, you can send it to the server or perform any other actions
    console.log('Input value:', value);
  };

  const columns = attributes
    ? attributes.map((key) => ({
        title: key,
        dataIndex: key,
        key,
      }))
    : [];

  return (
    <div>
      <Select
        style={{ width: '100%' }}
        placeholder="Select table"
        onChange={handleTableChange}
        value={selectedTable}
      >
        {tables &&
          tables.map((tableName) => (
            <Option key={tableName} value={tableName}>
              {tableName}
            </Option>
          ))}
      </Select>
      {attributes && (
        <Table
          dataSource={data}
          columns={columns}
          pagination={false}
          onRow={(record, rowIndex) => {
            return {
              onClick: () => {
                // Open the modal when a row is clicked
                showModal();
              },
            };
          }}
        />
      )}

      <Modal
        title="Enter Value"
        visible={isModalVisible}
        onCancel={() => setIsModalVisible(false)}
        footer={[
          <Button key="submit" type="primary" onClick={handleModalSubmit}>
            Submit
          </Button>,
        ]}
      >
        <Input
          value={inputValue}
          onChange={handleInputChange}
          placeholder="Enter something..."
        />
      </Modal>
    </div>
  );
}
