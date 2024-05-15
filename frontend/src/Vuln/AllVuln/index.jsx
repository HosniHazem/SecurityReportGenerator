import React, { useEffect, useState } from 'react';
import { useParams ,useNavigate } from 'react-router-dom';
import { axiosInstance } from '../../axios/axiosInstance';
import { Table,Input, Button } from 'antd';
import toast from 'react-hot-toast';

export default function AllVulns() {
  const { id } = useParams();
  const [vulns, setVulns] = useState(null);
  const [attributes, setAttributes] = useState(null);
  const navigate=useNavigate();

  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await axiosInstance.get(`/vuln-by-projectID/${id}`);
        if (response.status === 200) {
          // Sort the data by ID from newest to oldest
          const sortedData = response.data.sort((a, b) => b.id - a.id);
          setVulns(sortedData);
          console.log("Sorted data:", sortedData);
        }
      } catch (error) {
        console.error("Error fetching data:", error);
      }
    };
  
    fetchData();
  }, [id]);
  
  

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

  const handleInputUpdate = async (record, dataIndex, inputValue) => {
    // Implement your update logic here
    console.log('Update record:', record);
    console.log('DataIndex:', dataIndex);
    console.log('New value:', inputValue);
  
    // Update data state
    try {
      const primaryKey = 'id'
        
      const updatedData = vulns.map((row) => {
        if (row[primaryKey] === record[primaryKey]) {
          return { ...row, [dataIndex]: inputValue };
        } else {
          return row;
        }
      });
  
      setVulns(updatedData);
  
      // Log the updated record from updatedData
      const updatedRecord = updatedData.find((row) => row[primaryKey] === record[primaryKey]);
      console.log('Updated record:', updatedData);

      console.log('DataIndex:', dataIndex);
      console.log('New value:', inputValue);
      console.log('Updated value for', updatedRecord[dataIndex]);


      // Send the updated value to the server
      const response = await axiosInstance.post(`/vulns-update/${record.id}`, {
        attribute: dataIndex,
        value: updatedRecord[dataIndex],
      });
      console.log(response.data)
  
      if (response.data.success) {
        toast.success("Value updated successfully");
      } else {
        toast.error(response.data.message);
      }
    } catch (error) {
      toast.error("Error updating value");
      console.log(error);
    }
  };
  
  const handleDelete = async (id) => {
   
    try {
     

      const response = await axiosInstance.delete(`/vulns/${id}`)
  
      if (response.data.success) {
        toast.success("Deleted successfully");
        setVulns((prevData) => prevData.filter((row) => row.id !== id ));


      } else {
        toast.error("Error deleting data 1");
      }
    } catch (error) {
      toast.error("Error deleting data");
      console.log(error);
    }
  
  };

  const handleNavigate=()=>{
    navigate(`/add-vuln/${id}`)

  }


  const columns = attributes
    ? [
        ...attributes
          .filter(attribute =>  attribute.name !== 'ID_Projet')
          .map((attribute) => ({
            title: attribute.name,
            dataIndex: attribute.name,
            key: attribute.name,
            width: 100,

            render: (text, record) => (
              <EditableCell
                record={record}
                dataIndex={attribute.name}
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
            <a onClick={() => handleDelete(record.id)}>Delete</a>
          ),
        },
      ]
    : [];

  return (
    <div>
        <Button type='primary' style={{'width':'20%' ,'marginTop':'3%','marginBottom':'2%'}} onClick={handleNavigate}>Add Vuln</Button>
      <Table
        columns={columns}
        dataSource={vulns}
        pagination={{ pageSize: 10 }} // Adjust page size as needed
      />
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
            style={{ height: `${Math.max(2, Math.ceil(value.length / 10))}rem` ,width:"220px" }}
            value={inputValue}
            autoFocus
            onChange={handleInputChange}
            onBlur={handleInputConfirm}
            onPressEnter={handleInputConfirm}
          />
        ) : (
          <Input
            value={inputValue}
            style={{width:"80px"}}
            autoFocus
            onChange={handleInputChange}
            onBlur={handleInputConfirm}
            onPressEnter={handleInputConfirm}
          />
        )
      ) : (
        <div onClick={toggleEdit} style={{ cursor: "pointer" }}>
          {value !== undefined && value !== null   && value!=="" ? (
            value
          ) : (
            <span style={{ visibility: "hidden" }}>empty</span>
          )}
        </div>
      )}
    </div>
  );
};
