<?php $table_fields_cached = json_decode('{"0":"2014-12-29 16:16:25","1":[{"Field":"IDEMPLEADO","Type":"int(11)","Null":"NO","Key":"PRI","Default":null,"Extra":"auto_increment","PDOType":1,"Usable":0},{"Field":"NCOMPLETO","Type":"varchar(101)","Null":"YES","Key":"","Default":null,"Extra":"","PDOType":2,"Value":"","Usable":1},{"Field":"NOMBRE","Type":"varchar(50)","Null":"NO","Key":"","Default":null,"Extra":"","PDOType":2,"Value":"","Usable":1},{"Field":"APELLIDO","Type":"varchar(50)","Null":"NO","Key":"","Default":null,"Extra":"","PDOType":2,"Value":"","Usable":1},{"Field":"TELEFONOS","Type":"varchar(50)","Null":"NO","Key":"","Default":null,"Extra":"","PDOType":2,"Value":"","Usable":1},{"Field":"CORREO","Type":"varchar(200)","Null":"YES","Key":"","Default":null,"Extra":"","PDOType":2,"Value":"","Usable":1},{"Field":"IDSUCURSAL","Type":"int(11)","Null":"YES","Key":"","Default":null,"Extra":"","PDOType":1,"Value":0,"Usable":1},{"Field":"IDDEPARTAMENTO","Type":"int(11)","Null":"YES","Key":"","Default":null,"Extra":"","PDOType":1,"Value":0,"Usable":1},{"Field":"IDEMPLEADO_S","Type":"int(11)","Null":"YES","Key":"","Default":null,"Extra":"","PDOType":1,"Value":0,"Usable":1},{"Field":"NUSUARIO","Type":"varchar(30)","Null":"YES","Key":"","Default":null,"Extra":"","PDOType":2,"Value":"","Usable":1},{"Field":"CUSUARIO","Type":"varchar(32)","Null":"YES","Key":"","Default":null,"Extra":"","PDOType":2,"Value":"","Usable":1},{"Field":"IDUSERGROUP","Type":"int(11) unsigned","Null":"NO","Key":"","Default":"2","Extra":"","PDOType":1,"Value":"2","Usable":1},{"Field":"ACTIVO","Type":"tinyint(1) unsigned","Null":"YES","Key":"","Default":"1","Extra":"","PDOType":1,"Value":"1","Usable":1}],"11":"IDEMPLEADO","2":"SELECT * FROM `empleados`","21":"SELECT COUNT(*) AS RCOUNT FROM `empleados`","3":"INSERT INTO `empleados` (`NCOMPLETO`, `NOMBRE`, `APELLIDO`, `TELEFONOS`, `CORREO`, `IDSUCURSAL`, `IDDEPARTAMENTO`, `IDEMPLEADO_S`, `NUSUARIO`, `CUSUARIO`, `IDUSERGROUP`, `ACTIVO`) VALUES ( :NCOMPLETO,  :NOMBRE,  :APELLIDO,  :TELEFONOS,  :CORREO,  :IDSUCURSAL,  :IDDEPARTAMENTO,  :IDEMPLEADO_S,  :NUSUARIO,  :CUSUARIO,  :IDUSERGROUP,  :ACTIVO)","4":"UPDATE `empleados` SET `NCOMPLETO`= :NCOMPLETO, `NOMBRE`= :NOMBRE, `APELLIDO`= :APELLIDO, `TELEFONOS`= :TELEFONOS, `CORREO`= :CORREO, `IDSUCURSAL`= :IDSUCURSAL, `IDDEPARTAMENTO`= :IDDEPARTAMENTO, `IDEMPLEADO_S`= :IDEMPLEADO_S, `NUSUARIO`= :NUSUARIO, `CUSUARIO`= :CUSUARIO, `IDUSERGROUP`= :IDUSERGROUP, `ACTIVO`= :ACTIVO","5":"DELETE FROM `empleados`","6":" WHERE (((`IDEMPLEADO`)= :IDEMPLEADO))","7":1}',true); ?>