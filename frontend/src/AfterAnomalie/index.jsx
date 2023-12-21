import React from 'react';
import { useLocation } from 'react-router-dom';

export default function AfterANomalie(props) {
   
  return (
    <div>
     <div>
      <h2>After Anomalie</h2>
      <p>Accunetix: {props.accuentixNumber}</p>
      <p>Invicti: {props.invicti}</p>
      <p>Hcl: {props.hcl}</p>
    </div>
    </div>
  );
}
