import React, { useState } from "react";
import {
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  Paper,
  TextField,
  Button,
  Select,
  MenuItem,
} from "@mui/material";
import { axiosInstance } from "../../axios/axiosInstance";
import toast from "react-hot-toast";

export default function AddAuditPreviousAudit() {
  const [formData, setFormData] = useState({
    Project_name: "",
    ID_Projet: 1,
    ProjetNumero: "",
    Action: "",
    ActionNumero: "",
    Criticite: "faible",
    Chargee_action: "",
    ChargeHJ: "",
    TauxRealisation: "",
    Evaluation: "",
  });
  const [actionNumeroError,setActionNumeroError]=useState(false);
  const [projectNumeroError,setProjectNumeroError]=useState(false);

  const validateactionNumero=(number)=>{
    const posistiveNumberRegex=/^0*?[1-9]\d*$/;
    return posistiveNumberRegex.test(number);


  }

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormData({
      ...formData,
      [name]: value,
    });
    if(name==="ProjetNumero" ){
      setProjectNumeroError(!validateactionNumero(value));

    }else if(name==="ActionNumero"){
      setActionNumeroError(!validateactionNumero(value));

    }
  };
  const handleSubmit = async () => {
      if (
      formData.Project_name === "" ||
      formData.ID_Projet === "" ||
      formData.numeroProjet === "" ||
      formData.Action === "" ||
      formData.ActionNumero === "" ||
      formData.Criticite === "" ||
      formData.Chargee_action === "" ||
      formData.ChargeHJ === "" ||
      formData.TauxRealisation === "" ||
      formData.Evaluation === ""
    ) {
      // Display an error message to the user
      toast.error("Please fill in all required fields.");
      return;
    }
    if(actionNumeroError ||projectNumeroError ){
      toast.error("entrer un numero positif");
      return;
    }

    try {
      const response = await axiosInstance.post(
        "/add-audit-previous-audits",
        formData
      );
      if (response.data.success) {
        toast.success(response.data.message);
      } else {
        toast.error(response.data.message);
      }
    } catch (error) {
      console.log(error);
      toast.error("An error occurred.");
    }
  };

  return (
    <div>
      <TableContainer component={Paper}>
        <h1>Ajout un Audit Previous Audit :</h1>
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
            <TableRow>
              <TableCell style={{ width: "14.3061%" }}>
                <TextField
                  name="Project_name"
                  variant="outlined"
                  value={formData.Project_name}
                  onChange={handleInputChange}
                />
              </TableCell>
              <TableCell style={{ width: "14.2857%" }}>
              <Select
                  name="ID_Projet"
                  variant="outlined"
                  value={formData.ID_Projet}
                  onChange={handleInputChange}
                >
                  <MenuItem value={1}>MAJ PSI</MenuItem>
                  <MenuItem value={2}>Mise en place de PCA</MenuItem>
                  <MenuItem value={3}>Audit réglementaire</MenuItem>
                </Select>
              </TableCell>
              <TableCell style={{ width: "14.2857%" }}>
                <TextField
                  name="ProjetNumero"
                  variant="outlined"
                  value={formData.ProjetNumero}
                  onChange={handleInputChange}
                  helperText={projectNumeroError? "Entrer un nombre positif" :""}
                />
              </TableCell>
              <TableCell style={{ width: "14.2857%" }}>
                <TextField
                  name="Action"
                  variant="outlined"
                  value={formData.Action}
                  onChange={handleInputChange}
                /> 
              </TableCell>
              <TableCell style={{ width: "14.2388%" }}>
                <TextField
                  name="ActionNumero"
                  variant="outlined"
                  value={formData.ActionNumero}
                  onChange={handleInputChange}
                  helperText={actionNumeroError ? "Entrer un nombre positif" :""}

                />
              </TableCell>
              <TableCell style={{ width: "14.2857%" }}>
              <Select
                  name="Criticite"
                  variant="outlined"
                  value={formData.Criticite}
                  onChange={handleInputChange}
                >
                  <MenuItem value="faible">Faible</MenuItem>
                  <MenuItem value="moyenne">Moyenne</MenuItem>
                  <MenuItem value="élevée">Élevée</MenuItem>
                </Select>
              </TableCell>
              <TableCell style={{ width: "14.2857%" }}>
                <TextField
                  name="Chargee_action"
                  variant="outlined"
                  value={formData.Chargee_action}
                  onChange={handleInputChange}
                />
              </TableCell>
              <TableCell style={{ width: "14.2857%" }}>
                <TextField
                  name="ChargeHJ"
                  variant="outlined"
                  value={formData.ChargeHJ}
                  onChange={handleInputChange}
                />
              </TableCell>
              <TableCell style={{ width: "14.2857%" }}>
                <TextField
                  name="TauxRealisation"
                  variant="outlined"
                  value={formData.TauxRealisation}
                  onChange={handleInputChange}
                />
              </TableCell>
              <TableCell style={{ width: "14.2857%" }}>
                <TextField
                  name="Evaluation"
                  variant="outlined"
                  value={formData.Evaluation}
                  onChange={handleInputChange}
                />
              </TableCell>
              <TableCell colSpan={10} align="center">
                <Button
                  variant="contained"
                  color="primary"
                  onClick={handleSubmit}
                >
                  Ajouter
                </Button>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </TableContainer>
    </div>
  );
}
