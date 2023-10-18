import React from "react";

export default function AddGlbPip() {
  return (
    <table style={{ borderCollapse: "collapse", border: "1px solid black" }}>
      <tbody>
        <tr>
          <td style={{ border: "1px solid black", padding: "8px" }}>
            <p>
              <span>nom</span>
            </p>
          </td>
          <td style={{ border: "1px solid black", padding: "8px" }}>
            <p>
              <span>Titre</span>
            </p>
          </td>
          <td style={{ border: "1px solid black", padding: "8px" }}>
            <p>
              <span>Tél</span>
            </p>
          </td>
          <td style={{ border: "1px solid black", padding: "8px" }}>
            <p>
              <span>Mail</span>
            </p>
          </td>
        </tr>
        <tr>
          <td style={{ border: "1px solid black", padding: "8px" }}>
            <input type="text" placeholder="Enter nom" />
          </td>
          <td style={{ border: "1px solid black", padding: "8px" }}>
            <input type="text" placeholder="Enter Titre" />
          </td>

          <td style={{ border: "1px solid black", padding: "8px" }}>
            <input type="text" placeholder="Enter Tél" />
          </td>
          <td style={{ border: "1px solid black", padding: "8px" }}>
            <input type="text" placeholder="Enter Mail" />
          </td>
        </tr>
      </tbody>
    </table>
  );
}
