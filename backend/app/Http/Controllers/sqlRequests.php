<?php
/*
$data_serv = DB::table(DB::raw('(SELECT
vuln.`Host` as Hostip,
sow.Nom as Nom,
sow.field4 as field4,
COUNT(IF( `exploited_by_malware` = \'true\' , 1, NULL)) AS Exp_Malware,
COUNT(IF(vuln.`Risk` = \'Critical\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Critical_Ex,
COUNT(IF(vuln.`Risk` = \'High\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS High_Ex,
COUNT(IF(vuln.`Risk` = \'Medium\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Medium_Ex,
COUNT(IF(vuln.`Risk` = \'Critical\', 1, NULL)) AS Critical,
COUNT(IF(vuln.`Risk` = \'High\', 1, NULL)) AS High,
COUNT(IF(vuln.`Risk` = \'Medium\', 1, NULL)) AS Mediu,
COUNT(IF(vuln.`Risk` = \'Low\', 1, NULL)) AS Low,
COUNT(IF(vuln.`Risk` = \'FAILED\', 1, NULL)) AS FAILED2,
COUNT(IF(vuln.`Risk` = \'PASSED\', 1, NULL)) AS PASSED2
FROM vuln
LEFT JOIN `plugins` ON vuln.`Plugin ID` = plugins.id
RIGHT JOIN sow ON vuln.`Host` = sow.IP_Host
WHERE vuln.upload_id in (SELECT `ID` from uploadanomalies WHERE `ID_Projet`= ?) AND sow.Type=\'Serveur\' AND sow.IP_Host = vuln.Host AND sow.Projet=?
AND vuln.Port NOT IN (SELECT `Ports_List` FROM PortsMapping)
GROUP BY
`Host` ,  vuln.Name) t'))
->select('Hostip', 'Nom', 'field4', DB::raw('COUNT(IF(Exp_Malware>0,1,NULL)) as Exp_Malware'),
DB::raw('COUNT(IF(Critical_Ex>0,1,NULL)) as Critical_Ex'),
DB::raw('COUNT(IF(High_Ex>0,1,NULL)) as High_Ex'),
DB::raw('COUNT(IF(Medium_Ex>0,1,NULL)) as Medium_Ex'),
DB::raw('COUNT(IF(Critical>0,1,NULL)) as Critical'),
DB::raw('COUNT(IF(High>0,1,NULL)) as High'),
DB::raw('COUNT(IF(Mediu>0,1,NULL)) as Mediu'),
DB::raw('COUNT(IF(Low>0,1,NULL)) as Low'),
DB::raw('max(FAILED2) as FAILED2'),
DB::raw('max(PASSED2) as PASSED2'))
->setBindings([$id, $id])
->groupBy('hostip')
->orderByRaw('Critical_Ex DESC, High_Ex DESC, Exp_Malware DESC, Medium_Ex DESC, Critical DESC, High DESC')
->get();

$data_db = DB::table(DB::raw('(SELECT
vuln.`Host` as Hostip,
sow.Nom as Nom,
sow.field4 as field4 ,
COUNT(IF( `exploited_by_malware` = \'true\' , 1, NULL)) AS Exp_Malware,
COUNT(IF(vuln.`Risk` = \'Critical\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Critical_Ex,
COUNT(IF(vuln.`Risk` = \'High\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS High_Ex,
COUNT(IF(vuln.`Risk` = \'Medium\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Medium_Ex,
COUNT(IF(vuln.`Risk` = \'Critical\', 1, NULL)) AS Critical,
COUNT(IF(vuln.`Risk` = \'High\', 1, NULL)) AS High,
COUNT(IF(vuln.`Risk` = \'Medium\', 1, NULL)) AS Mediu,
COUNT(IF(vuln.`Risk` = \'Low\', 1, NULL)) AS Low,
COUNT(IF(vuln.`Risk` = \'FAILED\', 1, NULL)) AS FAILED2,
COUNT(IF(vuln.`Risk` = \'PASSED\', 1, NULL)) AS PASSED2
FROM vuln
LEFT JOIN `plugins` ON vuln.`Plugin ID` = plugins.id
RIGHT JOIN sow ON vuln.`Host` = sow.IP_Host
WHERE vuln.upload_id in (SELECT `ID` from uploadanomalies WHERE `ID_Projet`=?) AND sow.Type=\'Serveur\' AND sow.IP_Host = vuln.Host AND sow.Projet=?
AND vuln.Port IN (SELECT `Ports_List` FROM PortsMapping WHERE Utilisation=\'DB\')
GROUP BY
`Host` ,  vuln.Name) t'))
->select('Hostip', 'Nom', 'field4', DB::raw('COUNT(IF(Exp_Malware>0,1,NULL)) as Exp_Malware'),
DB::raw('COUNT(IF(Critical_Ex>0,1,NULL)) as Critical_Ex'),
DB::raw('COUNT(IF(High_Ex>0,1,NULL)) as High_Ex'),
DB::raw('COUNT(IF(Medium_Ex>0,1,NULL)) as Medium_Ex'),
DB::raw('COUNT(IF(Critical>0,1,NULL)) as Critical'),
DB::raw('COUNT(IF(High>0,1,NULL)) as High'),
DB::raw('COUNT(IF(Mediu>0,1,NULL)) as Mediu'),
DB::raw('COUNT(IF(Low>0,1,NULL)) as Low'),
DB::raw('max(FAILED2) as FAILED2'),
DB::raw('max(PASSED2) as PASSED2'))
->setBindings([$id, $id])
->whereRaw('(0,0,0,0,0,0,0) <> (Exp_Malware, Critical_Ex, High_Ex, Medium_Ex, Critical, High, Mediu)')
->groupBy('Hostip', 'Nom', 'field4')
->orderByRaw('Critical_Ex DESC, High_Ex DESC, Exp_Malware DESC, Medium_Ex DESC, Critical DESC, High DESC')
->get();

$data_rs = DB::table(DB::raw('(SELECT
vuln.`Host` as Hostip,
sow.Nom as Nom,
sow.field4 as field4,
COUNT(IF( `exploited_by_malware` = \'true\' , 1, NULL)) AS Exp_Malware,
COUNT(IF(vuln.`Risk` = \'Critical\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Critical_Ex,
COUNT(IF(vuln.`Risk` = \'High\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS High_Ex,
COUNT(IF(vuln.`Risk` = \'Medium\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Medium_Ex,
COUNT(IF(vuln.`Risk` = \'Critical\', 1, NULL)) AS Critical,
COUNT(IF(vuln.`Risk` = \'High\', 1, NULL)) AS High,
COUNT(IF(vuln.`Risk` = \'Medium\', 1, NULL)) AS Mediu,
COUNT(IF(vuln.`Risk` = \'Low\', 1, NULL)) AS Low,
COUNT(IF(vuln.`Risk` = \'FAILED\', 1, NULL)) AS FAILED2,
COUNT(IF(vuln.`Risk` = \'PASSED\', 1, NULL)) AS PASSED2
FROM vuln
LEFT JOIN `plugins` ON vuln.`Plugin ID` = plugins.id
RIGHT JOIN sow ON vuln.`Host` = sow.IP_Host
WHERE vuln.upload_id in (SELECT `ID` from uploadanomalies WHERE `ID_Projet`=?) AND sow.Type=\'R_S\' AND sow.IP_Host = vuln.Host AND sow.Projet=?
GROUP BY
`Host` ,  vuln.Name) t'))
->select('Hostip', 'Nom', 'field4', DB::raw('COUNT(IF(Exp_Malware>0,1,NULL)) as Exp_Malware'),
DB::raw('COUNT(IF(Critical_Ex>0,1,NULL)) as Critical_Ex'),
DB::raw('COUNT(IF(High_Ex>0,1,NULL)) as High_Ex'),
DB::raw('COUNT(IF(Medium_Ex>0,1,NULL)) as Medium_Ex'),
DB::raw('COUNT(IF(Critical>0,1,NULL)) as Critical'),
DB::raw('COUNT(IF(High>0,1,NULL)) as High'),
DB::raw('COUNT(IF(Mediu>0,1,NULL)) as Mediu'),
DB::raw('COUNT(IF(Low>0,1,NULL)) as Low'),
DB::raw('max(FAILED2) as FAILED2'),
DB::raw('max(PASSED2) as PASSED2'))
->setBindings([$id, $id])
->groupBy('hostip')
->orderByRaw('Critical_Ex DESC, High_Ex DESC, Exp_Malware DESC, Medium_Ex DESC, Critical DESC, High DESC')
->get();
$data_pc = DB::table(DB::raw('(SELECT
vuln.`Host` as Hostip,
sow.Nom as Nom,
sow.field4 as field4,
COUNT(IF( `exploited_by_malware` = \'true\' , 1, NULL)) AS Exp_Malware,
COUNT(IF(vuln.`Risk` = \'Critical\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Critical_Ex,
COUNT(IF(vuln.`Risk` = \'High\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS High_Ex,
COUNT(IF(vuln.`Risk` = \'Medium\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Medium_Ex,
COUNT(IF(vuln.`Risk` = \'Critical\', 1, NULL)) AS Critical,
COUNT(IF(vuln.`Risk` = \'High\', 1, NULL)) AS High,
COUNT(IF(vuln.`Risk` = \'Medium\', 1, NULL)) AS Mediu,
COUNT(IF(vuln.`Risk` = \'Low\', 1, NULL)) AS Low,
COUNT(IF(vuln.`Risk` = \'FAILED\', 1, NULL)) AS FAILED2,
COUNT(IF(vuln.`Risk` = \'PASSED\', 1, NULL)) AS PASSED2
FROM vuln
LEFT JOIN `plugins` ON vuln.`Plugin ID` = plugins.id
RIGHT JOIN sow ON vuln.`Host` = sow.IP_Host
WHERE vuln.upload_id in (SELECT `ID` from uploadanomalies WHERE `ID_Projet`=?) AND sow.Type=\'PC\' AND sow.IP_Host = vuln.Host AND sow.Projet=?
GROUP BY
`Host` ,  vuln.Name) t'))
->select('Hostip', 'Nom', 'field4', DB::raw('COUNT(IF(Exp_Malware>0,1,NULL)) as Exp_Malware'),
DB::raw('COUNT(IF(Critical_Ex>0,1,NULL)) as Critical_Ex'),
DB::raw('COUNT(IF(High_Ex>0,1,NULL)) as High_Ex'),
DB::raw('COUNT(IF(Medium_Ex>0,1,NULL)) as Medium_Ex'),
DB::raw('COUNT(IF(Critical>0,1,NULL)) as Critical'),
DB::raw('COUNT(IF(High>0,1,NULL)) as High'),
DB::raw('COUNT(IF(Mediu>0,1,NULL)) as Mediu'),
DB::raw('COUNT(IF(Low>0,1,NULL)) as Low'),
DB::raw('max(FAILED2) as FAILED2'),
DB::raw('max(PASSED2) as PASSED2'))
->setBindings([$id, $id])
->groupBy('hostip')
->orderByRaw('Critical_Ex DESC, High_Ex DESC, Exp_Malware DESC, Medium_Ex DESC, Critical DESC, High DESC')
->get();

$data_ext = DB::table(DB::raw('(SELECT
vuln.`Host` as Hostip,
sow.Nom as Nom,
sow.field4 as field4,
COUNT(IF( `exploited_by_malware` = \'true\' , 1, NULL)) AS Exp_Malware,
COUNT(IF(vuln.`Risk` = \'Critical\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Critical_Ex,
COUNT(IF(vuln.`Risk` = \'High\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS High_Ex,
COUNT(IF(vuln.`Risk` = \'Medium\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Medium_Ex,
COUNT(IF(vuln.`Risk` = \'Critical\', 1, NULL)) AS Critical,
COUNT(IF(vuln.`Risk` = \'High\', 1, NULL)) AS High,
COUNT(IF(vuln.`Risk` = \'Medium\', 1, NULL)) AS Mediu,
COUNT(IF(vuln.`Risk` = \'Low\', 1, NULL)) AS Low,
COUNT(IF(vuln.`Risk` = \'FAILED\', 1, NULL)) AS FAILED2,
COUNT(IF(vuln.`Risk` = \'PASSED\', 1, NULL)) AS PASSED2
FROM vuln
LEFT JOIN `plugins` ON vuln.`Plugin ID` = plugins.id
RIGHT JOIN sow ON vuln.`Host` = sow.IP_Host
WHERE vuln.upload_id in (SELECT `ID` from uploadanomalies WHERE `ID_Projet`=?) AND sow.Type=\'EXT\' AND sow.IP_Host = vuln.Host AND sow.Projet=?
GROUP BY
`Host` ,  vuln.Name) t'))
->select('Hostip', 'Nom', 'field4', DB::raw('COUNT(IF(Exp_Malware>0,1,NULL)) as Exp_Malware'),
DB::raw('COUNT(IF(Critical_Ex>0,1,NULL)) as Critical_Ex'),
DB::raw('COUNT(IF(High_Ex>0,1,NULL)) as High_Ex'),
DB::raw('COUNT(IF(Medium_Ex>0,1,NULL)) as Medium_Ex'),
DB::raw('COUNT(IF(Critical>0,1,NULL)) as Critical'),
DB::raw('COUNT(IF(High>0,1,NULL)) as High'),
DB::raw('COUNT(IF(Mediu>0,1,NULL)) as Mediu'),
DB::raw('COUNT(IF(Low>0,1,NULL)) as Low'),
DB::raw('max(FAILED2) as FAILED2'),
DB::raw('max(PASSED2) as PASSED2'))
->setBindings([$id, $id])
->groupBy('hostip')
->orderByRaw('Critical_Ex DESC, High_Ex DESC, Exp_Malware DESC, Medium_Ex DESC, Critical DESC, High DESC')
->get();



$data_apps = DB::table(DB::raw('(SELECT
vuln.`Host` as Hostip,
sow.Nom as Nom,
sow.field4 as field4,
COUNT(IF( `exploited_by_malware` = \'true\' , 1, NULL)) AS Exp_Malware,
COUNT(IF(vuln.`Risk` = \'Critical\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Critical_Ex,
COUNT(IF(vuln.`Risk` = \'High\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS High_Ex,
COUNT(IF(vuln.`Risk` = \'Medium\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Medium_Ex,
COUNT(IF(vuln.`Risk` = \'Critical\', 1, NULL)) AS Critical,
COUNT(IF(vuln.`Risk` = \'High\', 1, NULL)) AS High,
COUNT(IF(vuln.`Risk` = \'Medium\', 1, NULL)) AS Mediu,
COUNT(IF(vuln.`Risk` = \'Low\', 1, NULL)) AS Low,
COUNT(IF(vuln.`Risk` = \'FAILED\', 1, NULL)) AS FAILED2,
COUNT(IF(vuln.`Risk` = \'PASSED\', 1, NULL)) AS PASSED2
FROM vuln
LEFT JOIN `plugins` ON vuln.`Plugin ID` = plugins.id
RIGHT JOIN sow ON vuln.`Host` = sow.IP_Host
WHERE vuln.upload_id in (SELECT `ID` from uploadanomalies WHERE `ID_Projet`=?) AND sow.Type=\'Apps\' AND sow.IP_Host = vuln.Host AND sow.Projet=?
AND vuln.Port IN (SELECT `Ports_List` FROM PortsMapping WHERE Utilisation=\'Apps\')
GROUP BY
`Host` ,  vuln.Name) t'))
->select('Hostip', 'Nom', 'field4', DB::raw('COUNT(IF(Exp_Malware>0,1,NULL)) as Exp_Malware'),
DB::raw('COUNT(IF(Critical_Ex>0,1,NULL)) as Critical_Ex'),
DB::raw('COUNT(IF(High_Ex>0,1,NULL)) as High_Ex'),
DB::raw('COUNT(IF(Medium_Ex>0,1,NULL)) as Medium_Ex'),
DB::raw('COUNT(IF(Critical>0,1,NULL)) as Critical'),
DB::raw('COUNT(IF(High>0,1,NULL)) as High'),
DB::raw('COUNT(IF(Mediu>0,1,NULL)) as Mediu'),
DB::raw('COUNT(IF(Low>0,1,NULL)) as Low'),
DB::raw('max(FAILED2) as FAILED2'),
DB::raw('max(PASSED2) as PASSED2'))
->setBindings([$id, $id])
->groupBy('Hostip', 'Nom', 'field4')
->orderByRaw('Critical_Ex DESC, High_Ex DESC, Exp_Malware DESC, Medium_Ex DESC, Critical DESC, High DESC')
->get();



$data_mails = DB::table(DB::raw('(SELECT
vuln.`Host` as Hostip,
sow.Nom as Nom,
sow.field4 as field4,
COUNT(IF( `exploited_by_malware` = \'true\' , 1, NULL)) AS Exp_Malware,
COUNT(IF(vuln.`Risk` = \'Critical\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Critical_Ex,
COUNT(IF(vuln.`Risk` = \'High\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS High_Ex,
COUNT(IF(vuln.`Risk` = \'Medium\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Medium_Ex,
COUNT(IF(vuln.`Risk` = \'Critical\', 1, NULL)) AS Critical,
COUNT(IF(vuln.`Risk` = \'High\', 1, NULL)) AS High,
COUNT(IF(vuln.`Risk` = \'Medium\', 1, NULL)) AS Mediu,
COUNT(IF(vuln.`Risk` = \'Low\', 1, NULL)) AS Low,
COUNT(IF(vuln.`Risk` = \'FAILED\', 1, NULL)) AS FAILED2,
COUNT(IF(vuln.`Risk` = \'PASSED\', 1, NULL)) AS PASSED2
FROM vuln
LEFT JOIN `plugins` ON vuln.`Plugin ID` = plugins.id
RIGHT JOIN sow ON vuln.`Host` = sow.IP_Host
WHERE vuln.upload_id in (SELECT `ID` from uploadanomalies WHERE `ID_Projet`=?) AND sow.Type=\'Serveur\' AND sow.IP_Host = vuln.Host AND sow.Projet=?
AND vuln.Port IN (SELECT `Ports_List` FROM PortsMapping WHERE Utilisation=\'Mail\')
GROUP BY
`Host` ,  vuln.Name) t'))
->select('Hostip', 'Nom', 'field4', DB::raw('COUNT(IF(Exp_Malware>0,1,NULL)) as Exp_Malware'),
DB::raw('COUNT(IF(Critical_Ex>0,1,NULL)) as Critical_Ex'),
DB::raw('COUNT(IF(High_Ex>0,1,NULL)) as High_Ex'),
DB::raw('COUNT(IF(Medium_Ex>0,1,NULL)) as Medium_Ex'),
DB::raw('COUNT(IF(Critical>0,1,NULL)) as Critical'),
DB::raw('COUNT(IF(High>0,1,NULL)) as High'),
DB::raw('COUNT(IF(Mediu>0,1,NULL)) as Mediu'),
DB::raw('COUNT(IF(Low>0,1,NULL)) as Low'),
DB::raw('max(FAILED2) as FAILED2'),
DB::raw('max(PASSED2) as PASSED2'))
->setBindings([$id, $id])
->groupBy('Hostip', 'Nom', 'field4')
->orderByRaw('Critical_Ex DESC, High_Ex DESC, Exp_Malware DESC, Medium_Ex DESC, Critical DESC, High DESC')
->get();



$data_voip = DB::table(DB::raw('(SELECT
vuln.`Host` as Hostip,
sow.Nom as Nom,
sow.field4 as field4,
COUNT(IF( `exploited_by_malware` = \'true\' , 1, NULL)) AS Exp_Malware,
COUNT(IF(vuln.`Risk` = \'Critical\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Critical_Ex,
COUNT(IF(vuln.`Risk` = \'High\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS High_Ex,
COUNT(IF(vuln.`Risk` = \'Medium\' AND ( `exploit_available` = \'true\' ), 1, NULL)) AS Medium_Ex,
COUNT(IF(vuln.`Risk` = \'Critical\', 1, NULL)) AS Critical,
COUNT(IF(vuln.`Risk` = \'High\', 1, NULL)) AS High,
COUNT(IF(vuln.`Risk` = \'Medium\', 1, NULL)) AS Mediu,
COUNT(IF(vuln.`Risk` = \'Low\', 1, NULL)) AS Low,
COUNT(IF(vuln.`Risk` = \'FAILED\', 1, NULL)) AS FAILED2,
COUNT(IF(vuln.`Risk` = \'PASSED\', 1, NULL)) AS PASSED2
FROM vuln
LEFT JOIN `plugins` ON vuln.`Plugin ID` = plugins.id
RIGHT JOIN sow ON vuln.`Host` = sow.IP_Host
WHERE vuln.upload_id in (SELECT `ID` from uploadanomalies WHERE `ID_Projet`=?) AND sow.Type=\'Serveur\' AND sow.IP_Host = vuln.Host AND sow.Projet=?
AND vuln.Port IN (SELECT `Ports_List` FROM PortsMapping WHERE Utilisation=\'voip\')
GROUP BY
`Host` ,  vuln.Name) t'))
->select('Hostip', 'Nom', 'field4', DB::raw('COUNT(IF(Exp_Malware>0,1,NULL)) as Exp_Malware'),
DB::raw('COUNT(IF(Critical_Ex>0,1,NULL)) as Critical_Ex'),
DB::raw('COUNT(IF(High_Ex>0,1,NULL)) as High_Ex'),
DB::raw('COUNT(IF(Medium_Ex>0,1,NULL)) as Medium_Ex'),
DB::raw('COUNT(IF(Critical>0,1,NULL)) as Critical'),
DB::raw('COUNT(IF(High>0,1,NULL)) as High'),
DB::raw('COUNT(IF(Mediu>0,1,NULL)) as Mediu'),
DB::raw('COUNT(IF(Low>0,1,NULL)) as Low'),
DB::raw('max(FAILED2) as FAILED2'),
DB::raw('max(PASSED2) as PASSED2'))
->setBindings([$id, $id])
->groupBy('Hostip', 'Nom', 'field4')
->orderByRaw('Critical_Ex DESC, High_Ex DESC, Exp_Malware DESC, Medium_Ex DESC, Critical DESC, High DESC')
->get();


///////Table2


$data2_serv = DB::table('vuln')
->select('Risk', 'plugins.synopsis', DB::raw('count(DISTINCT Risk, plugins.synopsis, vuln.Host) as count'), DB::raw('GROUP_CONCAT(DISTINCT HOST) AS nbr'), 'exploited_by_malware', 'exploit_available')
->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet = ?)', [$id])
->where('sow.Type', '=', 'Serveur')
->whereRaw('sow.IP_Host = vuln.Host')
->where('sow.Projet', '=', $id)
->whereRaw('vuln.Port NOT IN (SELECT Ports_List FROM PortsMapping)')
->whereIn('Risk', ['Critical', 'High', 'Medium', 'Low'])
->groupBy(['Risk', 'plugins.synopsis'])
->orderByRaw('exploited_by_malware DESC, exploit_available DESC, Risk ASC, nbr DESC')
->get();

$data2_db = DB::table('vuln')
->select('Risk','plugins.synopsis',DB::raw('count(DISTINCT Risk,plugins.synopsis,vuln.Host) As count'),
DB::raw('GROUP_CONCAT(DISTINCT HOST) AS nbr'),'exploited_by_malware','exploit_available')
->leftJoin('plugins','vuln.Plugin ID','=','plugins.id')
->rightJoin('sow','vuln.Host','=','sow.IP_Host')
->whereRaw('vuln.upload_id IN (SELECT `ID`from uploadanomalies WHERE ID_Projet = ?)', [$id])
->where('sow.Type','=','Serveur')
->whereRaw('sow.IP_Host = vuln.Host')
->where('sow.Projet','=',$id)
->whereIn('Risk',['Critical', 'High', 'Medium', 'Low'])
->whereRaw('vuln.Port IN (SELECT Ports_List FROM PortsMapping WHERE Utilisation=\'DB\')')
->groupBy(['Risk','plugins.synopsis'])
->orderByRaw('exploited_by_malware DESC,exploit_available DESC,Risk ASC,nbr DESC')
->get();


$data2_rs = DB::table('vuln')
->select('Risk', 'plugins.synopsis', DB::raw('count(DISTINCT Risk, plugins.synopsis, vuln.Host) as count'),
DB::raw('GROUP_CONCAT(DISTINCT HOST) AS nbr'), 'exploited_by_malware', 'exploit_available')
->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet = ?)', [$id])
->where('sow.Type', '=', 'R_S')
->whereRaw('sow.IP_Host = vuln.Host')
->where('sow.Projet', '=', $id)
->whereIn('Risk', ['Critical', 'High', 'Medium', 'Low'])
->groupBy(['Risk', 'plugins.synopsis'])
->orderByRaw('exploited_by_malware DESC, exploit_available DESC, Risk ASC, nbr DESC')
->get();
$data2_pc = DB::table('vuln')
->select('Risk', 'plugins.synopsis', DB::raw('count(DISTINCT Risk, plugins.synopsis, vuln.Host) as count'),
DB::raw('GROUP_CONCAT(DISTINCT HOST) AS nbr'), 'exploited_by_malware', 'exploit_available')
->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet = ?)', [$id])
->where('sow.Type', '=', 'PC')
->whereRaw('sow.IP_Host = vuln.Host')
->where('sow.Projet', '=', $id)
->whereIn('Risk', ['Critical', 'High', 'Medium', 'Low'])
->groupBy(['Risk', 'plugins.synopsis'])
->orderByRaw('exploited_by_malware DESC, exploit_available DESC, Risk ASC, nbr DESC')
->get();

$data2_ext = DB::table('vuln')
->select('Risk', 'plugins.synopsis', DB::raw('count(DISTINCT Risk, plugins.synopsis, vuln.Host) as count'),
DB::raw('GROUP_CONCAT(DISTINCT HOST) AS nbr'), 'exploited_by_malware', 'exploit_available')
->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet = ?)', [$id])
->where('sow.Type', '=', 'EXT')
->whereRaw('sow.IP_Host = vuln.Host')
->where('sow.Projet', '=', $id)
->whereIn('Risk', ['Critical', 'High', 'Medium', 'Low'])
->groupBy(['Risk', 'plugins.synopsis'])
->orderByRaw('exploited_by_malware DESC, exploit_available DESC, Risk ASC, nbr DESC')
->get();

$data2_apps = DB::table('vuln')
->select('Risk', 'plugins.synopsis', DB::raw('count(DISTINCT Risk, plugins.synopsis, vuln.Host) as count'), DB::raw('GROUP_CONCAT(DISTINCT HOST) AS nbr'), 'exploited_by_malware', 'exploit_available')
->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet = ?)', [$id])
->where('sow.Type', '=', 'Apps')
->whereRaw('sow.IP_Host = vuln.Host')
->where('sow.Projet', '=', $id)
->whereRaw('vuln.Port IN (SELECT Ports_List FROM PortsMapping WHERE UTILISATION=\'Apps\')')
->whereIn('Risk', ['Critical', 'High', 'Medium', 'Low'])
->groupBy(['Risk', 'plugins.synopsis'])
->orderByRaw('exploited_by_malware DESC, exploit_available DESC, Risk ASC, nbr DESC')
->get();
$data2_mails = DB::table('vuln')
->select('Risk', 'plugins.synopsis', DB::raw('count(DISTINCT Risk, plugins.synopsis, vuln.Host) as count'), DB::raw('GROUP_CONCAT(DISTINCT HOST) AS nbr'), 'exploited_by_malware', 'exploit_available')
->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet = ?)', [$id])
->where('sow.Type', '=', 'Apps')
->whereRaw('sow.IP_Host = vuln.Host')
->where('sow.Projet', '=', $id)
->whereRaw('vuln.Port IN (SELECT Ports_List FROM PortsMapping WHERE UTILISATION=\'Mail\')')
->whereIn('Risk', ['Critical', 'High', 'Medium', 'Low'])
->groupBy(['Risk', 'plugins.synopsis'])
->orderByRaw('exploited_by_malware DESC, exploit_available DESC, Risk ASC, nbr DESC')
->get();



$data2_voip = DB::table('vuln')
->select('Risk', 'plugins.synopsis', DB::raw('count(DISTINCT Risk, plugins.synopsis, vuln.Host) as count'),
DB::raw('GROUP_CONCAT(DISTINCT HOST) AS nbr'), 'exploited_by_malware', 'exploit_available')
->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet = ?)', [$id])
->where('sow.Type', '=', 'Apps')
->whereRaw('sow.IP_Host = vuln.Host')
->where('sow.Projet', '=', $id)
->whereRaw('vuln.Port IN (SELECT Ports_List FROM PortsMapping WHERE UTILISATION=\'Voip\')')
->whereIn('Risk', ['Critical', 'High', 'Medium', 'Low'])
->groupBy(['Risk', 'plugins.synopsis'])
->orderByRaw('exploited_by_malware DESC, exploit_available DESC, Risk ASC, nbr DESC')
->get();










//////////////table3


$data3_serv = DB::table('vuln')
->select('vuln.Risk','vuln.Name','plugins.cvss3_base_score AS Score','vuln.Plugin ID As Plugin_Id','plugins.age_of_vuln','vuln.Plugin Output As Plugin_Output',
DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) AS Port'),
DB::raw('GROUP_CONCAT(DISTINCT vuln.Host) AS Elt_Impactes'),
DB::raw('COUNT(*) AS nbr'),'vuln.Plugin ID','plugins.synopsis','plugins.description','plugins.solution','plugins.see_also AS See','plugins.exploit_available','plugins.exploit_framework_metasploit','plugins.exploit_framework_canvas','plugins.exploit_framework_core','plugins.exploited_by_malware','plugins.age_of_vuln')
->leftJoin('plugins','vuln.Plugin ID','=','plugins.id')
->rightJoin('sow','vuln.Host','=','sow.IP_Host')
->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet=?)', [$id])
->where('sow.Type','=','Serveur')
->whereRaw('sow.IP_Host = vuln.Host')
->where('sow.Projet','=',$id)
->whereRaw('vuln.Port NOT IN (SELECT Ports_List FROM PortsMapping)')
->whereIn('Risk',['Critical', 'High', 'Medium','Low'])
->groupBy(['Risk','vuln.Synopsis'])
->orderByRaw('exploited_by_malware DESC,exploit_available DESC,Risk ASC,nbr DESC')
->get();

 $data3_db = DB::table('vuln')
 ->select('vuln.Risk','vuln.Name','plugins.cvss3_base_score AS Score','vuln.Plugin ID As Plugin_Id','plugins.age_of_vuln','vuln.Plugin Output As Plugin_Output',
DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) AS Port'),
DB::raw('GROUP_CONCAT(DISTINCT vuln.Host) AS Elt_Impactes'),
DB::raw('COUNT(*) AS nbr'),'vuln.Plugin ID','plugins.synopsis','plugins.description','plugins.solution','plugins.see_also AS See','plugins.exploit_available','plugins.exploit_framework_metasploit','plugins.exploit_framework_canvas','plugins.exploit_framework_core','plugins.exploited_by_malware','plugins.age_of_vuln')
->leftJoin('plugins','vuln.Plugin ID','=','plugins.id')
->rightJoin('sow','vuln.Host','=','sow.IP_Host')
->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet=?)', [$id])
->where('sow.Type','=','Serveur')
->whereRaw('sow.IP_Host = vuln.Host')
->where('sow.Projet','=',$id)
->whereRaw('vuln.Port IN (SELECT Ports_List FROM PortsMapping)')
->whereRaw('vuln.Port IN (SELECT Ports_List FROM PortsMapping WHERE UTILISATION=\'DB\')')
->whereIn('Risk',['Critical', 'High', 'Medium', 'Low'])
->groupBy(['Risk','vuln.Synopsis'])
->orderByRaw('exploited_by_malware DESC,exploit_available DESC,Risk ASC,nbr DESC ')
->get();






$data3_rs = DB::table('vuln')
 ->select('vuln.Risk','vuln.Name','plugins.cvss3_base_score AS Score','vuln.Plugin ID As Plugin_Id','plugins.age_of_vuln','vuln.Plugin Output As Plugin_Output',
DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) AS Port'),
DB::raw('GROUP_CONCAT(DISTINCT vuln.Host) AS Elt_Impactes'),
DB::raw('COUNT(*) AS nbr'),'vuln.Plugin ID','plugins.synopsis','plugins.description','plugins.solution','plugins.see_also AS See','plugins.exploit_available','plugins.exploit_framework_metasploit','plugins.exploit_framework_canvas','plugins.exploit_framework_core','plugins.exploited_by_malware','plugins.age_of_vuln')
->leftJoin('plugins','vuln.Plugin ID','=','plugins.id')
->rightJoin('sow','vuln.Host','=','sow.IP_Host')
->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet=?)', [$id])
->where('sow.Type','=','R_S')
->whereRaw('sow.IP_Host = vuln.Host')
->where('sow.Projet','=',$id)
->whereIn('Risk',['Critical', 'High', 'Medium', 'Low'])
->groupBy(['Risk','vuln.Synopsis'])
->orderByRaw('exploited_by_malware DESC,exploit_available DESC,Risk ASC,nbr DESC')
->get();

$data3_pc = DB::table('vuln')
 ->select('vuln.Risk','vuln.Name','plugins.cvss3_base_score AS Score','vuln.Plugin ID As Plugin_Id','plugins.age_of_vuln','vuln.Plugin Output As Plugin_Output',
DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) AS Port'),
DB::raw('GROUP_CONCAT(DISTINCT vuln.Host) AS Elt_Impactes'),
DB::raw('COUNT(*) AS nbr'),'vuln.Plugin ID','plugins.synopsis','plugins.description','plugins.solution','plugins.see_also AS See','plugins.exploit_available','plugins.exploit_framework_metasploit','plugins.exploit_framework_canvas','plugins.exploit_framework_core','plugins.exploited_by_malware','plugins.age_of_vuln')
->leftJoin('plugins','vuln.Plugin ID','=','plugins.id')
->rightJoin('sow','vuln.Host','=','sow.IP_Host')
->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet=?)', [$id])
->where('sow.Type','=','PC')
->whereRaw('sow.IP_Host = vuln.Host')
->where('sow.Projet','=',$id)

->whereIn('Risk',['Critical', 'High', 'Medium', 'Low'])
->groupBy(['Risk','vuln.Synopsis'])
->orderByRaw('exploited_by_malware DESC,exploit_available DESC,Risk ASC,nbr DESC')
->get();


$data3_ext = DB::table('vuln')
 ->select('vuln.Risk','vuln.Name','plugins.cvss3_base_score AS Score','vuln.Plugin ID As Plugin_Id','plugins.age_of_vuln','vuln.Plugin Output As Plugin_Output',
DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) AS Port'),
DB::raw('GROUP_CONCAT(DISTINCT vuln.Host) AS Elt_Impactes'),
DB::raw('COUNT(*) AS nbr'),'vuln.Plugin ID','plugins.synopsis','plugins.description','plugins.solution','plugins.see_also AS See','plugins.exploit_available','plugins.exploit_framework_metasploit','plugins.exploit_framework_canvas','plugins.exploit_framework_core','plugins.exploited_by_malware','plugins.age_of_vuln')
->leftJoin('plugins','vuln.Plugin ID','=','plugins.id')
->rightJoin('sow','vuln.Host','=','sow.IP_Host')
->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet=?)', [$id])
->where('sow.Type','=','EXT')
->whereRaw('sow.IP_Host = vuln.Host')
->where('sow.Projet','=',$id)
->whereIn('Risk',['Critical', 'High', 'Medium', 'Low'])
->groupBy(['Risk','vuln.Synopsis'])
->orderByRaw('exploited_by_malware DESC,exploit_available DESC,Risk ASC,nbr DESC')
->get();


$data3_apps = DB::table('vuln')
 ->select('vuln.Risk','vuln.Name','plugins.cvss3_base_score AS Score','vuln.Plugin ID As Plugin_Id','plugins.age_of_vuln','vuln.Plugin Output As Plugin_Output',
DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) AS Port'),
DB::raw('GROUP_CONCAT(DISTINCT vuln.Host) AS Elt_Impactes'),
DB::raw('COUNT(*) AS nbr'),'vuln.Plugin ID','plugins.synopsis','plugins.description','plugins.solution','plugins.see_also AS See','plugins.exploit_available','plugins.exploit_framework_metasploit','plugins.exploit_framework_canvas','plugins.exploit_framework_core','plugins.exploited_by_malware','plugins.age_of_vuln')
->leftJoin('plugins','vuln.Plugin ID','=','plugins.id')
->rightJoin('sow','vuln.Host','=','sow.IP_Host')
->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet=?)', [$id])
->where('sow.Type','=','Apps')
->whereRaw('sow.IP_Host = vuln.Host')
->where('sow.Projet','=',$id)
->whereRaw('vuln.Port IN (SELECT Ports_List FROM PortsMapping WHERE UTILISATION=\'Apps\')')
->whereIn('Risk',['Critical', 'High', 'Medium', 'Low'])
->groupBy(['Risk','vuln.Synopsis'])
->orderByRaw('exploited_by_malware DESC,exploit_available DESC,Risk ASC,nbr DESC')
->get();



$data3_mails = DB::table('vuln')
 ->select('vuln.Risk','vuln.Name','plugins.cvss3_base_score AS Score','vuln.Plugin ID As Plugin_Id','plugins.age_of_vuln','vuln.Plugin Output As Plugin_Output',
DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) AS Port'),
DB::raw('GROUP_CONCAT(DISTINCT vuln.Host) AS Elt_Impactes'),
DB::raw('COUNT(*) AS nbr'),'vuln.Plugin ID','plugins.synopsis','plugins.description','plugins.solution','plugins.see_also AS See','plugins.exploit_available','plugins.exploit_framework_metasploit','plugins.exploit_framework_canvas','plugins.exploit_framework_core','plugins.exploited_by_malware','plugins.age_of_vuln')
->leftJoin('plugins','vuln.Plugin ID','=','plugins.id')
->rightJoin('sow','vuln.Host','=','sow.IP_Host')
->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet=?)', [$id])
->where('sow.Type','=','Serveur')
->whereRaw('sow.IP_Host = vuln.Host')
->where('sow.Projet','=',$id)
->whereRaw('vuln.Port IN (SELECT Ports_List FROM PortsMapping WHERE UTILISATION=\'Mail\')')
->whereIn('Risk',['Critical', 'High', 'Medium', 'Low'])
->groupBy(['Risk','vuln.Synopsis'])
->orderByRaw('exploited_by_malware DESC,exploit_available DESC,Risk ASC,nbr DESC')
->get();



$data3_voip = DB::table('vuln')
 ->select('vuln.Risk','vuln.Name','plugins.cvss3_base_score AS Score','vuln.Plugin ID As Plugin_Id','plugins.age_of_vuln','vuln.Plugin Output As Plugin_Output',
DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) AS Port'),
DB::raw('GROUP_CONCAT(DISTINCT vuln.Host) AS Elt_Impactes'),
DB::raw('COUNT(*) AS nbr'),'vuln.Plugin ID','plugins.synopsis','plugins.description','plugins.solution','plugins.see_also AS See','plugins.exploit_available','plugins.exploit_framework_metasploit','plugins.exploit_framework_canvas','plugins.exploit_framework_core','plugins.exploited_by_malware','plugins.age_of_vuln')
->leftJoin('plugins','vuln.Plugin ID','=','plugins.id')
->rightJoin('sow','vuln.Host','=','sow.IP_Host')
->whereRaw('vuln.upload_id IN (SELECT ID from uploadanomalies WHERE ID_Projet=?)', [$id])
->where('sow.Type','=','Serveur')
->whereRaw('sow.IP_Host = vuln.Host')
->where('sow.Projet','=',$id)
->whereRaw('vuln.Port IN (SELECT Ports_List FROM PortsMapping WHERE UTILISATION=\'Voip\')')
->whereIn('Risk',['Critical', 'High', 'Medium', 'Low'])
->groupBy(['Risk','vuln.Synopsis'])
->orderByRaw('exploited_by_malware DESC,exploit_available DESC,Risk ASC,nbr DESC')
->get();*/

