import React, { useState, useEffect } from "react";
import {
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  Paper,
  Button,
} from "@mui/material";
import { axiosInstance } from "../../axios/axiosInstance";
import { useNavigate } from "react-router-dom";
import toast from "react-hot-toast";

export default function ViewAuditPRevious() {
  const [auditData, setAuditData] = useState([]);
  const [projectNameMapping, setProjectNameMapping] = useState({});
const navigate=useNavigate()
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
      .get("/all-audit-previous-audits")
      .then((response) => {
        if (response.status === 200) {
          console.log(response.data.auditPrev);
          setAuditData(response.data.auditPrev);
        }
      })
      .catch((error) => {
        console.error("Error fetching data:", error);
      });
  }, []);
const handleDelete=async (id)=>{
   
        console.log(id);
        try {
          const response = await axiosInstance.delete(`/delete-audit-previous-audits/${id}`);
          if (response.data.success) {
            // Remove the deleted record from the state
            setAuditData((prevAuditData) => prevAuditData.filter((auditData) => auditData.ID !== id));
            toast.success(response.data.message);
          } else {
            toast.error(response.data.message);
          }
        } catch (error) {
          console.error(error);
          toast.error("Something went wrong");
        }
      
}
  return (
    <div>
      <TableContainer component={Paper}>
        <Table>
          <TableHead>
            <TableRow>
              <TableCell style={{ width: "14.3061%" }}>
                <b>
                  <span style={{ fontSize: "11pt" }}>Nom de Projet</span>
                </b>
              </TableCell>
              <TableCell style={{ width: "14.2857%" }}>
                Type de Projet
              </TableCell>
              <TableCell style={{ width: "14.2857%" }}>
                Numéro de Projet
              </TableCell>
              <TableCell style={{ width: "14.2857%" }}>Action</TableCell>
              <TableCell style={{ width: "14.2857%" }}>
                Numéro D'action
              </TableCell>
              <TableCell style={{ width: "14.2857%" }}>Criticité</TableCell>
              <TableCell style={{ width: "14.2857%" }}>
                Chargé de l'action
              </TableCell>
              <TableCell style={{ width: "14.2388%" }}>Chargé(H/J)</TableCell>
              <TableCell style={{ width: "14.2857%" }}>
                Taux de réalisation
              </TableCell>
              <TableCell style={{ width: "14.2857%" }}>Evaluation</TableCell>
             <TableCell></TableCell> 
            </TableRow>
          </TableHead>
          <TableBody>
            {auditData.map((audit, index) => (
              <TableRow key={index}>
                <TableCell>{audit.Project_name}</TableCell>
                <TableCell>
                  {projectNameMapping[audit.ID_Projet]}
                </TableCell>
                <TableCell>{audit.ProjetNumero}</TableCell>
                <TableCell>{audit.ActionNumero}</TableCell>
                <TableCell>{audit.Action}</TableCell>
                <TableCell>{audit.Criticite}</TableCell>
                <TableCell>{audit.Chargee_action}</TableCell>
                <TableCell>{audit.ChargeHJ}</TableCell>
                <TableCell>{audit.TauxRealisation}</TableCell>
                <TableCell>{audit.Evaluation}</TableCell>
               <TableCell>
                <Button onClick={()=>handleDelete(audit.ID)}>Supprimer</Button>
                <Button>Modifier</Button>
                </TableCell> 
              </TableRow>
            ))}
          </TableBody>
        </Table>
      </TableContainer>
    </div>
  );
}
