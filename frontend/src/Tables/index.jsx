import React, { useState, useEffect } from "react";
import { axiosInstance } from "../axios/axiosInstance";
import { Select, Table, Input, Button } from "antd";
import "./index.css";
import toast from "react-hot-toast";

const { Option } = Select;

export default function TablesClone() {
  const [tables, setTables] = useState(null);
  const [selectedTable, setSelectedTable] = useState(null);
  const [attributes, setAttributes] = useState(null);
  const [data, setData] = useState(null);
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

  const handleInputUpdate = async (record, dataIndex, inputValue) => {
    const updatedData = data.map((row) => {
      const primaryKey = Object.keys(row).find((key) => key.toLowerCase() === 'id');
      
      if (row[primaryKey] === record[primaryKey]) {
        return { ...row, [dataIndex]: inputValue };
      } else {
        return row;
      }
    });
  
    // Update data state
    setData(updatedData);
  
    // Send the updated value to the server
    try {
      const response = await axiosInstance.put("/modify", {
        name: selectedTable,
        attribute: dataIndex,
        value: inputValue,
        id: record.id,
      });
  
      // if (response.data.success) {
      //   toast.success("Value updated successfully");
      // } else {
      //   toast.error("Error updating value");
      // }
    } catch (error) {
      toast.error("Error updating value");
      console.log(error);
    }
  };
  

  const handleDelete = async (id) => {
    const dataToDelete = {
      name: selectedTable,
      id:id
    };
    try {
      console.log("id is",id)
    console.log("selected table is",selectedTable
    )

      const response = await axiosInstance.delete("delete-row", {
        data:dataToDelete
      });
  
      if (response.data.success) {
        toast.success("Deleted successfully");
        setData((prevData) => prevData.filter((row) => row.id !== id && row.ID !== id));


      } else {
        toast.error("Error deleting data 1");
      }
    } catch (error) {
      toast.error("Error deleting data");
      console.log(error);
    }
  
  };


  const columns = attributes
  ? [
      ...attributes.map((key) => ({
        title: key,
        dataIndex: key,
        key,
        render: (text, record) => (
          <EditableCell
            record={record}
            dataIndex={key}
            value={text}
            handleUpdate={handleInputUpdate}
          />
        ),
      })),
      {
        title: 'Action',
        key: 'operation',
        fixed: 'right',
        width: 100,
        render: (text, record) => (

          <a onClick={() => handleDelete(record.id || record.ID)}>Delete</a>
        ),
      },
    ]
  : [];

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
          columns={columns}
          onRow={(record, rowIndex) => {
            return {
              onClick: () => {
                setSelectedRow(record);
                setSelectedAttribute(columns[0].dataIndex); // Assuming the first attribute is used for selection
              },
            };
          }}
        />
      )}
    </div>
  );
}

const EditableCell = ({ value, record, dataIndex, handleUpdate }) => {
  const [isEditing, setIsEditing] = useState(false);
  const [inputValue, setInputValue] = useState(value);

  const toggleEdit = () => {
    setIsEditing(!isEditing);
    setInputValue(value);
  };

  const handleInputChange = (e) => {
    setInputValue(e.target.value);
  };

  const handleInputConfirm = () => {
    if (isEditing) {
      setIsEditing(false);
      if (inputValue !== value) {
        handleUpdate(record, dataIndex, inputValue);
      }
    }
  };

  useEffect(() => {
    if (!isEditing) {
      setInputValue(value);
    }
  }, [isEditing, value]);

  const isTextArea = value && value.length > 40;

  return (
    <div>
      {isEditing ? (
        isTextArea ? (
          <textarea
            className="editable-cell-textarea"
            style={{ height: `${Math.max(2, Math.ceil(value.length / 10))}rem` }}
            value={inputValue}
            autoFocus
            onChange={handleInputChange}
            onBlur={handleInputConfirm}
            onPressEnter={handleInputConfirm}
          />
        ) : (
          <Input
            value={inputValue}
            autoFocus
            onChange={handleInputChange}
            onBlur={handleInputConfirm}
            onPressEnter={handleInputConfirm}
          />
        )
      ) : (
        <div onClick={toggleEdit} style={{ cursor: "pointer" }}>
          {value !== undefined && value !== null ? (
            value
          ) : (
            <span style={{ visibility: "hidden" }}>empty</span>
          )}
        </div>
      )}
    </div>
  );
};





