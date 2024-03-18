import React, { useEffect, useState } from 'react'
import { axiosInstance } from '../../axios/axiosInstance';
import { useNavigate, useParams } from 'react-router-dom';
import { Space, Table, Tag,Input, Button } from 'antd';
import toast from 'react-hot-toast';

export default function AllCustomerSites() {

    const {customerID}=useParams();
    const [customerSites,setCustomerSites]=useState(null);
    const [loading, setLoading] = useState(false);
  const navigate=useNavigate();
    useEffect(() => {
    axiosInstance
      .get(`customer-sites-by-customer-id/${customerID}`)
      .then((response) => {
        if (response.status === 200) {
          setCustomerSites(response.data.data);
          console.log(response.data.data)
        }
      })
      .catch((error) => {
        console.error("Error fetching data:", error);
      });
  }, []);

  
  
  
  
  const handleInputUpdate = (rowId, dataIndex, newValue) => {
    setCustomerSites((customerSites) => {
      const updatedData = customerSites.map((row) => {
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
  };
  

  const saveChanges=async(updatedData)=>{
      console.log("updated",updatedData);

    try {
      const response= await axiosInstance.post(`/customer-sites/${updatedData.ID}`,updatedData)
      console.log(response);
    } catch (error) {
      
    }



  }



  const columns = [
    {
      title: 'Numero_site',
      dataIndex: 'Numero_site',
      key: 'Numero_site',
      render: (text, record) => (
        <EditableCell
          record={record}
          dataIndex="Numero_site"
          value={text}
          handleUpdate={handleInputUpdate}
        />
      ),
    },
    {
      title: 'Structure',
      dataIndex: 'Structure',
      key: 'Structure',
      render: (text, record) => (
        <EditableCell
          record={record}
          dataIndex="Structure"
          value={text}
          handleUpdate={handleInputUpdate}
        />
      ),
    },
    {
      title: 'Lieu',
      dataIndex: 'Lieu',
      key: 'Lieu',
      render: (text, record) => (
        <EditableCell
          record={record}
          dataIndex="Lieu"
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
          <a onClick={() => handleDelete(record.ID)}>Delete</a>
        </Space>
      ),
    },
  ];

  const handleDelete = async (customerSiteId) => {
    try {
      setLoading(true);
      const response = await axiosInstance.delete(`/customer-sites/${customerSiteId}`);
      
      if (response.status === 200) {
        // Delete was successful, update the state
        setCustomerSites((prevCustomerSites) =>
          prevCustomerSites.filter((site) => site.ID !== customerSiteId)
        );
        toast.success("Customer site deleted successfully");
      } else {
        // Handle other status codes if needed
        toast.error("Failed to delete customer site");
      }
    } catch (error) {
      console.error("Error deleting customer site:", error);
      toast.error("An error occurred while deleting customer site");
    } finally {
      setLoading(false);
    }
  };
  const handleEdit=(customerSiteID)=>{

    navigate (`/customer-sites-modify/${customerSiteID}`)
  }
const handleNavigate=()=>{

  navigate(`/sites/${customerID}`)
}



  return (
    <div>
        <Table dataSource={customerSites} columns={columns}/>
        <Button type='primary' onClick={handleNavigate}>Add a new customer site for this customer ? </Button>
    </div>
  )
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
