DROP DATABASE IF EXISTS team_up;

CREATE DATABASE team_up;

USE team_up;

CREATE TABLE attivita (
  id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  titolo varchar(100) NOT NULL,
  descrizione varchar(500) NOT NULL,
  data_creazione date NOT NULL,
  posizione varchar(50) DEFAULT NULL,
  posti_rimanenti int(11) NOT NULL,
  completata tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE categorie (
  id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  nome varchar(25) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE utenti (
  id int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  email varchar(256) NOT NULL UNIQUE,
  username varchar(16) NOT NULL UNIQUE,
  password varchar(16) NOT NULL,
  posizione varchar(50) DEFAULT NULL,
  token varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE categorie_attivita (
  attivita int(11) NOT NULL,
  categoria int(11) NOT NULL,
  
  FOREIGN KEY (attivita) REFERENCES attivita(id)
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  
  FOREIGN KEY (categoria) REFERENCES categorie(id)
  ON DELETE CASCADE
  ON UPDATE CASCADE
  
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE interessi_utente (
  utente int(11) NOT NULL,
  categoria int(11) NOT NULL,
  
  FOREIGN KEY (utente) REFERENCES utenti(id)
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  
  FOREIGN KEY (categoria) REFERENCES categorie(id)
  ON DELETE CASCADE
  ON UPDATE CASCADE
  
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE partecipanti (
  utente int(11) NOT NULL,
  attivita int(11) NOT NULL,
  is_leader tinyint(1) NOT NULL DEFAULT 0,
  
  FOREIGN KEY (utente) REFERENCES utenti(id)
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  
  FOREIGN KEY (attivita) REFERENCES attivita(id)
  ON DELETE CASCADE
  ON UPDATE CASCADE
  
  
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

CREATE TABLE richieste_pendenti_attivita (
  attivita int(11) NOT NULL,
  utente int(11) NOT NULL,
  data_richiesta date DEFAULT NULL,
  descrizione varchar(500) DEFAULT NULL,
  
  FOREIGN KEY (attivita) REFERENCES attivita(id)
  ON DELETE CASCADE
  ON UPDATE CASCADE,
  
  FOREIGN KEY (utente) REFERENCES utenti(id)
  ON DELETE CASCADE
  ON UPDATE CASCADE
  
  
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


INSERT INTO categorie (nome) VALUES ("Natura"),
									("Tecnologia"),
									("Famiglia"),
									("Salute"),
									("Sport"),
									("Istruzione"),
									("Hobby"),
									("Cucina"),
									("Cultura"),
									("Musica"),
									("Politica"),
									("Business"),
									("Film e SerieTV"),
									("Giochi"),
									("Arte"),
									("Libri"),
									("Viaggi"),
									("Animali"),
									("Moda"),
									("Social"),
									("LGBTQ+");