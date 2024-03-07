import React, { useState, useEffect, Children } from "react";
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
  const [page, setPage] = useState(0);
  const [rowsPerPage, setRowsPerPage] = useState(5);
  const navigate = useNavigate();
  const { customerID } = useParams();

  useEffect(() => {
    axiosInstance
      .get(`get-glbpip-by-customer-id/${customerID}`)
      .then((response) => {
        if (response.status === 200) {
          setGlbPips(response.data.data);
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
    try {
      const response = await axiosInstance.delete(`/delete-glbPip/${id}`);
      if (response.data.success) {
        // Remove the deleted record from the state
        setGlbPips((prevGlbPips) => prevGlbPips.filter((glbPip) => glbPip.ID !== id));
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

  const isValidEmail = (email) => {
    // You can implement a more sophisticated email validation regex if needed
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  };

  const isValidPhoneNumber = (phoneNumber) => {
    // Validate Tél format: It should start with 2, 5, or 8 and have 8 digits.
    const phoneRegex = /^(2|5|9)\d{7}$/;
    return phoneRegex.test(phoneNumber);
  };

  const handleSaveChanges = async (id, updatedData) => {
    // Implement your validation rules here
    if (!updatedData.Nom || !updatedData.Titre) {
      toast.error("Please fill in all required fields.");
      return;
    }
  
    if (updatedData["Adresse mail primaire"] && !isValidEmail(updatedData["Adresse mail primaire"])) {
      toast.error("Invalid Email primaire format.");
      return;
    }
  
    if (updatedData["Adresse mail secondaire"] && !isValidEmail(updatedData["Adresse mail secondaire"])) {
      toast.error("Invalid Email Secondaire format.");
      return;
    }
  
    if (updatedData.Tél && !isValidPhoneNumber(updatedData.Tél)) {
      toast.error("Invalid Tél format. It should start with 2, 5, or 8 and have 8 digits.");
      return;
    }
  
    console.log(glbPips);
  
    try {
      const response = await axiosInstance.post(`/update-glbPip/${id}`, {
        Nom: updatedData.Nom,
        Titre: updatedData.Titre,
        adresse_mail_primaire: updatedData["Adresse mail primaire"],
        adresse_mail_secondaire: updatedData["Adresse mail secondaire"],
        tel: updatedData.Tél
      });
  console.log(response.data)
      if (response.data.success) {
        // Update the state with the modified data
        setGlbPips((prevGlbPips) => prevGlbPips.map((glbPip) => (glbPip.ID === id ? { ...glbPip, ...updatedData } : glbPip)));
        toast.success(response.data.message);
      } else {
        toast.error(response.data.message);
      }
    } catch (error) {
      console.error(error);
      toast.error("Something went wrong");
    }
  };
  

  const handleInputChange = (id, field, value) => {
    setGlbPips((prevGlbPips) => {
      const updatedGlbPips = prevGlbPips.map((glbPip) =>
        glbPip.ID === id ? { ...glbPip, [field]: value } : glbPip
      );
      console.log("wa", updatedGlbPips);
      return updatedGlbPips;
    });
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
  {glbPips?.slice(page * rowsPerPage, (page + 1) * rowsPerPage).map((glbPip) => (
    <TableRow key={glbPip.ID}>
      <TableCell>
        <Input
          defaultValue={glbPip.Nom}
          onChange={(e) => handleInputChange(glbPip.ID, 'Nom', e.target.value)}
        />
      </TableCell>
      <TableCell>
        <Input
          defaultValue={glbPip.Titre}
          onChange={(e) => handleInputChange(glbPip.ID, 'Titre', e.target.value)}
        />
      </TableCell>
      <TableCell>
        <Input
          defaultValue={glbPip["Adresse mail primaire"]}
          onChange={(e) => handleInputChange(glbPip.ID, 'adresse_mail_primaire', e.target.value)}
        />
      </TableCell>
      <TableCell>
        <Input
          defaultValue={glbPip["Adresse mail secondaire"]}
          onChange={(e) => handleInputChange(glbPip.ID, 'adresse_mail_secondaire', e.target.value)}
        />
      </TableCell>
      <TableCell>
        <Input
          defaultValue={glbPip.Tél}
          onChange={(e) => handleInputChange(glbPip.ID, 'tel', e.target.value)}
        />
      </TableCell>
      <TableCell>
        <Button onClick={() => handleNavigation(glbPip.ID)}>Modifier</Button>
        <Button onClick={() => handleDeleteGlbPip(glbPip.ID)}>Supprimer</Button>
        <Button onClick={() => handleSaveChanges(glbPip.ID, glbPip)}>Sauvegarder</Button>
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

      <Button onClick={() => navigate(-1)}>Go back</Button>
    </div>
  );
}
