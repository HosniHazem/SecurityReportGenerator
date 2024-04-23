import React, { useState, useEffect } from 'react';
import { axiosInstance } from '../axios/axiosInstance';
import { useParams } from 'react-router-dom';
import { Input, Table, Typography } from 'antd';
import toast from 'react-hot-toast';

const { Title } = Typography;

export default function AllSow() {
  const [sowData, setSowData] = useState([]);
  const { id } = useParams();
  const [editableCell, setEditableCell] = useState({
    rowIndex: -1,
    colIndex: -1,
    id: null,
  });
  const fetchSowData = async () => {
    try {
      const response = await axiosInstance.get(`/sow-by-projectID/${id}`);
      setSowData(response.data); 
    } catch (error) {
      console.error('Error fetching SOW data:', error);
    }
  };

  useEffect(() => {
    fetchSowData();
  }, []);


  const handleInputUpdate = (rowId, dataIndex, newValue) => {
    setSowData((sow) => {
      const updatedData = sow.map((row) => {
        if (row.ID === rowId) {
          const updatedRow = {
            ...row,
            [dataIndex]: newValue,
          };
          saveChanges(updatedRow); // Call saveChanges with the updated row
        console.log("update",updatedRow)

          return updatedRow;
        }
        return row; // Return the unchanged row
      });
      console.log("Updated Data:", updatedData); // Log the entire updated data
      return updatedData;
    });
  };
  const saveChanges=async(updatedData)=>{
    console.log("updated",updatedData);

  try {
    const response= await axiosInstance.post(`/sow-by-projectID/${updatedData.ID}`,updatedData)
    console.log(response);
  } catch (error) {
    
  }



}
const handleDelete = async (sowId) => {
    try {
        console.log(sowId)
      const token = localStorage.getItem('token'); // Retrieve the token from local storage
      const response = await axiosInstance.delete(`delete-sow/${sowId}`, {
        headers: {
          Authorization: `Bearer ${token}`, // Include the token in the request headers
        },
      });
      console.log(response.data); // Optionally, you can log the response or handle it as needed
    } catch (error) {
      console.error('Error deleting SOW:', error);
    }
  };
  

const columns = [
    {
      title: 'Name',
      dataIndex: 'Nom',
      key: 'nom',
    },
    {
      title: 'IP Host',
      dataIndex: 'IP_Host',
      key: 'ipHost',
    },
    {
      title: 'Field 3',
      dataIndex: 'field3',
      key: 'field3',
    },
    {
      title: 'Field 4',
      dataIndex: 'field4',
      key: 'field4',
    },
    {
      title: 'Field 5',
      dataIndex: 'field5',
      key: 'field5',
    },
    {
      title: 'Dev By',
      dataIndex: 'dev_by',
      key: 'devBy',
    },
    {
      title: 'URL',
      dataIndex: 'URL',
      key: 'url',
    },
    {
      title: 'Number of Users',
      dataIndex: 'Number_users',
      key: 'numberOfUsers',
    },
    {
      title: 'Action',
      dataIndex: 'action',
      key: 'action',
      render: (text, record) => (
        <button onClick={() => handleDelete(record.ID)}>Delete</button>
      ),
    },
  ];
  
  

  return (
    <div>
      <Title level={2}>All Sow</Title>
      <Table
        dataSource={sowData}
        columns={columns}
        rowKey="id"
      />
    </div>
  );
}
const EditableCell = ({ value, record, dataIndex, handleUpdate }) => {
    const [isEditing, setIsEditing] = useState(false);
    const [inputValue, setInputValue] = useState(value); // Initialize inputValue with the current value
  
    const toggleEdit = () => {
      // Skip editing if dataIndex is 'id' or 'ID'
      if (dataIndex.toLowerCase() === "id") {
        return;
      }
  
      setIsEditing(!isEditing); // Toggle between true and false
      setInputValue(value); // Set input to the current value, even if it's an empty string
    };
  
    const handleInputChange = (e) => {
      setInputValue(e.target.value);
    };
  
    // Inside the EditableCell component
    const handleInputConfirm = () => {
      if (isEditing) {
        // Only call update if value has changed
        if (inputValue !== value) {
          handleUpdate(record.ID, dataIndex, inputValue); // Pass the row ID, dataIndex, and newValue
        }
        setIsEditing(false);
      }
    };
    
    
  
    useEffect(() => {
      // When isEditing becomes false, reset the inputValue
      // This handles the case when editing is canceled
      if (!isEditing) {
        setInputValue(value);
      }
    }, [isEditing, value]);
  
    return (
      <div>
        {isEditing ? (
          <Input
            value={inputValue}
            autoFocus // Automatically focus the input when editing starts
            onChange={handleInputChange}
            onBlur={handleInputConfirm}
            onPressEnter={handleInputConfirm}
          />
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
