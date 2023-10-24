<?php

$DefaultQuery = array (
    0=>
    <<<HERE0
    SELECT Hostip Hosts_IP,
    Nom as Hosts_Name,
    field4 as Hosts_OS,
    COUNT(IF(Exp_Malware>0,1,NULL)) as Hosts_MLW,
    COUNT(IF(Critical_Ex>0,1,NULL)) as Hosts_ExC,
    COUNT(IF(High_Ex>0,1,NULL)) as Hosts_ExH,
    COUNT(IF(Medium_Ex>0,1,NULL)) as Hosts_ExM,
    COUNT(IF(Low_Ex>0,1,NULL)) as Hosts_ExL,
    COUNT(IF(Critical>0,1,NULL)) as Hosts_CR,
    COUNT(IF(High>0,1,NULL)) as Hosts_HI,
    COUNT(IF(Mediu>0,1,NULL)) as Hosts_MD,
    COUNT(IF(Low>0,1,NULL)) as Hosts_LW,
    max(FAILED2) as Hosts_NC, max(PASSED2) as Hosts_CF

    FROM (
    SELECT
    vuln.`Host` as Hostip,
        sow.Nom as Nom,
        sow.field4,
        COUNT(IF( `exploited_by_malware` = 'true' , 1, NULL)) AS Exp_Malware,
        COUNT(IF(vuln.`Risk` = 'Critical' AND ( `exploit_available` = 'true' ), 1, NULL)) AS Critical_Ex,
        COUNT(IF(vuln.`Risk` = 'High' AND ( `exploit_available` = 'true' ), 1, NULL)) AS High_Ex,
        COUNT(IF(vuln.`Risk` = 'Medium' AND ( `exploit_available` = 'true' ), 1, NULL)) AS Medium_Ex,
        COUNT(IF(vuln.`Risk` = 'Low' AND ( `exploit_available` = 'true' ), 1, NULL)) AS Low_Ex,
        COUNT(IF(vuln.`Risk` = 'Critical', 1, NULL)) AS Critical,
        COUNT(IF(vuln.`Risk` = 'High', 1, NULL)) AS High,
        COUNT(IF(vuln.`Risk` = 'Medium', 1, NULL)) AS Mediu,
        COUNT(IF(vuln.`Risk` = 'Low', 1, NULL)) AS Low,
        COUNT(IF(vuln.`Risk` = 'FAILED', 1, NULL)) AS FAILED2,
        COUNT(IF(vuln.`Risk` = 'PASSED', 1, NULL)) AS PASSED2
    FROM vuln
    LEFT JOIN `plugins` ON vuln.`Plugin ID` = plugins.id
    RIGHT JOIN sow ON vuln.`Host` = sow.IP_Host
        WHERE vuln.upload_id in (SELECT `ID`from uploadanomalies WHERE `ID_Projet`=?) and sow.Type="CLAUSENUMBER1"   and sow.IP_Host = vuln.Host and sow.Projet=?
        CLAUSENUMBER2
    GROUP BY
    `Host` ,  vuln.Name
    ) t
    CLAUSENUMBER3
    GROUP BY hostip
    ORDER BY  Critical_Ex DESC, High_Ex DESC, Exp_Malware DESC,Medium_Ex DESC,Critical  DESC,High  DESC
    HERE0,
    1 => <<<HERE1
    SELECT  Risk AS VulnSummary_Risk,
    plugins.Synopsis AS VulnSummary_Synopsis_ToBeClean,
    plugins.id AS VulnSummary_Plugin,
    plugins.name AS VulnSummary_Name_ToBeClean,
    count(DISTINCT Risk,plugins.Synopsis,vuln.Host) AS VulnSummary_Count,
    IF( plugins.exploited_by_malware='true' , 'exploitable par malware', IF( plugins.exploit_available = 'true', "exploit disponible", NULL)) AS VulnSummary_Exploitability,
    REPLACE (GROUP_CONCAT(DISTINCT HOST LIMIT 3), ",", "\n") AS VulnSummary_Hosts
    FROM vuln
    LEFT JOIN `plugins` ON vuln.`Plugin ID` = plugins.id
    RIGHT JOIN sow ON vuln.`Host` = sow.IP_Host
    WHERE vuln.upload_id in (SELECT `ID`from uploadanomalies WHERE `ID_Projet`=?) and sow.Type="CLAUSENUMBER1" and sow.Projet=?
    CLAUSENUMBER2
    AND `Risk` in ('Critical', 'High', 'Medium', 'Low')
    group by `Risk`,vuln.`Synopsis`
    ORDER BY  exploited_by_malware DESC, exploit_available DESC,`Risk` DESC  CLAUSENUMBER99
    HERE1,
    2 =><<<HERE2
    SELECT
    vuln.Host AS VulnPerHost_host_ip,
    vuln.Host AS VulnPerHost_host,
    Risk AS VulnPerHost_Risk,
    plugins.synopsis AS VulnPerHost_Synopsis_ToBeClean,
    plugins.id AS VulnPerHost_Plugin,
    plugins.name AS VulnPerHost_Name_ToBeClean,
    IF( plugins.exploited_by_malware='true' , 'exploitable par malware', IF( plugins.exploit_available = 'true', "exploit disponible", NULL)) AS VulnPerHost_exploi,
     GROUP_CONCAT(DISTINCT vuln.Port) as VulnPerHost_port,
    plugins.age_of_vuln AS VulnPerHost_age
    FROM vuln
    LEFT JOIN plugins ON vuln.`Plugin ID` = plugins.id
    RIGHT JOIN sow ON vuln.Host = sow.IP_Host
        WHERE vuln.upload_id in (SELECT `ID`from uploadanomalies WHERE ID_Projet=?) and sow.Type='CLAUSENUMBER1'  and sow.Projet=?
        CLAUSENUMBER2 AND Risk in ('Critical', 'High', 'Medium', 'Low')
    GROUP BY Host, vuln.Name
    ORDER BY  VulnPerHost_host,VulnPerHost_exploi DESC,VulnPerHost_Risk DESC
    HERE2,
    3 =>  <<<HERE3
    SELECT
    ROW_NUMBER() OVER() AS VulnDetails_ID,
    vuln.Risk AS VulnDetails_RISK,
    plugins.name AS VulnDetails_Name_ToBeClean,
    plugins.cvss3_base_score  AS VulnDetails_CVSS,
    GROUP_CONCAT(DISTINCT vuln.Host) AS VulnDetails_Hosts,
    GROUP_CONCAT(DISTINCT vuln.Port) AS VulnDetails_Hosts_ports,
    plugins.description AS VulnDetails_Desc_ToBeClean,
    vuln.`Plugin ID` AS VulnDetails_pluginID,
    plugins.synopsis AS VulnDetails_Synopsis_ToBeClean,
    plugins.solution AS VulnDetails_Recomendations_ToBeClean,
    plugins.see_also AS VulnDetails_ref_ToBeClean,
    `vuln`.`Plugin Output` AS VulnDetails_PluginOutput_ToBeClean,
    plugins.exploit_available AS VulnDetails_available,
    plugins.exploit_framework_metasploit AS VulnDetails_Metasploit,
    plugins.exploit_framework_canvas AS VulnDetails_CANVAS,
    plugins.exploit_framework_core AS VulnDetails_Core_Impact,
    plugins.age_of_vuln AS VulnDetails_Age,
    plugins.exploited_by_malware AS VulnDetails_malware
    FROM vuln
    LEFT JOIN `plugins` ON vuln.`Plugin ID` = plugins.id
    RIGHT JOIN sow ON vuln.`Host` = sow.IP_Host
    WHERE vuln.upload_id in (SELECT `ID`from uploadanomalies WHERE `ID_Projet`=?) and sow.Type="CLAUSENUMBER1"
    and sow.IP_Host = vuln.Host and sow.Projet=?
    CLAUSENUMBER2
    AND `Risk` in ('Critical', 'High', 'Medium')
    group by `Risk`,plugins.`synopsis`
    ORDER BY  exploited_by_malware DESC, exploit_available DESC,`Risk` DESC
    HERE3,
    4 =>  <<<HERE4
    SELECT
    ROW_NUMBER() OVER() AS VulnDetails_ID,
    vuln.Risk AS VulnDetails_RISK,
    if(SUBSTRING(`Name`, 1, 28)="Vérifications de conformité", Substring(`Description`, 1, LEAST (300,LOCATE(":",`Description`))), `Name`) AS VulnDetails_Name_ToBeClean,
    `CVSS v3.0 Base Score`  AS VulnDetails_CVSS,
    GROUP_CONCAT(DISTINCT vuln.Host) AS VulnDetails_Hosts,
    GROUP_CONCAT(DISTINCT vuln.Port) AS VulnDetails_Hosts_ports,
    vuln.description AS VulnDetails_Desc_ToBeClean,
    vuln.`Plugin ID` AS VulnDetails_pluginID,
    vuln.synopsis AS VulnDetails_Synopsis_ToBeClean,
    vuln.solution AS VulnDetails_Recomendations_ToBeClean,
    vuln.`See Also` AS VulnDetails_ref_ToBeClean
    FROM vuln
    RIGHT JOIN sow ON vuln.`Host` = sow.IP_Host
    WHERE vuln.upload_id in (SELECT `ID`from uploadanomalies WHERE `ID_Projet`=?) and sow.Type="CLAUSENUMBER1"
    and sow.IP_Host = vuln.Host and sow.Projet=?
    CLAUSENUMBER2
    AND `Risk` in ('FAILED', 'PASSED')
    group by `Risk`,vuln.description
    ORDER BY  `Risk` ASC  CLAUSENUMBER99
    HERE4
);


