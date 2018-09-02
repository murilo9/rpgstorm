DELIMITER $$
CREATE FUNCTION validaLogin (inputEmail VARCHAR(60), inputSenha VARCHAR(20)) 
RETURNS BOOLEAN
BEGIN
	RETURN (SELECT COUNT(stEmail) FROM tbUsuarios WHERE stEmail=inputEmail && inputSenha=inputSenha) != 0;
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE getWorldStaffs (inputWorld VARCHAR(5))
BEGIN
	(SELECT U.stNickname, S.stMundo FROM tbStaffs S INNER JOIN tbUsuarios U
    ON S.stUsuario = U.stEmail
    WHERE S.stMundo = inputWorld);
END$$
DELIMITER ;
