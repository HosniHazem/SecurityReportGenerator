import React, { useState, useEffect } from "react";
import axios from "axios";
import { Card, CardContent, Typography, CardActions, Button, Grid } from "@mui/material";
import { axiosInstance } from "../../axios/axiosInstance";

const GlbPipList = () => {
  const [glbPips, setGlbPips] = useState([]);

  useEffect(() => {
    axiosInstance.get("/all-glbpip")
      .then((response) => {
        if (response.status === 200) {
          setGlbPips(response.data.GlbPip);
        }
      })
      .catch((error) => {
        console.error("Error fetching data:", error);
      });
  }, []);

  return (
    <Grid container spacing={2}>
      {glbPips.map((glbPip) => (
        <Grid item key={glbPip.ID} xs={12} sm={6} md={4}>
          <Card>
            <CardContent>
              <Typography variant="h6">{glbPip.Nom}</Typography>
              <Typography variant="subtitle1">{glbPip.Titre}</Typography>
              <Typography variant="body2">
                Email primaire: {glbPip["Adresse mail primaire"]}
              </Typography>
              <Typography variant="body2">
                Tél: {glbPip.Tél}
              </Typography>
            </CardContent>
            <CardActions>
              <Button size="small" color="primary">
                Learn More
              </Button>
            </CardActions>
          </Card>
        </Grid>
      ))}
    </Grid>
  );
};

export default GlbPipList;