$RowOfColoring = array(0=>null, 1=>"VulnSummary_Risk", 2=>"VulnPerHost_Risk", 3=>null, 4=>null);
$keyToDuplicateRows = array(0=>"Hosts_Name", 1=>"VulnSummary_Risk", 2=>"VulnPerHost_host", 3=>"VulnDetails_ID", 4=>"VulnDetails_ID");
$prefixTLT = array(0=>"TLT_", 1=>null, 2=>null, 3=>null, 4=>null);
$arrayRisks = array("Critical", "High", "Medium", "Low");
$ColoredRowsArrays= array (
    0=>null,
    1=> $arrayRisks,
    2=> $arrayRisks,
    3=>null, 4=>null);
$SqlQueriesMarks = array(
    "0" =>array(0=>"CLAUSENUMBER1", 1=>"CLAUSENUMBER2", 2=>"CLAUSENUMBER3"),
    "1" => array(0=>"Serveur", 1=>"AND vuln.Port NOT IN (SELECT `Ports_List` FROM PortsMapping)", 2=>""),
    "2" => array(0=>"R_S", 1=>"", 2=>"WHERE (Critical, High, Mediu, Low, FAILED2, PASSED2) <>(0,0,0,0,0,0)"),
    "3" => array(0=>"Serveur", 1=>" AND 	vuln.Port IN (SELECT `Ports_List` FROM PortsMapping WHERE Utilisation='DB')", 2=>"WHERE (Critical, High, Mediu, Low, FAILED2, PASSED2) <>(0,0,0,0,0,0)"),
    "4" => array(0=>"PC", 1=>"", 2=>""),
    "5" => array(0=>"Ext", 1 =>"", 2=>"WHERE (Critical, High, Mediu, Low, FAILED2, PASSED2) <>(0,0,0,0,0,0)"),
    "6" => array(0=>"Apps", 1=>"AND 	vuln.Port IN (SELECT `Ports_List` FROM PortsMapping WHERE Utilisation='Apps')", 2=>""),
    "7" => array(0=>"Serveur", 1=>" AND 	vuln.Port IN (SELECT `Ports_List` FROM PortsMapping WHERE Utilisation='Mail')", 2=>"WHERE (Critical, High, Mediu, Low, FAILED2, PASSED2) <>(0,0,0,0,0,0)"),
    "8" => array(0=>"Serveur", 1=>" AND 	vuln.Port IN (SELECT `Ports_List` FROM PortsMapping WHERE Utilisation='Voip')", 2=>"WHERE (Critical, High, Mediu, Low, FAILED2, PASSED2) <>(0,0,0,0,0,0)"),


);

?>
