import React, { useEffect, useState } from 'react';
import { axiosInstance } from '../axios/axiosInstance';
import { Table, Button, Modal, Select } from 'antd';
import moment from 'moment';

const { Option } = Select;

const Logs = () => {
  const [logs, setLogs] = useState([]);
  const [sortedLogs, setSortedLogs] = useState([]);
  const [sortByNewest, setSortByNewest] = useState(true);
  const [filterName, setFilterName] = useState(null);
  const [modalVisible, setModalVisible] = useState(false);
  const [selectedBody, setSelectedBody] = useState('');

  useEffect(() => {
    axiosInstance
      .get(`all-logs`)
      .then((response) => {
        if (response.status === 200) {
          const filtered = response.data.activity_logs.filter(
            (log) => log.action !== 'GET api/all-logs'
          );
          console.log(response.data);

          // Sort by newest initially
          const sorted = [...filtered].sort(
            (a, b) => moment(b.created_at).valueOf() - moment(a.created_at).valueOf()
          );

          setLogs(filtered);
          setSortedLogs(sorted);
        }
      })
      .catch((error) => {
        console.error('Error fetching data:', error);
      });
  }, []);

  const handleSort = () => {
    const sorted = [...logs];

    sorted.sort((a, b) =>
      sortByNewest
        ? moment(b.created_at).valueOf() - moment(a.created_at).valueOf()
        : moment(a.created_at).valueOf() - moment(b.created_at).valueOf()
    );

    setLogs(sorted); // Update logs with the sorted array

    const filtered = sorted.filter((log) => (filterName ? log.user_name === filterName : true));

    setSortedLogs(filtered);
    setSortByNewest(!sortByNewest);
  };

  const handleFilter = (value) => {
    setFilterName(value);
    const filtered = logs.filter((log) => (value ? log.user_name === value : true));
    const sorted = [...filtered].sort(
      (a, b) => moment(b.created_at).valueOf() - moment(a.created_at).valueOf()
    );
    setSortedLogs(sorted);
  };

  const handleResetFilters = () => {
    setFilterName(null);
    const sorted = [...logs].sort(
      (a, b) => moment(b.created_at).valueOf() - moment(a.created_at).valueOf()
    );
    setSortedLogs(sorted);
  };

  const handleViewBody = (body) => {
    setSelectedBody(body);
    setModalVisible(true);
  };

  const renderBodyContent = (body) => {
    const bodyObject = JSON.parse(body);
  
    return (
      <>
        {Object.entries(bodyObject).map(([key, value], index) => (
          <div key={index}>
            {key !== 'Logo' && key !== 'Organigramme' && key !== 'Network_Design' ? (
              <p>{`${key}: ${value}`}</p>
            ) : (
              <Button type="link" onClick={() => window.open(value, '_blank')}>
                View Image {key === 'Logo' ? 'Logo' : key === 'Organigramme' ? 'Organigramme' : 'Network Design'}
              </Button>
            )}
          </div>
        ))}
      </>
    );
  };
  

  const columns = [
    {
      title: 'User Name',
      dataIndex: 'user_name',
      key: 'user_name',
      filters: [
        { text: 'Hajer', value: 'Hajer' },
        { text: 'Ayed', value: 'Ayed' },
        { text: 'Habib', value: 'Habib' },
        { text: 'Rawia', value: 'Rawia' },
      ],
      onFilter: (value, record) => record.user_name === value,
      render: (text) => <span>{text}</span>,
    },
    {
      title: 'Action',
      dataIndex: 'action',
      key: 'action',
    },
    {
      title: 'Body',
      dataIndex: 'body',
      key: 'body',
      render: (text, record) => (
        <Button type="link" onClick={() => handleViewBody(text)}>
          View Body
        </Button>
      ),
    },
    {
      title: 'Date',
      dataIndex: 'created_at',
      key: 'created_at',
      render: (text) => moment(text).format('DD-MM-YYYY'),
    },
    {
      title: 'Time',
      dataIndex: 'created_at',
      key: 'time',
      render: (text) => moment(text).format('HH:mm'),
    },
    {
      title: 'Reset Filters',
      dataIndex: 'reset_filters',
      key: 'reset_filters',
      render: () => (
        <Button type="link" onClick={handleResetFilters}>
          Reset Filters
        </Button>
      ),
    },
  ];

  return (
    <div>
      <h2>
        <Button onClick={handleSort}>
          {sortByNewest ? 'Sort by Newest' : 'Sort by Oldest'}
        </Button>
        <Select
          style={{ width: 120, marginLeft: 8 }}
          placeholder="Filter by Name"
          onChange={handleFilter}
          value={filterName}
          allowClear
        >
          <Option value="Hajer">Hajer</Option>
          <Option value="Ayed">Ayed</Option>
          <Option value="Habib">Habib</Option>
          <Option value="Rawia">Rawia</Option>
        </Select>
      </h2>
      <Table dataSource={sortedLogs} columns={columns} />

      <Modal
        title="Body Content"
        visible={modalVisible}
        onCancel={() => setModalVisible(false)}
        footer={null}
      >
        {selectedBody && renderBodyContent(selectedBody)}
      </Modal>
    </div>
  );
};

export default Logs;
