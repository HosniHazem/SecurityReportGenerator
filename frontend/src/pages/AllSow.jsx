import React, { useState, useEffect } from "react";
import { axiosInstance } from "../axios/axiosInstance";
import { useNavigate, useParams } from "react-router-dom";
import { Button, Input, Select, Table, Typography } from "antd";
import toast from "react-hot-toast";

const { Title } = Typography;
const { Option } = Select;

export default function AllSow() {
  const [sowData, setSowData] = useState([]);
  const { id } = useParams();
  const [editableCell, setEditableCell] = useState({
    rowIndex: -1,
    colIndex: -1,
    id: null,
  });
  const [filterType, setFilterType] = useState("");
  const [searchInput, setSearchInput] = useState("");
  const navigate=useNavigate();

  useEffect(() => {
    const fetchFilteredSowData = async () => {
      try {
        const response = await axiosInstance.get(`/sow-by-projectID/${id}`);
        if (response.status === 200) {
          let filteredData = response.data;

          // Apply filter if filterType is set
          if (filterType) {
            filteredData = filteredData.filter(
              (item) => item.Type === filterType
            );
          }

          // Apply search filter
          if (searchInput) {
            filteredData = filteredData.filter((item) =>
              item.IP_Host.includes(searchInput)
            );
          }

          // Update state with filtered data
          setSowData(filteredData);
        }
      } catch (error) {
        console.error("Error fetching SOW data:", error);
      }
    };

    fetchFilteredSowData(); // Fetch SOW data initially
  }, [filterType, id, searchInput]);

  const handleChangeFilter = (value) => {
    setFilterType(value);
  };

  const handleResetFilter = () => {
    setFilterType("");
  };

  const handleSearch = (value) => {
    setSearchInput(value);
  };

  const handleInputUpdate = (rowId, dataIndex, newValue) => {
    setSowData((sow) => {
      const updatedData = sow.map((row) => {
        if (row.ID === rowId) {
          const updatedRow = {
            ...row,
            [dataIndex]: newValue,
          };
          saveChanges(updatedRow);
          return updatedRow;
        }
        return row;
      });
      return updatedData;
    });
  };

  const saveChanges = async (updatedData) => {
    try {
      const response = await axiosInstance.post(
        `/sow-by-projectID/${updatedData.ID}`,
        updatedData
      );
      console.log(response);
    } catch (error) {
      console.error("Error updating SOW:", error);
    }
  };

  const handleDelete = async (sowId) => {
    try {
      const token = localStorage.getItem("token");
      const response = await axiosInstance.delete(`delete-sow/${sowId}`, {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });
      console.log(response.data);
    } catch (error) {
      console.error("Error deleting SOW:", error);
    }
  };
  const handleGoback=()=>{
    navigate(-1);
  }

  const columns = [
    {
      title: "Type",
      dataIndex: "Type",
      key: "type",
      render: (text, record) => (
        <EditableCell
          record={record}
          dataIndex="Type"
          value={text}
          handleUpdate={handleInputUpdate}
        />
      ),
    },
    {
      title: "Name",
      dataIndex: "Nom",
      key: "nom",
      render: (text, record) => (
        <EditableCell
          record={record}
          dataIndex="Nom"
          value={text}
          handleUpdate={handleInputUpdate}
        />
      ),
    },
    {
      title: "IP Host",
      dataIndex: "IP_Host",
      key: "ipHost",
      render: (text, record) => (
        <EditableCell
          record={record}
          dataIndex="IP_Host"
          value={text}
          handleUpdate={handleInputUpdate}
        />
      ),
    },
    {
      title: "Field 3",
      dataIndex: "field3",
      key: "field3",
      render: (text, record) => (
        <EditableCell
          record={record}
          dataIndex="field3"
          value={text}
          handleUpdate={handleInputUpdate}
        />
      ),
    },
    {
      title: "Field 4",
      dataIndex: "field4",
      key: "field4",
      render: (text, record) => (
        <EditableCell
          record={record}
          dataIndex="field4"
          value={text}
          handleUpdate={handleInputUpdate}
        />
      ),
    },
    {
      title: "Field 5",
      dataIndex: "field5",
      key: "field5",
      render: (text, record) => (
        <EditableCell
          record={record}
          dataIndex="field5"
          value={text}
          handleUpdate={handleInputUpdate}
        />
      ),
    },
    {
      title: "Dev By",
      dataIndex: "dev_by",
      key: "devBy",
      render: (text, record) => (
        <EditableCell
          record={record}
          dataIndex="dev_by"
          value={text}
          handleUpdate={handleInputUpdate}
        />
      ),
    },
    {
      title: "URL",
      dataIndex: "URL",
      key: "url",
      render: (text, record) => (
        <EditableCell
          record={record}
          dataIndex="URL"
          value={text}
          handleUpdate={handleInputUpdate}
        />
      ),
    },
    {
      title: "Number of Users",
      dataIndex: "Number_users",
      key: "numberOfUsers",
      render: (text, record) => (
        <EditableCell
          record={record}
          dataIndex="Number_users"
          value={text}
          handleUpdate={handleInputUpdate}
        />
      ),
    },
    {
      title: "Action",
      dataIndex: "action",
      key: "action",
      render: (text, record) => (
        <button onClick={() => handleDelete(record.ID)}>Delete</button>
      ),
    },
  ];

  return (
    <div>
      <Title level={2}>All Sow</Title>
      <Button onClick={handleResetFilter} type="primary">
        Reset Filter
      </Button>

      <Select
        placeholder="Select Type"
        style={{ width: 200, marginBottom: 16 }}
        allowClear
        onChange={handleChangeFilter}
        value={filterType}
      >
        <Option value="PC">PC</Option>
        <Option value="R_S">R_S</Option>
        <Option value="Ext">Ext</Option>
        <Option value="Serveur">Serveur</Option>
      </Select>

      <Input.Search
        placeholder="Search IP Host"
        allowClear
        onSearch={handleSearch}
        style={{ width: 200, marginBottom: 16 }}
      />

      <Table dataSource={sowData} columns={columns} rowKey="id" />
      <Button  type='primary' onClick={handleGoback}> Go Back</Button>
    </div>
  );
}

const EditableCell = ({ value, record, dataIndex, handleUpdate }) => {
  const [isEditing, setIsEditing] = useState(false);
  const [inputValue, setInputValue] = useState(value);

  const toggleEdit = () => {
    if (dataIndex.toLowerCase() === "id") {
      return;
    }

    setIsEditing(!isEditing);
    setInputValue(value);
  };

  const handleInputChange = (e) => {
    setInputValue(e.target.value);
  };

  const handleInputConfirm = () => {
    if (isEditing) {
      if (inputValue !== value) {
        handleUpdate(record.ID, dataIndex, inputValue);
      }
      setIsEditing(false);
    }
  };

  useEffect(() => {
    if (!isEditing) {
      setInputValue(value);
    }
  }, [isEditing, value]);

  return (
    <div>
      {isEditing ? (
        <Input
          value={inputValue}
          autoFocus
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
