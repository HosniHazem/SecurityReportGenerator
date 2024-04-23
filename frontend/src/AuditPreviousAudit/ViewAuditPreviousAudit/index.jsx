import React, { useState, useEffect } from "react";
import { Table, Button, Input } from "antd";
import { axiosInstance } from "../../axios/axiosInstance";
import { useNavigate, useParams } from "react-router-dom";
import toast from "react-hot-toast";

export default function ViewAuditPrevious() {
  const [auditData, setAuditData] = useState([]);
  const [projectNameMapping, setProjectNameMapping] = useState({});
  const { projectId } = useParams();
  console.log(projectId);
  const navigate = useNavigate();

  // Create a mapping of project IDs to names
  useEffect(() => {
    setProjectNameMapping({
      1: "MAJ PSI",
      2: "Mise en place de PCA",
      3: "Audit réglementaire",
    });
  }, []);

  useEffect(() => {
    axiosInstance
      .get(`/get-audit-previous-audits-by-projectID/${projectId}`)
      .then((response) => {
        if (response.status === 200) {
          console.log("response",response.data.auditPrev);
          const sortedData = response.data.data.sort((a, b) => b.ID - a.ID);

          setAuditData(sortedData);
        }
      })
      .catch((error) => {
        console.error("Error fetching data:", error);
      });
  }, []);

  const handleDelete = async (id) => {
    console.log(id);
    try {
      const response = await axiosInstance.delete(
        `/delete-audit-previous-audits/${id}`
      );
      if (response.data.success) {
        // Remove the deleted record from the state
        setAuditData((prevAuditData) =>
          prevAuditData.filter((auditData) => auditData.ID !== id)
        );
        toast.success(response.data.message);
      } else {
        toast.error(response.data.message);
      }
    } catch (error) {
      console.error(error);
      toast.error("Something went wrong");
    }
  };

  const handleNavigate = async (id) => {
    navigate(`modify-audit-previous-audit/${id}`);
  };

  const handleGoBack = () => {
    navigate(-1);
  };

  // Inside ViewAuditPrevious component
  const handleInputUpdate = (rowId, dataIndex, newValue) => {
    setAuditData((prevAuditData) => {
      const updatedData = prevAuditData.map((row) => {
        if (row.ID === rowId) {
          const updatedRow = {
            ...row,
            [dataIndex]: newValue,
          };
          saveChanges(updatedRow); // Call saveChanges with the updated row
          return updatedRow;
        }
        return row;
      });
      console.log("Updated Data:", updatedData); // Log the entire updated data
      return updatedData;
    });
  };

  const saveChanges = async (updatedData) => {
    const {
      Action,
      ActionNumero,
      ChargeHJ,
      Chargee_action,
      Criticite,
      Evaluation,
      ID,
      Project_name,
      ProjetNumero,
      TauxRealisation,
    } = updatedData;
    try {
      const response = await axiosInstance.put(
        `/update-audit-previous-audits/${ID}`,
        updatedData
      );
      console.log("Updated audit previous:", response.data); // Log the response after updating
      // Handle success or display a message if needed
    } catch (error) {
      console.error("Failed to update audit previous:", error);
      // Handle error or display an error message
    }
  };

  const columns = [
    {
      title: "Nom de Projet",
      dataIndex: "Project_name",
      key: "Project_name",
      width: "14.3061%",
      render: (text, record) => (
        <EditableCell
          record={record}
          dataIndex="Project_name"
          value={text}
          handleUpdate={handleInputUpdate}
        />
      ),
    },
    {
      title: "Type de Projet",
      dataIndex: "ID_Projet",
      key: "ID_Projet",
      render: (ID_Projet, record) => (
        <EditableCell
          record={record}
          dataIndex="ID_Projet"
          value={projectNameMapping[ID_Projet]}
          handleUpdate={handleInputUpdate}
        />
      ),
      width: "14.2857%",
    },
    {
      title: "Numéro de Projet",
      dataIndex: "ProjetNumero",
      key: "ProjetNumero",
      width: "14.2857%",
      render: (text, record) => (
        <EditableCell
          record={record}
          dataIndex="ProjetNumero"
          value={text}
          handleUpdate={handleInputUpdate}
        />
      ),
    },
    {
      title: "Action",
      dataIndex: "ActionNumero",
      key: "ActionNumero",
      width: "14.2857%",
      render: (text, record) => (
        <EditableCell
          record={record}
          dataIndex="ActionNumero"
          value={text}
          handleUpdate={handleInputUpdate}
        />
      ),
    },
    {
      title: "Numéro D'action",
      dataIndex: "Action",
      key: "Action",
      width: "14.2857%",
      render: (text, record) => (
        <EditableCell
          record={record}
          dataIndex="Action"
          value={text}
          handleUpdate={handleInputUpdate}
        />
      ),
    },
    {
      title: "Criticité",
      dataIndex: "Criticite",
      key: "Criticite",
      width: "14.2857%",
      render: (text, record) => (
        <EditableCell
          record={record}
          dataIndex="Criticite"
          value={text}
          handleUpdate={handleInputUpdate}
        />
      ),
    },
    {
      title: "Chargé de l'action",
      dataIndex: "Chargee_action",
      key: "Chargee_action",
      width: "14.2857%",
      render: (text, record) => (
        <EditableCell
          record={record}
          dataIndex="Chargee_action"
          value={text}
          handleUpdate={handleInputUpdate}
        />
      ),
    },
    {
      title: "Chargé(H/J)",
      dataIndex: "ChargeHJ",
      key: "ChargeHJ",
      width: "14.2388%",
      render: (text, record) => (
        <EditableCell
          record={record}
          dataIndex="ChargeHJ"
          value={text}
          handleUpdate={handleInputUpdate}
        />
      ),
    },
    {
      title: "Taux de réalisation",
      dataIndex: "TauxRealisation",
      key: "TauxRealisation",
      width: "14.2857%",
      render: (text, record) => (
        <EditableCell
          record={record}
          dataIndex="TauxRealisation"
          value={text}
          handleUpdate={handleInputUpdate}
        />
      ),
    },
    {
      title: "Evaluation",
      dataIndex: "Evaluation",
      key: "Evaluation",
      width: "14.2857%",
      render: (text, record) => (
        <EditableCell
          record={record}
          dataIndex="Evaluation"
          value={text}
          handleUpdate={handleInputUpdate}
        />
      ),
    },
    {
      title: "Action",
      key: "action",
      render: (_, record) => (
        <span>
          <Button onClick={() => handleDelete(record.ID)}>Supprimer</Button>
          {/* <Button onClick={() => handleNavigate(record.ID)}>Modifier</Button> */}
        </span>
      ),
    },
  ];

  const goToAddAuditPrev = () => {
    navigate(`/add-audit-previous-audit/${projectId}`);
  };

  return (
    <div>
      <Table dataSource={auditData} columns={columns} />
      <Button onClick={handleGoBack}> Go back</Button>
      <Button onClick={goToAddAuditPrev}> Add audit previous Audit</Button>
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
      setIsEditing(false);
      // Only call update if value has changed
      if (inputValue !== value) {
        handleUpdate(record.ID, dataIndex, inputValue); // Pass the row ID, dataIndex, and newValue
      }
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
