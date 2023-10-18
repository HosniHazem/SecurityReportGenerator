import React from 'react';
import './DataTable.css'; // Import your CSS file for styling

const DataTable = ({ data }) => {
  if (!data || data.length === 0) {
    // Handle the case where data is undefined or an empty array
    return <div>No data available</div>;
  }

  // Assuming data has at least one row
  const headers = data[0];

  return (
    <div className="table-container">
      <table className="data-table">
        <thead>
          <tr>
            {headers.map((header, index) => (
              <th key={index}>{header}</th>
            ))}
          </tr>
        </thead>
        <tbody>
          {data.slice(1).map((rowData, rowIndex) => (
            <tr key={rowIndex}>
              {rowData.map((cellData, cellIndex) => (
                <td key={cellIndex}>
                  {cellIndex === 2 ? (
                    <a href={`http://webapp.smartskills.tn:8002/api/${cellData}`} target="_blank" rel="noopener noreferrer">
                      {cellData}
                    </a>
                  ) : (
                    cellData
                  )}
                </td>
              ))}
            </tr>
          ))}
        </tbody>
      </table>
    </div>
  );
};

export default DataTable;
