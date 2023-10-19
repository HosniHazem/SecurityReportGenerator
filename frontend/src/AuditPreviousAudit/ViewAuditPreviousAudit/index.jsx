import React from 'react'
import {
    Table,
    TableBody,
    TableCell,
    TableContainer,
    TableHead,
    TableRow,
    Paper,
    
  } from "@mui/material";
import { axiosInstance } from '../../axios/axiosInstance';


export default function ViewAuditPRevious() {
    const [auditData, setAuditData] = useState([]);

    useEffect(() => {
        axiosInstance
          .get("/all-audit-previous-audits")
          .then((response) => {
            if (response.status === 200) {
                setAuditData(response.data); 
            }
          })
          .catch((error) => {
            console.error("Error fetching data:", error);
          });
      }, []);
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
          <TableCell style={{ width: "14.2857%" }}></TableCell>
        </TableRow>
      </TableHead>
      <TableBody>
      {auditData.map((audit, index) => (
              <TableRow key={index}>
                <TableCell>{audit.ProjetNumero}</TableCell>
                <TableCell>{audit.Project_name}</TableCell>
                <TableCell>{audit.ActionNumero}</TableCell>
                <TableCell>{audit.Action}</TableCell>
                <TableCell>{audit.Criticite}</TableCell>
                <TableCell>{audit.Chargee_action}</TableCell>
                <TableCell>{audit.ChargeHJ}</TableCell>
                <TableCell>{audit.TauxRealisation}</TableCell>
                <TableCell>{audit.Evaluation}</TableCell>
              </TableRow>
            ))}
      </TableBody>
    </Table>
  </TableContainer>
</div>
  )
}
