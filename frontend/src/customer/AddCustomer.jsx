import React, { useState } from "react";
import { useNavigate } from "react-router-dom";
import { Button } from "@mui/material";
import { styled } from "@mui/system";
import { ValidatorForm } from "react-material-ui-form-validator";
import axios from "axios";
import swal from "sweetalert";
import Swal from "sweetalert2";

import { Span } from "../projects/Typography";
import "./Add.css";

const Container = styled("div")(({ theme }) => ({
  margin: "30px",
  [theme.breakpoints.down("sm")]: {
    margin: "16px",
  },
  "& .breadcrumb": {
    marginBottom: "20px",
    [theme.breakpoints.down("sm")]: {
      marginBottom: "16px",
    },
  },
}));

function AddCustom() {
  const navigate = useNavigate();
  const [CustomerInput, setCustomer] = useState({
    SN: null,
    LN: null,
    Logo: null,
    Description: null,
    SecteurActivité: null,
    Categorie: null,
    SiteWeb: null,
    AddresseMail: "",
    Organigramme: null,
    NetworkDesign: null,
    error_list: [],
  });

  const handleInput = (e) => {
    e.persist();
    setCustomer({ ...CustomerInput, [e.target.name]: e.target.value });
  };

  const [Fich, setFich] = useState(null);

  const [Logo, setLogo] = useState(null);
  const [Organigramme, setOrganigramme] = useState(null);
  const [NetworkDesign, setNetworkDesign] = useState(null);

   const handleLogoUpload = (e) => {
    const file = e.target.files[0];
    if (CustomerInput.SN) {
      setLogo(file);
    } else {
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "You need to fill the SN before!",
      });
    }
  };

  const handleOrganigrammeUpload = (e) => {
    const file = e.target.files[0];
    if (CustomerInput.SN) {
      setOrganigramme(file);
    } else {
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "You need to fill the SN before!",
      });
    }
  };

  const handleNetworkDesignUpload = (e) => {
    const file = e.target.files[0];
    if (CustomerInput.SN) {
      setNetworkDesign(file);
    } else {
      Swal.fire({
        icon: "error",
        title: "Oops...",
        text: "You need to fill the SN before!",
      });
    }
  };


  

  const AddCustomer = (e) => {
    e.preventDefault();

    // Create separate FormData objects for each file
    const logoFormData = new FormData();
    const organigrammeFormData = new FormData();
    const networkDesignFormData = new FormData();

    if (Logo) {
      logoFormData.append("Logo", Logo);
      logoFormData.append("Logo_name", CustomerInput.SN + "_Logo." + Logo.name);
    }

    if (Organigramme) {
      organigrammeFormData.append("Organigramme", Organigramme);
      organigrammeFormData.append("Organigramme_name", CustomerInput.SN + "_Organigramme." + Organigramme.name);
    }

    if (NetworkDesign) {
      networkDesignFormData.append("NetworkDesign", NetworkDesign);
      networkDesignFormData.append("NetworkDesign_name", CustomerInput.SN + "_NetworkDesign." + NetworkDesign.name);
    }


    axios
      .post(
        "http://webapp.smartskills.tn/AppGenerator/backend/api/imageProfil",
        logoFormData
      )
      .then((res) => {
        // Handle response
      });

    axios
      .post(
        "http://webapp.smartskills.tn/AppGenerator/backend/api/imageProfil",
        organigrammeFormData
      )
      .then((res) => {
        // Handle response
      });

    axios
      .post(
        "http://webapp.smartskills.tn/AppGenerator/backend/api/imageProfil",
        networkDesignFormData
      )
      .then((res) => {
        // Handle response
      });


    e.preventDefault();

    const data = {
      SN: CustomerInput.SN,
      LN: CustomerInput.LN,
      Logo: Fich.Logo,
      Description: CustomerInput.Description,
      SecteurActivité: CustomerInput.SecteurActivité,
      Categorie: CustomerInput.Categorie,
      "Site Web": CustomerInput.SiteWeb,
      "Addresse mail": CustomerInput.AddresseMail,
      Organigramme: Fich.Organigramme,
      Network_Design: Fich.NetworkDesign,
    };
    console.log(data);
    axios
      .post(
        `http://webapp.smartskills.tn/AppGenerator/backend/api/Customer/create`,
        data
      )
      .then((res) => {
        if (res.data.status === 200) {
          swal("Created", "Customer", "success");
          navigate("/customer");
        } else if (res.data.status === 404) {
          swal("Error", CustomerInput.SN, "error");
        } else if (res.data.status === 422) {
          swal("All fields are mandatory", "", "error");

          setCustomer({ ...CustomerInput, error_list: res.data.validate_err });
        }
      });
  };

  return (
    <Container>
      <div className="Container">
        <ValidatorForm
          onSubmit={AddCustomer}
          onError={() => null}
          encType="multipart/form-data"
        >
          <label htmlFor="exampleFormControlInput1" className="item">
            SN :
          </label>
          <input
            type="text"
            name="SN"
            onChange={handleInput}
            className="form-control"
            htmlFor="exampleFormControlInput1"
            value={CustomerInput.SN}
          />

          <label htmlFor="exampleFormControlInput1" className="item">
            LN :
          </label>
          <input
            type="text"
            name="LN"
            onChange={handleInput}
            className="form-control"
            htmlFor="exampleFormControlInput1"
            value={CustomerInput.LN}
          />

          <label htmlFor="exampleFormControlInput1" className="item">
            Description :
          </label>
          <input
            type="text"
            name="Description"
            onChange={handleInput}
            className="form-control"
            htmlFor="exampleFormControlInput1"
            value={CustomerInput.Description}
          />

          <label htmlFor="exampleFormControlInput1" className="item">
            Secteur d'Activité :
          </label>
          <input
            type="text"
            name="SecteurActivité"
            onChange={handleInput}
            className="form-control"
            htmlFor="exampleFormControlInput1"
            value={CustomerInput.SecteurActivité}
          />

          <label htmlFor="exampleFormControlInput1" className="item">
            Catégorie :
          </label>
          <input
            type="text"
            name="Categorie"
            onChange={handleInput}
            className="form-control"
            htmlFor="exampleFormControlInput1"
            value={CustomerInput.Categorie}
          />

          <label htmlFor="exampleFormControlInput1" className="item">
            Site Web :
          </label>
          <input
            type="text"
            name="SiteWeb"
            onChange={handleInput}
            className="form-control"
            htmlFor="exampleFormControlInput1"
            value={CustomerInput.SiteWeb}
          />

          <label htmlFor="exampleFormControlInput1" className="item">
            Addresse mail :
          </label>
          <input
            type="text"
            name="AddresseMail"
            onChange={handleInput}
            className="form-control"
            htmlFor="exampleFormControlInput1"
            value={CustomerInput.AddresseMail}
          />

          <Button className="upload" variant="contained" component="label">
            Upload Logo
            <input
              type="file"
              name="Logo"
              onChange={(e) => handleImage(e, "Logo")}
              hidden
            />
          </Button>

          <Button className="upload" variant="contained" component="label">
            Upload Organigramme
            <input
              type="file"
              name="Organigramme"
              onChange={(e) => handleImage(e, "Organigramme")}
              hidden
            />
          </Button>

          <Button className="upload" variant="contained" component="label">
            Upload Network Design
            <input
              type="file"
              name="NetworkDesign"
              onChange={(e) => handleImage(e, "NetworkDesign")}
              hidden
            />
          </Button>

          <Button
            color="primary"
            variant="contained"
            type="submit"
            className="item"
          >
            <Span sx={{ pl: 1, textTransform: "capitalize" }}>ADD</Span>
          </Button>
        </ValidatorForm>
      </div>
    </Container>
  );
}

export default AddCustom;
