USE dbrpgstorm;
SELECT * FROM tbPersonagens;

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
    blPublic BOOLEAN DEFAULT true,
    stCapa VARCHAR(80) DEFAULT 'none',
    PRIMARY KEY (stId),
    FOREIGN KEY (stCreator) REFERENCES tbUsuarios(stEmail)
) ENGINE = innodb;

CREATE TABLE tbPersonagens (
	stId VARCHAR(6) NOT NULL,
    stMundo VARCHAR(5) NOT NULL,
    stDono VARCHAR(60) NOT NULL,
    stNome VARCHAR(80) NOT NULL,
    stFoto VARCHAR(80),
    PRIMARY KEY (stId),
    FOREIGN KEY (stMundo) REFERENCES tbMundos(stId),
    FOREIGN KEY (stDono) REFERENCES tbUsuarios(stEmail)
) ENGINE = innodb;

CREATE TABLE tbCenas (
	stId VARCHAR(5) NOT NULL,
    stMundo VARCHAR(5) NOT NULL,
    stCreator VARCHAR(6) NOT NULL,
    blEstado BOOLEAN DEFAULT TRUE,
    stNome VARCHAR(80),
    dtData DATETIME DEFAULT NOW(),
    stImagem VARCHAR(80) DEFAULT 'none',
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
    blStatus BOOLEAN NOT NULL,
    stAprovedBy VARCHAR(60),
    PRIMARY KEY (stUsuario, stMundo),
    FOREIGN KEY (stUsuario) REFERENCES tbUsuarios(stEmail),
    FOREIGN KEY (stMundo) REFERENCES tbMundos(stId),
    FOREIGN KEY (stAprovedBy) REFERENCES tbStaffs(stUsuario)
) ENGINE = innodb;

CREATE TABLE tbNotifs(
	stId VARCHAR(8) AUTO_INCREMENT,
	stUsuario VARCHAR(60),
    stTipo VARCHAR(2) NOT NULL,
    stLink VARCHAR(100),
    stConteudo VARCHAR(255),
    dtData DATETIME DEFAULT NOW(),
    etc1 VARCHAR(80),
    etc2 VARCHAR(80),
    etc3 VARCHAR(80),
    PRIMARY KEY (stId),
    FOREIGN KEY (stUsuario) REFERENCES tbUsuarios(stEmail)
) ENGINE = innodb;

SET SQL_SAFE_UPDATES=1;