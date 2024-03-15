import React, { useEffect, useState } from 'react'
import { axiosInstance } from '../../axios/axiosInstance';
import { useParams } from 'react-router-dom';
import { Space, Table, Tag } from 'antd';
import toast from 'react-hot-toast';

export default function AllCustomerSites() {

    const {customerID}=useParams();
    const [customerSites,setCustomerSites]=useState(null);
    const [loading, setLoading] = useState(false);

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

  const columns = [
    {
      title: 'Numero_site',
      dataIndex: 'Numero_site',
      key: 'Numero_site',
    },
    {
      title: 'Structure',
      dataIndex: 'Structure',
      key: 'Structure',
    },
    {
      title: 'Lieu',
      dataIndex: 'Lieu',
      key: 'Lieu',
    },
    {
      title: 'Action',
      key: 'action',
      render: (text, record) => (
        <Space size="middle">
          <a onClick={() => handleEdit(record)}>Edit</a>
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




  return (
    <div>
        <Table dataSource={customerSites} columns={columns}/>
    </div>
  )
}
