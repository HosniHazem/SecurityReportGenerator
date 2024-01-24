import React, { useState, useEffect } from "react";
import { axiosInstance } from "../axios/axiosInstance";
import { Select, Table, Modal, Input, Button } from "antd";
import "./index.css";
import toast from "react-hot-toast";

const { Option } = Select;

export default function TablesClone() {
  const [tables, setTables] = useState(null);
  const [selectedTable, setSelectedTable] = useState(null);
  const [attributes, setAttributes] = useState(null);
  const [data, setData] = useState(null);
  const [inputValue, setInputValue] = useState("");
  const [isModalVisible, setIsModalVisible] = useState(false);
  const [selectedRow, setSelectedRow] = useState(null);
  const [selectedAttribute, setSelectedAttribute] = useState(null);

  useEffect(() => {
    const fetchTables = async () => {
      try {
        const response = await axiosInstance.get(`all-tables`);
        setTables(response.data);
      } catch (error) {
        console.error("Error fetching project data:", error);
      }
    };

    fetchTables();
  }, []);

  useEffect(() => {
    console.log("attributes are", attributes);
  }, [attributes]);

  const displayAttributes = async (selectedTab) => {
    try {
      const response = await axiosInstance.post("all-attributes", {
        name: selectedTab,
      });

      setData(response.data.data);
      setAttributes(response.data.attributes);
    } catch (error) {
      console.error("Error fetching attributes data:", error);
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

  const handleModalSubmit = async () => {
    // Send the value to handleInput function
    const rowData = {
      value: inputValue,
      attributeName: selectedAttribute,
      tableName: selectedTable,
      rowId: selectedRow.id ? selectedRow.id : selectedRow.ID,
    };

    try {
      const response = await axiosInstance.put("/modify", {
        name: rowData.tableName,
        attribute: rowData.attributeName,
        value: rowData.value,
        id: rowData.rowId,
      });
      if (response.data.success) {
        toast.success("done");
      } else {
        toast.error("error 1");
      }
    } catch (error) {
      toast.error("error 2");
      console.log(error);
    }

    // Close the modal
    setIsModalVisible(false);
  };

  const handleInput = (value) => {
    // Handle the input value, you can send it to the server or perform any other actions
    console.log("Input value:", value);
  };

  const columns = attributes
    ? attributes.map((key) => ({
        title: key,
        dataIndex: key,
        key,
      }))
    : [];
  console.log("columns", columns);

  return (
    <div>
      <Select
        style={{ width: "60%", marginBottom: "2%" }}
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
          pagination={true}
          columns={columns.map((col) => ({
            ...col,
            onCell: (record) => ({
              record,
              dataIndex: col.dataIndex,
              title: col.title,
              onClick: () => {
                console.log("record is ", record);
                console.log("tilte", col.title);
                setSelectedAttribute(col.title);
              },
            }),
          }))}
          onRow={(record, rowIndex) => {
            return {
              onClick: () => {
                setSelectedRow(record);
                showModal();
              },
            };
          }}
        />
      )}

      <Modal
        title={`Enter new ${selectedAttribute} value for row ID ${
          selectedRow?.id || selectedRow?.ID
        }`}
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
          placeholder={`Enter new ${selectedAttribute} value...`}
        />
      </Modal>
    </div>
  );
}
