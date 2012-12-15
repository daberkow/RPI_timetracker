RPI_timetracker
===============

Time Tracking 

Random Code:
SELECT CASE WHEN EXISTS (SELECT `status` FROM `timedata` WHERE `startTime`='11-11-11 11:11:11' AND `endTime`='11-11-11 11:11:12')
THEN (SELECT CASE WHEN `status` = 1 THEN "DAN" ELSE "MORGY" END AS RESULT
FROM `timedata` WHERE `startTime`='11-11-11 11:11:11' AND `endTime`='11-11-11 11:11:12' AND `user`=1) 
ELSE (INSERT INTO `timedata`(`user`, `startTime`, `endTime`, `status`) VALUES(1, '11-11-11 11:11:11', '11-11-11 11:11:12', 1)
END AS test



CASE WHEN EXISTS (SELECT `status` FROM `timedata` WHERE `startTime`='11-11-11 11:11:11' AND `endTime`='11-11-11 11:11:12' AND `user`=1)
THEN (SELECT CASE WHEN `status` = 1 THEN 'DAN' ELSE 'MORGY' END AS RESULT
FROM `timedata` WHERE `startTime`='11-11-11 11:11:11' AND `endTime`='11-11-11 11:11:12' AND `user`=1) 
ELSE (INSERT INTO `timedata`(`user`, `startTime`, `endTime`, `status`) VALUE(1, '11-11-11 11:11:11', '11-11-11 11:11:12', 1))

IF (SELECT count(*) FROM `timedata` WHERE `startTime`='11-11-11 11:11:11' AND `endTime`='11-11-11 11:11:12' AND `user`=1) > 0

THEN
SELECT CASE WHEN `status` = 1 THEN 'DAN' ELSE 'MORGY' END AS RESULT
FROM `timedata` WHERE `startTime`='11-11-11 11:11:11' AND `endTime`='11-11-11 11:11:12' AND `user`=1

ELSE
INSERT INTO `timedata`(`user`, `startTime`, `endTime`, `status`) VALUE(1, '11-11-11 11:11:11', '11-11-11 11:11:12', 1)

END IF