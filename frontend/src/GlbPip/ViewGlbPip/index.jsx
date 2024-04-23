import React, { useState, useEffect } from "react";
import axios from "axios";
import {
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  Paper,
  Button,
  TablePagination,
  Input,
} from "@mui/material";
import { axiosInstance } from "../../axios/axiosInstance";
import { useNavigate, useParams } from "react-router-dom";
import toast from "react-hot-toast";

export default function ViewGlbPip() {
  const [glbPips, setGlbPips] = useState(null);
  const [editableCell, setEditableCell] = useState({
    rowIndex: -1,
    colIndex: -1,
    id: null,
  });
  const [page, setPage] = useState(0);
  const [rowsPerPage, setRowsPerPage] = useState(5);
  const navigate = useNavigate();
  const { customerID } = useParams();
  const [modifiedGlbPips, setModifiedGlbPips] = useState([]);

  useEffect(() => {
    axiosInstance
      .get(`get-glbpip-by-customer-id/${customerID}`)
      .then((response) => {
        if (response.status === 200) {
          // Sort the data by ID from newest to latest
          const sortedData = response.data.data.sort((a, b) => b.ID - a.ID);
          setGlbPips(sortedData);
        }
      })
      .catch((error) => {
        console.error("Error fetching data:", error);
      });
  }, [customerID]);
  

  const handleChangePage = (event, newPage) => {
    setPage(newPage);
  };

  const handleChangeRowsPerPage = (event) => {
    setRowsPerPage(parseInt(event.target.value, 10));
    setPage(0); // Reset the page to the first page when changing rows per page.
  };

  const handleDeleteGlbPip = async (id) => {
    console.log(id);
    try {
      const response = await axiosInstance.delete(`/delete-glbPip/${id}`);
      if (response.data.success) {
        // Remove the deleted record from the state
        setGlbPips((prevGlbPips) =>
          prevGlbPips.filter((glbPip) => glbPip.ID !== id)
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

  const handleNavigation = (id) => {
    navigate(`/modify-glb-pip/${id}`);
  };

  const handleEditCell = (rowIndex, colIndex, id) => {
    setEditableCell({ rowIndex, colIndex, id });
  };

  const validateEmail = (email) => {
    // Simple email validation regex
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  };

  const validateTel = (tel) => {
    // Telephone number validation regex (8 digits starting with 2, 5, or 9)
    const telRegex = /^[259]\d{7}$/;
    return telRegex.test(tel);
  };

  const handleUpdate = async (data, id) => {
    const {
      Nom,
      Titre,
      "Adresse mail primaire": Adresse_mail_primaire,
      "Adresse mail secondaire": Adresse_mail_secondaire,
      Tél,
    } = data;
    console.log("data", data);
    if (Adresse_mail_primaire && !validateEmail(Adresse_mail_primaire)) {
      toast.error("Invalid email format for Adresse mail primaire");
      return;
    }
    if (Adresse_mail_secondaire && !validateEmail(Adresse_mail_secondaire)) {
      toast.error("Invalid email format for Adresse mail secondaire");
      return;
    }

    if (Tél && !validateTel(Tél)) {
      toast.error("Invalid telephone number format");
      return;
    }

    try {
      const response = await axiosInstance.post(`/update-glbPip/${id}`, {
        Nom,
        Titre,
        adresse_mail_primaire: Adresse_mail_primaire,
        adresse_mail_secondaire: Adresse_mail_secondaire,
        tel: Tél,
      });
      console.log(response.data);
      if (response.data.success) {
        toast.success(response.data.message);
      } else {
        toast.error("Something went wrong");
      }
    } catch (error) {
      console.error(error);
      toast.error("Error updating data");
    }
  };

  const handleCellBlur = (field, rowIndex, colIndex, newValue, id) => {
    console.log(
      `New value for ${field} at (${rowIndex}, ${colIndex}):`,
      newValue
    );
    setGlbPips((prevGlbPips) => {
      const newGlbPips = [...prevGlbPips];
      newGlbPips[rowIndex][field] = newValue;
      if (
        editableCell.rowIndex === rowIndex &&
        editableCell.colIndex === colIndex
      ) {
        // handleUpdate(newGlbPips[rowIndex], id);
      }
      return newGlbPips;
    });
  };

  const handleSave = (glbPip) => {
    if (glbPip !== null) {
      handleUpdate(glbPip, glbPip.ID);
    }
  };

  const handleGoback = () => {
    navigate(-1);
  };

  const handleNavigate = () => {
      navigate(`/ajout-glb-pip/${customerID}`);
   
  };

  return (
    <div className="center-container">
      <TableContainer sx={{ maxHeight: 440 }}>
        <Table stickyHeader aria-label="sticky table">
          <TableHead>
            <TableRow>
              <TableCell>Nom</TableCell>
              <TableCell>Titre</TableCell>
              <TableCell>Email primaire</TableCell>
              <TableCell>Email Secondaire</TableCell>
              <TableCell>Tél</TableCell>
              <TableCell>Actions</TableCell>
            </TableRow>
          </TableHead>
          <TableBody>
            {(glbPips
              ? glbPips.slice(page * rowsPerPage, (page + 1) * rowsPerPage)
              : []
            ).map((glbPip, rowIndex) => (
              <TableRow key={glbPip.ID}>
                <TableCell
                  onDoubleClick={() =>
                    handleEditCell(rowIndex, 0, glbPip.ID)
                  }
                >
                  {editableCell.rowIndex === rowIndex &&
                  editableCell.colIndex === 0 ? (
                    <Input
                      value={glbPip.Nom}
                      onChange={(e) =>
                        handleCellBlur(
                          "Nom",
                          rowIndex,
                          0,
                          e.target.value,
                          glbPip.ID
                        )
                      }
                      onBlur={(e) =>
                        handleCellBlur(
                          "Nom",
                          rowIndex,
                          0,
                          e.target.value,
                          glbPip.ID
                        )
                      }
                    />
                  ) : (
                    glbPip.Nom
                  )}
                </TableCell>
                <TableCell
                  onDoubleClick={() =>
                    handleEditCell(rowIndex, 1, glbPip.ID)
                  }
                >
                  {editableCell.rowIndex === rowIndex &&
                  editableCell.colIndex === 1 ? (
                    <Input
                      value={glbPip.Titre}
                      onChange={(e) =>
                        handleCellBlur(
                          "Titre",
                          rowIndex,
                          1,
                          e.target.value,
                          glbPip.ID
                        )
                      }
                      onBlur={(e) =>
                        handleCellBlur(
                          "Titre",
                          rowIndex,
                          1,
                          e.target.value,
                          glbPip.ID
                        )
                      }
                    />
                  ) : (
                    glbPip.Titre
                  )}
                </TableCell>
                <TableCell
                  onDoubleClick={() =>
                    handleEditCell(rowIndex, 2, glbPip.ID)
                  }
                >
                  {editableCell.rowIndex === rowIndex &&
                  editableCell.colIndex === 2 ? (
                    <Input
                      value={glbPip["Adresse mail primaire"]}
                      onChange={(e) =>
                        handleCellBlur(
                          "Adresse mail primaire",
                          rowIndex,
                          2,
                          e.target.value,
                          glbPip.ID
                        )
                      }
                      onBlur={(e) =>
                        handleCellBlur(
                          "Adresse mail primaire",
                          rowIndex,
                          2,
                          e.target.value,
                          glbPip.ID
                        )
                      }
                    />
                  ) : (
                    glbPip["Adresse mail primaire"]
                  )}
                </TableCell>
                <TableCell
                  onDoubleClick={() =>
                    handleEditCell(rowIndex, 3, glbPip.ID)
                  }
                >
                  {editableCell.rowIndex === rowIndex &&
                  editableCell.colIndex === 3 ? (
                    <Input
                      value={glbPip["Adresse mail secondaire"]}
                      onChange={(e) =>
                        handleCellBlur(
                          "Adresse mail secondaire",
                          rowIndex,
                          3,
                          e.target.value,
                          glbPip.ID
                        )
                      }
                      onBlur={(e) =>
                        handleCellBlur(
                          "Adresse mail secondaire",
                          rowIndex,
                          3,
                          e.target.value,
                          glbPip.ID
                        )
                      }
                    />
                  ) : (
                    glbPip["Adresse mail secondaire"]
                  )}
                </TableCell>
                <TableCell
                  onDoubleClick={() =>
                    handleEditCell(rowIndex, 4, glbPip.ID)
                  }
                >
                  {editableCell.rowIndex === rowIndex &&
                  editableCell.colIndex === 4 ? (
                    <Input
                      value={glbPip.Tél}
                      onChange={(e) =>
                        handleCellBlur(
                          "Tél",
                          rowIndex,
                          4,
                          e.target.value,
                          glbPip.ID
                        )
                      }
                      onBlur={(e) =>
                        handleCellBlur(
                          "Tél",
                          rowIndex,
                          4,
                          e.target.value,
                          glbPip.ID
                        )
                      }
                    />
                  ) : (
                    glbPip.Tél
                  )}
                </TableCell>
                <TableCell>
                  <Button onClick={() => handleSave(glbPip)}>Save</Button>
                  <Button onClick={() => handleDeleteGlbPip(glbPip.ID)}>
                    Supprimer
                  </Button>
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </TableContainer>
      <TablePagination
        component="div"
        count={glbPips?.length}
        page={page}
        onPageChange={handleChangePage}
        rowsPerPage={rowsPerPage}
        onRowsPerPageChange={handleChangeRowsPerPage}
      />
      <Button onClick={handleNavigate}>Add</Button>
      <Button onClick={handleGoback}>Go back</Button>
    </div>
  );
}
