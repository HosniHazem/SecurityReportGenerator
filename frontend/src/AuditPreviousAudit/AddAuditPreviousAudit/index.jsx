import React from 'react'
import { Table, TableBody, TableCell, TableContainer, TableHead, TableRow, Paper } from "@mui/material";

export default function AddAuditPreviousAudit() {
  return (
    <div>
         <TableContainer component={Paper}>
      <Table>
        <TableHead>
          <TableRow>
            <TableCell style={{ width: '14.3061%' }}>
              <b>
                <span style={{ fontSize: '11pt' }}>Projet</span>
              </b>
            </TableCell>
            <TableCell style={{ width: '14.2857%' }}>Action</TableCell>
            <TableCell style={{ width: '14.2857%' }}>Criticité</TableCell>
            <TableCell style={{ width: '14.2857%' }}>Chargé de l'action</TableCell>
            <TableCell style={{ width: '14.2388%' }}>Charge(H/J)</TableCell>
            <TableCell style={{ width: '14.2857%' }}>Taux de réalisation</TableCell>
            <TableCell style={{ width: '14.2857%' }}>Evaluation</TableCell>
          </TableRow>
        </TableHead>
        <TableBody>
          <TableRow>
            <TableCell style={{ width: '14.3061%' }}></TableCell>
            <TableCell style={{ width: '14.2857%' }}></TableCell>
            <TableCell style={{ width: '14.2857%' }}></TableCell>
            <TableCell style={{ width: '14.2857%' }}></TableCell>
            <TableCell style={{ width: '14.2388%' }}></TableCell>
            <TableCell style={{ width: '14.2857%' }}></TableCell>
            <TableCell style={{ width: '14.2857%' }}></TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </TableContainer>
    </div>
  )
}
