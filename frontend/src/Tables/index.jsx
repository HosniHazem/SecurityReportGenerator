import React, { useState, useEffect } from 'react';
import { axiosInstance } from '../axios/axiosInstance';
import { Select, Table } from 'antd';
import "./index.css"
const { Option } = Select;

export default function TablesClone() {
  const [tables, setTables] = useState(null);
  const [selectedTable, setSelectedTable] = useState(null);
  const [attributes, setAttributes] = useState(null);
  const [data,setData]=useState(null);
  useEffect(() => {
    const fetchTables = async () => {
      try {
        const response = await axiosInstance.get(`all-tables`);
        setTables(response.data);
      } catch (error) {
        console.error("Error fetching project data:", error);
        // Handle error, for example, redirect to an error page
      }
    };

    fetchTables();
  }, []);

  useEffect(() => {
    console.log("attributes are", attributes);
  }, [attributes]); // Log the state whenever 'attributes' changes

  const displayAttributes = async (selectedTab) => {
    try {
      const response = await axiosInstance.post('all-attributes', {
        name: selectedTab,
      });

      console.log("response is",response.data.data);
      setData(response.data.data);
      console.log("data is",data);

      setAttributes(response.data.attributes);

    } catch (error) {
      console.error("Error fetching attributes data:", error);
      // Handle error, for example, show an error message
    }
  };

  const handleTableChange = (value) => {
    setSelectedTable(value);
    displayAttributes(value);
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
        style={{ width: "100%" }}
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
          dataSource={data} // Wrap attributes in an array to create a single-row table
          columns={columns}
          pagination={false}
        />
      )}
     
    </div>
  );
}
