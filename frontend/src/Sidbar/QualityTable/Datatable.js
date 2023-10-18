import React from 'react';
import './DataTable.css'; // Import your CSS file for styling

const DataTable = ({ data }) => {
  return (
    <div className="table-container">
      <table className="data-table">
        <thead>
          <tr>
            <th>Column A</th>
            <th>Column B</th>
            <th>Column C</th>
            <th>Link</th>
          </tr>
        </thead>
        <tbody>
        {data.map((rowData, index) => (
            <tr key={index}>
              {rowData.map((cellData, cellIndex) => (
                <td key={cellIndex}>
                  {cellIndex === 3 ? (
                    <a href={cellData} target="_blank" rel="noopener noreferrer">
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