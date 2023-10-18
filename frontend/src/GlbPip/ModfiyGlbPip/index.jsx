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
  TextField,
  Button,
} from "@mui/material";
import { axiosInstance } from "../../axios/axiosInstance";
import { useNavigate, useParams } from "react-router-dom";
import toast from "react-hot-toast";

export default function ModifyGlbPip() {
  const { id } = useParams();
  const [formData, setFormData] = useState({
    Nom: "",
    Titre: "",
    adresse_mail_primaire: "",
    adresse_mail_secondaire: "",
    tel: "",
  });

  const [telError, setTelError] = useState(false);
  const [emailError, setEmailError] = useState(false);

  useEffect(() => {
    axiosInstance
      .get(`/get-glbPip/${id}`)
      .then((response) => {
        if (response.status === 200) {
          const glbPip = response.data.GlbPip;
          setFormData({
            Nom: glbPip.Nom || "",
            Titre: glbPip.Titre || "",
            adresse_mail_primaire: glbPip["Adresse mail primaire"] || "",
            adresse_mail_secondaire: glbPip["Adresse mail secondaire"] || "",
            tel: glbPip.Tél,
          });
        }
      })
      .catch((error) => {
        console.error("Error fetching data:", error);
      });
  }, [id]);

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
   const navigate=useNavigate();
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

    try {
      const response = await axiosInstance.put(
        `/update-glbPip/${id}`,
        formData
      );
      console.log(response.data);
      if(response.data.success){
        toast.success(response.data.message);
        setTimeout(() => {
            navigate("/all-glb-pip");
          }, 2000);
      }else {
        toast.error(response.data.message)
      }
    } catch (error) {
      console.error(error);
      toast.error("Something went wrong");
    }
  };

  return (
    <div className="add-glb-pip-div">
      <h1 className="add-glb-pip-title">Modifier un Glb Pip</h1>
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
                  placeholder="Saisir Mail secondaire"
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
                  Enregistrer
                </Button>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </TableContainer>
    </div>
  );
}
