import React, { useEffect, useState } from "react";
import { Table, Card, Button, Modal } from "antd";
import { axiosInstance } from "../axios/axiosInstance";

const PermissionList = () => {
  const [permissions, setPermissions] = useState([]);
  const [modalVisible, setModalVisible] = useState(false);
  const [selectedUser, setSelectedUser] = useState(null);
  const [userControllers, setUserControllers] = useState([]);
  const token = localStorage.getItem("token"); // Get token from local storage

  useEffect(() => {
    fetchPermissions();
  }, []);

  const fetchPermissions = async () => {
    try {
      const response = await axiosInstance.get("/GetAllPrivilige", {
        headers: {
          Authorization: `Bearer ${token}`,
        },
      });
      setPermissions(Object.values(response.data));
    } catch (error) {
      console.error("Error fetching permissions:", error);
    }
  };

  const handleSeePrivileges = (user, controllers) => {
    setSelectedUser(user);
    setUserControllers(controllers);
    setModalVisible(true);
  };

  const handleCloseModal = () => {
    setSelectedUser(null);
    setUserControllers([]);
    setModalVisible(false);
  };

  const handleGrantPrivilege =async (userId, controllerId) => {
    // Implement grant privilege functionality here
    console.log("Grant privilege for User ID:", userId, "Controller ID:", controllerId);

    try {
        const response=await axiosInstance.post(`/Delete-Privilige/{userId}/{controllerId}`)
    } catch (error) {
        
    }


  };

  const handleDeletePrivilege = (userId, controllerId) => {
    // Implement delete privilege functionality here
    console.log("Delete privilege for User ID:", userId, "Controller ID:", controllerId);
  };

  const columns = [
    {
      title: "User Name",
      dataIndex: ["user", "name"],
      key: "name",
    },
    {
      title: "Email",
      dataIndex: ["user", "email"],
      key: "email",
    },
    {
      title: "Actions",
      key: "actions",
      render: (text, record) => (
        <Button onClick={() => handleSeePrivileges(record.user, record.controllers)}>
          See Privileges
        </Button>
      ),
    },
  ];

  const controllerColumns = [
    {
      title: "Controller Name",
      dataIndex: ["controller", "name"],
      key: "name",
    },
    {
      title: "Description",
      dataIndex: ["controller", "description"],
      key: "description",
    },
    {
      title: "Authorized",
      dataIndex: ["controller", "isAuthorized"],
      key: "isAuthorized",
      render: (text) => (text ? "Yes" : "No"),
    },
    {
      title: "Actions",
      key: "actions",
      render: (text, record) => (
        <>
          {record.controller.isAuthorized ? (
            <Button onClick={() => handleDeletePrivilege(selectedUser.id, record.controller.id)}>
              Delete Privilege
            </Button>
          ) : (
            <Button onClick={() => handleGrantPrivilege(selectedUser.id, record.controller.id)}>
              Grant Privilege
            </Button>
          )}
        </>
      ),
    },
  ];

  return (
    <div>
      <Card title="Permissions">
        <Table
          dataSource={permissions}
          columns={columns}
          rowKey={(record, index) => index}
        />
      </Card>
      <Modal
        title={`Privileges for ${selectedUser?.name}`}
        visible={modalVisible}
        onCancel={handleCloseModal}
        footer={null}
        width={800} // Set the width of the modal
      >
        <Table
          dataSource={userControllers}
          columns={controllerColumns}
          rowKey={(record, index) => index}
          pagination={false}
        />
      </Modal>
    </div>
  );
};

export default PermissionList;
