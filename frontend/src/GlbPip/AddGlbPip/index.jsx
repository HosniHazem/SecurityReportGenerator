import React, { useState, useEffect } from "react";
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
} from "@mui/material";
import axios from "axios";
import "./index.css";
import { axiosInstance } from "../../axios/axiosInstance";
import toast from "react-hot-toast";
import { useNavigate, useParams } from "react-router-dom";

export default function AddGlbPip() {
  const [formData, setFormData] = useState({
    Nom: "",
    Titre: "",
    adresse_mail_primaire: "",
    adresse_mail_secondaire: "",
    tel: "",
  });
  const [customerId, setCustomerId] = useState();
  const initialFormData = {
    Nom: "",
    Titre: "",
    adresse_mail_primaire: "",
    adresse_mail_secondaire: "",
    tel: "",
  };

  const [telError, setTelError] = useState(false);
  const [emailError, setEmailError] = useState(false);
  const { id } = useParams();
  const navigate = useNavigate();

  const [project, setProject] = useState();
  console.log("projectId is", id);
  
  useEffect(() => {
    const fetchData = async () => {
      try {
        const response = await axiosInstance.get(`/Project/${id}/show`);
        if (response.status === 200) {
          console.log("mm", response.data.Project);
          setProject(response.data.Project);
          console.log('project is', project);
        }
      } catch (error) {
        console.error("Error fetching data:", error);
      }
    };
  
    fetchData();
  }, []);
  
  // console.log("project", project.customer_id);
  const handleChange = (e) => {
    const { name, value } = e.target;
    setFormData({ ...formData, [name]: value });

    if (name === "tel") {
      setTelError(!validateTel(value));
    } else if (name === "adresse_mail_primaire") {
      setEmailError(!validateEmail(value));
    }
  };

  const validateEmail = (email) => {
    // Regular expression to validate email format
    const emailRegex = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
    return emailRegex.test(email);
  };

  const validateTel = (tel) => {
    // Regular expression to validate phone number format
    const telRegex = /^(2|5|9)\d{7}$/;
    return telRegex.test(tel);
  };

  const handleSubmit = async () => {
    const { adresse_mail_primaire, tel } = formData;

    if (!validateEmail(adresse_mail_primaire)) {
      setEmailError(true);
      return;
    }

    if (!validateTel(tel)) {
      setTelError(true);
      return;
    }
    console.log("cus",project.customer_id)

    try {
      const response = await axiosInstance.post("/add-glbPip", {
        ...formData,
        customer_id: id,
      });
      console.log(response.data);
      setFormData(initialFormData);
      setTelError(false);
      setEmailError(false);
      if (response.data.success) {
        toast.success(response.data.message);
        navigate("/dashboard")
      } else {
        toast.error(response.data.message);
      }
    } catch (error) {
      toast.error("error");
    }
  };

  const handleNavigate=()=>{
    navigate(-1);
  }

  return (
<div className="add-glb-pip-div">
  <h1 className="add-glb-pip-title">Ajouter un Glb Pip</h1>
  <TableContainer component={Paper}>
    <Table>
      <TableHead>
        <TableRow>
          <TableCell>
            <span>Nom et Prénom</span>
          </TableCell>
          <TableCell>
            <span>Titre</span>
          </TableCell>
          <TableCell>
            <span>Tél</span>
          </TableCell>
          <TableCell>
            <span>Mail primaire</span>
          </TableCell>
          <TableCell>
            <span>Mail secondaire</span>
          </TableCell>
          <TableCell>
            <span> </span>
          </TableCell>
        </TableRow>
      </TableHead>
      <TableBody>
        <TableRow>
          <TableCell>
            <TextField
              variant="outlined"
              placeholder="Saisir nom et prénom"
              fullWidth
              name="Nom"
              value={formData.Nom}
              onChange={handleChange}
            />
          </TableCell>
          <TableCell>
            <TextField
              variant="outlined"
              placeholder="Saisir Titre"
              fullWidth
              name="Titre"
              value={formData.Titre}
              onChange={handleChange}
            />
          </TableCell>
          <TableCell>
            <TextField
              variant="outlined"
              placeholder="Saisir Tél"
              fullWidth
              name="tel"
              value={formData.tel}
              onChange={handleChange}
              helperText={
                telError
                  ? "Entrer un numéro Ooredoo/Tunisie Télécom/Orange"
                  : ""
              }
              error={telError}
            />
          </TableCell>
          <TableCell>
            <TextField
              variant="outlined"
              placeholder="Saisir E-mail"
              fullWidth
              name="adresse_mail_primaire"
              value={formData.adresse_mail_primaire}
              onChange={handleChange}
              helperText={emailError ? "Entrer un email valide" : ""}
              error={emailError}
            />
          </TableCell>
          <TableCell>
            <TextField
              variant="outlined"
              placeholder="Enter Mail secondaire"
              fullWidth
              name="adresse_mail_secondaire"
              value={formData.adresse_mail_secondaire}
              onChange={handleChange}
            />
          </TableCell>
          <TableCell>
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
  <Button onClick={handleNavigate}>View GlB PIP OF THIS CUSTOMER</Button>
</div>

  );
}