$data21_serv = DB::table('vuln')
->select('vuln.Host', 'vuln.Name','Risk','plugins.synopsis',
    DB::raw("IF( plugins.exploited_by_malware='true' , 'exploitable par malware', IF( plugins.exploit_available = 'true', 'exploit disponible', NULL)) AS exploitability"),
    DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) as ports'),
    'plugins.age_of_vuln')
->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
->whereIn('vuln.upload_id', function($query) use ($id) {
    $query->select('ID')
          ->from('uploadanomalies')
          ->where('ID_Projet', '=', $id);
 })
->where('sow.Type', '=', 'Serveur')
->whereColumn('sow.IP_Host', 'vuln.Host')
->where('sow.Projet', '=', $id)
->whereNotIn('vuln.Port', function($query) {
    $query->select('Ports_List')
          ->from('PortsMapping');
})
->whereIn('Risk', ['Critical', 'High', 'Medium', 'Low'])
->groupBy('vuln.Host', 'vuln.Name')
->orderBy('vuln.Host')
->orderByDesc('exploitability')
->orderByDesc('Risk')
->get();
/*





 $data21_db = DB::table('vuln')
->select('vuln.Host', 'vuln.Name','Risk','plugins.synopsis',
    DB::raw("IF( plugins.exploited_by_malware='true' , 'exploitable par malware', IF( plugins.exploit_available = 'true', 'exploit disponible', NULL)) AS exploitability"),
    DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) as ports'),
    'plugins.age_of_vuln')
->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
->whereIn('vuln.upload_id', function($query) use ($id) {
    $query->select('ID')
          ->from('uploadanomalies')
          ->where('ID_Projet', '=', $id);
 })
->where('sow.Type', '=', 'Serveur')
->whereColumn('sow.IP_Host', 'vuln.Host')
->where('sow.Projet', '=', $id)
->whereIn('vuln.Port', function($query) {
    $query->select('Ports_List')
          ->from('PortsMapping')
          ->where('Utilisation', '=', 'DB');
})
->whereIn('Risk', ['Critical', 'High', 'Medium', 'Low'])
->groupBy('vuln.Host', 'vuln.Name')
->orderBy('vuln.Host')
->orderByDesc('exploitability')
->orderByDesc('Risk')
->get();

$data21_pc = DB::table('vuln')
->select('vuln.Host', 'vuln.Name','Risk','plugins.synopsis',
    DB::raw("IF( plugins.exploited_by_malware='true' , 'exploitable par malware', IF( plugins.exploit_available = 'true', 'exploit disponible', NULL)) AS exploitability"),
    DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) as ports'),
    'plugins.age_of_vuln')
->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
->whereIn('vuln.upload_id', function($query) use ($id) {
    $query->select('ID')
          ->from('uploadanomalies')
          ->where('ID_Projet', '=', $id);
 })
->where('sow.Type', '=', 'PC')
->whereColumn('sow.IP_Host', 'vuln.Host')
->where('sow.Projet', '=', $id)
->whereIn('Risk', ['Critical', 'High', 'Medium', 'Low'])
->groupBy('vuln.Host', 'vuln.Name')
->orderBy('vuln.Host')
->orderByDesc('exploitability')
->orderByDesc('Risk')
->get();

$data21_ext = DB::table('vuln')
->select('vuln.Host', 'vuln.Name','Risk','plugins.synopsis',
    DB::raw("IF( plugins.exploited_by_malware='true' , 'exploitable par malware', IF( plugins.exploit_available = 'true', 'exploit disponible', NULL)) AS exploitability"),
    DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) as ports'),
    'plugins.age_of_vuln')
->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
->where('sow.Type', '=', 'EXT')
->whereColumn('sow.IP_Host', 'vuln.Host')
->where('sow.Projet', '=', $id)
->whereIn('Risk', ['Critical', 'High', 'Medium', 'Low'])
->groupBy('vuln.Host', 'vuln.Name')
->orderBy('vuln.Host')
->orderByDesc('exploitability')
->orderByDesc('Risk')
->get();

$data21_apps = DB::table('vuln')
->select('vuln.Host', 'vuln.Name','Risk','plugins.synopsis',
    DB::raw("IF( plugins.exploited_by_malware='true' , 'exploitable par malware', IF( plugins.exploit_available = 'true', 'exploit disponible', NULL)) AS exploitability"),
    DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) as ports'),
    'plugins.age_of_vuln')
->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
->whereIn('vuln.upload_id', function($query) use ($id) {
    $query->select('ID')
          ->from('uploadanomalies')
          ->where('ID_Projet', '=', $id);
 })
->where('sow.Type', '=', 'Apps')
->whereColumn('sow.IP_Host', 'vuln.Host')
->where('sow.Projet', '=', $id)
->whereIn('vuln.Port', function($query) {
    $query->select('Ports_List')
          ->from('PortsMapping')
          ->where('Utilisation', '=', 'Apps');
})
->whereIn('Risk', ['Critical', 'High', 'Medium', 'Low'])
->groupBy('vuln.Host', 'vuln.Name')
->orderBy('vuln.Host')
->orderByDesc('exploitability')
->orderByDesc('Risk')
->get();

$data21_mails = DB::table('vuln')
->select('vuln.Host', 'vuln.Name','Risk','plugins.synopsis',
    DB::raw("IF( plugins.exploited_by_malware='true' , 'exploitable par malware', IF( plugins.exploit_available = 'true', 'exploit disponible', NULL)) AS exploitability"),
    DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) as ports'),
    'plugins.age_of_vuln')
->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
->whereIn('vuln.upload_id', function($query) use ($id) {
    $query->select('ID')
          ->from('uploadanomalies')
          ->where('ID_Projet', '=', $id);
 })
->where('sow.Type', '=', 'Serveur')
->whereColumn('sow.IP_Host', 'vuln.Host')
->where('sow.Projet', '=', $id)
->whereIn('vuln.Port', function($query) {
    $query->select('Ports_List')
          ->from('PortsMapping')
          ->where('Utilisation', '=', 'Mail');
})
->whereIn('Risk', ['Critical', 'High', 'Medium', 'Low'])
->groupBy('vuln.Host', 'vuln.Name')
->orderBy('vuln.Host')
->orderByDesc('exploitability')
->orderByDesc('Risk')
->get();

$data21_voip = DB::table('vuln')
->select('vuln.Host', 'vuln.Name','Risk','plugins.synopsis',
    DB::raw("IF( plugins.exploited_by_malware='true' , 'exploitable par malware', IF( plugins.exploit_available = 'true', 'exploit disponible', NULL)) AS exploitability"),
    DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) as ports'),
    'plugins.age_of_vuln')
->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
->whereIn('vuln.upload_id', function($query) use ($id) {
    $query->select('ID')
          ->from('uploadanomalies')
          ->where('ID_Projet', '=', $id);
 })
->where('sow.Type', '=', 'Serveur')
->whereColumn('sow.IP_Host', 'vuln.Host')
->where('sow.Projet', '=', $id)
->whereIn('vuln.Port', function($query) {
    $query->select('Ports_List')
          ->from('PortsMapping')
          ->where('Utilisation', '=', 'Voip');
})
->whereIn('Risk', ['Critical', 'High', 'Medium', 'Low'])
->groupBy('vuln.Host', 'vuln.Name')
->orderBy('vuln.Host')
->orderByDesc('exploitability')
->orderByDesc('Risk')
->get();
$data21_rs = DB::table('vuln')
->select('vuln.Host', 'vuln.Name','Risk','plugins.synopsis',
    DB::raw("IF( plugins.exploited_by_malware='true' , 'exploitable par malware', IF( plugins.exploit_available = 'true', 'exploit disponible', NULL)) AS exploitability"),
    DB::raw('GROUP_CONCAT(DISTINCT vuln.Port) as ports'),
    'plugins.age_of_vuln')
->leftJoin('plugins', 'vuln.Plugin ID', '=', 'plugins.id')
->rightJoin('sow', 'vuln.Host', '=', 'sow.IP_Host')
->whereIn('vuln.upload_id', function($query) use ($id) {
    $query->select('ID')
          ->from('uploadanomalies')
          ->where('ID_Projet', '=', $id);
 })
->where('sow.Type', '=', 'R_S')
->whereColumn('sow.IP_Host', 'vuln.Host')
->where('sow.Projet', '=', $id)
->whereIn('Risk', ['Critical', 'High', 'Medium', 'Low'])
->groupBy('vuln.Host', 'vuln.Name')
->orderBy('vuln.Host')
->orderByDesc('exploitability')
->orderByDesc('Risk')
->get();
*/
?>
