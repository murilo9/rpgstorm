DELIMITER $$
CREATE FUNCTION validaLogin (inputEmail VARCHAR(60), inputSenha VARCHAR(20)) 
RETURNS BOOLEAN
BEGIN
	RETURN (SELECT COUNT(stEmail) FROM tbUsuarios WHERE stEmail=inputEmail && inputSenha=inputSenha) != 0;
END$$
DELIMITER ;
 
