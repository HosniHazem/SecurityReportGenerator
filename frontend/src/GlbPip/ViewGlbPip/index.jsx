import React, { useState, useEffect } from "react";
import axios from "axios";
import { Table, TableBody, TableCell, TableContainer, TableHead, TableRow, Paper, Button, TablePagination } from "@mui/material";
import { axiosInstance } from "../../axios/axiosInstance";
import { useNavigate } from "react-router-dom";
import toast from "react-hot-toast";

export default function ViewGlbPip() {
  const [glbPips, setGlbPips] = useState([]);
  const [page, setPage] = useState(0);
  const [rowsPerPage, setRowsPerPage] = useState(5);
  const navigate=useNavigate();
  useEffect(() => {
    axiosInstance
      .get("/all-glbpip")
      .then((response) => {
        if (response.status === 200) {
          setGlbPips(response.data.GlbPip);
        }
      })
      .catch((error) => {
        console.error("Error fetching data:", error);
      });
  }, []);

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
  const handleNavigation=(id)=>{
    navigate(`/modify-glb-pip/${id}`)
  }
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
            {glbPips.slice(page * rowsPerPage, (page + 1) * rowsPerPage).map((glbPip) => (
              <TableRow key={glbPip.ID}>
                <TableCell>{glbPip.Nom}</TableCell>
                <TableCell>{glbPip.Titre}</TableCell>
                <TableCell>{glbPip["Adresse mail primaire"]}</TableCell>
                <TableCell>{glbPip["Adresse mail secondaire"]}</TableCell>
                <TableCell>{glbPip.Tél}</TableCell>
                <TableCell>
                  <Button  onClick={()=>handleNavigation(glbPip.ID)}>Modifier</Button>
                  <Button onClick={()=>handleDeleteGlbPip(glbPip.ID)}> Supprimer</Button>
                </TableCell>
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </TableContainer>
      <TablePagination
        component="div"
        count={glbPips.length}
        page={page}
        onPageChange={handleChangePage}
        rowsPerPage={rowsPerPage}
        onRowsPerPageChange={handleChangeRowsPerPage}
      />
    </div>
  );
}
