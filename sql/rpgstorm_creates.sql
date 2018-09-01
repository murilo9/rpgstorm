USE dbrpgstorm;

CREATE TABLE tbUsuarios(
	stEmail VARCHAR(60) NOT NULL,
    stNickname VARCHAR(40) NOT NULL UNIQUE,
    stSenha VARCHAR(20) NOT NULL,
    PRIMARY KEY (stEmail)
) ENGINE = innodb;

CREATE TABLE tbMundos(
	stId VARCHAR(5) NOT NULL,
    stNome VARCHAR(80) NOT NULL UNIQUE,
    stCreator VARCHAR(60) NOT NULL,
    PRIMARY KEY (stId),
    FOREIGN KEY (stCreator) REFERENCES tbUsuarios(stEmail)
) ENGINE = innodb;

CREATE TABLE tbPersonagens (
	stId VARCHAR(6) NOT NULL,
    stMundo VARCHAR(5) NOT NULL,
    stDono VARCHAR(60) NOT NULL,
    stNome VARCHAR(80) NOT NULL,
    stFichaPath VARCHAR (80) NOT NULL UNIQUE,
    PRIMARY KEY (stId),
    FOREIGN KEY (stMundo) REFERENCES tbMundos(stId),
    FOREIGN KEY (stDono) REFERENCES tbUsuarios(stEmail)
) ENGINE = innodb;

CREATE TABLE tbCenas (
	stId VARCHAR(5) NOT NULL,
    stMundo VARCHAR(5) NOT NULL,
    stCreator VARCHAR(6) NOT NULL,
    blEstado BOOLEAN DEFAULT TRUE,
    PRIMARY KEY (stId, stMundo),
    FOREIGN KEY (stMundo) REFERENCES tbMundos(stId),
    FOREIGN KEY (stCreator) REFERENCES tbPersonagens(stId)
) ENGINE = innodb;

CREATE TABLE tbAcoes (
	stId VARCHAR(6) NOT NULL,
    stCena VARCHAR(5) NOT NULL,
    stMundo VARCHAR(5) NOT NULL,
    stPersonagem VARCHAR(6) NOT NULL,
    PRIMARY KEY (stId, stCena, stMundo),
    FOREIGN KEY (stCena) REFERENCES tbCenas(stId),
    FOREIGN KEY (stMundo) REFERENCES tbMundos(stId)
) ENGINE = innodb;

CREATE TABLE tbStaffs(
	stUsuario VARCHAR(60),
    stMundo VARCHAR(5),
    PRIMARY KEY (stUsuario, stMundo),
    FOREIGN KEY (stUsuario) REFERENCES tbUsuarios(stEmail),
    FOREIGN KEY (stMundo) REFERENCES tbMundos(stId)
) ENGINE = innodb;

CREATE TABLE tbMundoUsuarios(
	stUsuario VARCHAR(60),
    stMundo VARCHAR(5),
    PRIMARY KEY (stUsuario, stMundo),
    FOREIGN KEY (stUsuario) REFERENCES tbUsuarios(stEmail),
    FOREIGN KEY (stMundo) REFERENCES tbMundos(stId)
) ENGINE = innodb;