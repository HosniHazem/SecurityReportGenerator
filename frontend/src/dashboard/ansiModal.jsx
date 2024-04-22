import React from "react";
import Modal from "@mui/material/Modal";
import Box from "@mui/material/Box";
import Button from "@mui/material/Button";

const AnsiModal = ({ isOpen, onClose, project }) => {
  // Define your modal content here
  return (
    <Modal
      open={isOpen}
      onClose={onClose}
      aria-labelledby="modal-modal-title"
      aria-describedby="modal-modal-description"
    >
      <Box sx={{ position: "absolute", top: "50%", left: "50%", transform: "translate(-50%, -50%)", width: 400, bgcolor: "background.paper", borderRadius: 2, boxShadow: 24, p: 4 }}>
        <h2 id="modal-modal-title">Project Details</h2>
        <p id="modal-modal-description">
          <div>
            <div>glbpip: {project.glbpip > 0 ? "✔️" : "❌"}</div>
            <div>sow: {project.sow > 0 ? "✔️" : "❌"}</div>
            <div>customerSites: {project.customerSites > 0 ? "✔️" : "❌"}</div>
            <div>auditPrev: {project.auditPrev > 0 ? "✔️" : "❌"}</div>
            <div>answers: {project.answers > 0 ? "✔️" : "❌"}</div>
            <div>indicators: {project.indicators > 0 ? "✔️" : "❌"}</div>
            <div>rm_processus: {project.rm_processus > 0 ? "✔️" : "❌"}</div>
          </div>
        </p>
        <div>
          <Button variant="contained" onClick={() => window.location.href = `/ansi-report/${project.id}`}>Proceed</Button>
          <Button variant="contained" onClick={onClose}>Go Back</Button>
        </div>
      </Box>
    </Modal>
  );
};

export default AnsiModal;
