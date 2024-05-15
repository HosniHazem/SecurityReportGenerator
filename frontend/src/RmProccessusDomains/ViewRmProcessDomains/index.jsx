import React, { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import { Table, Button, Space, Popconfirm, message,Input } from 'antd';
import { axiosInstance } from '../../axios/axiosInstance';

export default function AllRmProcess() {
    const { idIteration } = useParams();
    const [rmProccessDomains, setRmProccessDomains] = useState([]);
    const navigate=useNavigate();
    useEffect(() => {
        axiosInstance
            .get(`/rm-processus-domains/getRmProccessByIterationID/${idIteration}`)
            .then((response) => {
                if (response.status === 200) {
                    console.log("response", response.data);
                              const sortedData = response.data.sort((a, b) => b.ID - a.ID);

                    setRmProccessDomains(sortedData);
                }
            })
            .catch((error) => {
                console.error("Error fetching data:", error);
            });
    }, [idIteration]);

    const handleDelete = async (record) => {
      const token = localStorage.getItem('token'); // Retrieve the token from local storage
  
      try {
          const response = await axiosInstance.delete(`/rm-processus-domains/${record.ID}`, {
              headers: {
                  Authorization: `Bearer ${token}` // Set the token in the request header
              }
          });
          console.log(response.data);
          if (response.status === 200) {
              message.success('Record deleted successfully');
              // Refresh the data after deletion
              const newData = rmProccessDomains.filter(item => item.ID !== record.ID);
              setRmProccessDomains(newData);
          }
      } catch (error) {
          console.error("Error deleting record:", error);
          message.error('Failed to delete record');
      }
  };
  

    const handleInputUpdate = (rowId, dataIndex, newValue) => {

      setRmProccessDomains((rmProccessDomains) => {
        const updatedData =rmProccessDomains.map((row) => {
          if (row.ID === rowId) {
            const updatedRow = {
              ...row,
              [dataIndex]: newValue,
            };
            saveChanges(updatedRow); // Call saveChanges with the updated row
            return updatedRow;
          }
          return row; // Return the unchanged row
        });
        console.log("Updated Data:", updatedData); // Log the entire updated data
        return updatedData;
      });


    }

    const saveChanges=async(updatedData)=>{
      console.log("updated",updatedData);

    try {
      const response= await axiosInstance.post(`/update-rm-processus-domains/${updatedData.ID}`,updatedData)
      console.log(response);
    } catch (error) {
      
    }
  }




    const columns = [
        
     
        {
            title: 'Processus_domaine',
            dataIndex: 'Processus_domaine',
            key: 'Processus_domaine',
            render: (text, record) => (
              <EditableCell
                record={record}
                dataIndex="Processus_domaine"
                value={text}
                handleUpdate={handleInputUpdate}
              />
            ),
        },
        {
            title: 'Description',
            dataIndex: 'Description',
            key: 'Description',
            render: (text, record) => (
              <EditableCell
                record={record}
                dataIndex="Description"
                value={text}
                handleUpdate={handleInputUpdate}
              />
            ),
        },
        {
            title: 'Action',
            key: 'action',
            render: (text, record) => (
                <Space size="middle">
                    <Popconfirm
                        title="Are you sure you want to delete this record?"
                        onConfirm={() => handleDelete(record)}
                        okText="Yes"
                        cancelText="No"
                    >
                        <Button type="danger">Delete</Button>
                    </Popconfirm>
                </Space>
            ),
        },
    ];
    const handleNavigate=()=>{
      navigate(`/add-rm-proccessus/${idIteration}`)
    }

    return (
        <div>
            <h1>Rm Processus Domains</h1>

            <Table columns={columns} dataSource={rmProccessDomains} />
            <Button type='primary' onClick={handleNavigate}> Add  Rm Proccessus Domain</Button>
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