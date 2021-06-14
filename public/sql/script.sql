CREATE TABLE Account(
   idAccount INT,
   email VARCHAR(50),
   password VARCHAR(500),
   role VARCHAR(500),
   firstname VARCHAR(50),
   lastname VARCHAR(50),
   birthDate DATE,
   idMongo VARCHAR(500),
   subscribeDate DATETIME,
   isActivated LOGICAL,
   skills JSON,
   job JSON,
   PRIMARY KEY(idAccount)
);

CREATE TABLE Project(
   idProject INT,
   repo VARCHAR(250),
   name VARCHAR(50),
   description VARCHAR(500),
   dateCreation DATE,
   idMongo VARCHAR(50),
   status VARCHAR(50),
   skillsNeeded JSON,
   jobNeeded JSON,
   idAccount INT NOT NULL,
   PRIMARY KEY(idProject),
   FOREIGN KEY(idAccount) REFERENCES Account(idAccount) ON DELETE CASCADE
);

CREATE TABLE Skill(
   name VARCHAR(50),
   PRIMARY KEY(name)
);

CREATE TABLE Commentary(
   idProject INT,
   idAccount INT,
   dateComment DATETIME,
   comment VARCHAR(140),
   PRIMARY KEY(idProject, idAccount, dateComment),
   UNIQUE(idProject),
   UNIQUE(idAccount),
   FOREIGN KEY(idProject) REFERENCES Project(idProject) ON DELETE CASCADE,
   FOREIGN KEY(idAccount) REFERENCES Account(idAccount) ON DELETE CASCADE
);

CREATE TABLE Apply(
   idAccount INT,
   idProject INT,
   isAdmitted LOGICAL,
   role VARCHAR(50),
   PRIMARY KEY(idAccount, idProject),
   FOREIGN KEY(idAccount) REFERENCES Account(idAccount) ON DELETE CASCADE,
   FOREIGN KEY(idProject) REFERENCES Project(idProject) ON DELETE CASCADE
);

CREATE TABLE isFor(
   idAccount INT,
   idProject INT,
   evaluation LOGICAL,
   PRIMARY KEY(idAccount, idProject),
   FOREIGN KEY(idAccount) REFERENCES Account(idAccount) ON DELETE CASCADE,
   FOREIGN KEY(idProject) REFERENCES Project(idProject) ON DELETE CASCADE
);

CREATE TABLE ReportProject(
   idAccount INT,
   idProject INT,
   dateReport DATETIME,
   reason VARCHAR(50),
   description VARCHAR(140),
   PRIMARY KEY(idAccount, idProject, dateReport),
   FOREIGN KEY(idAccount) REFERENCES Account(idAccount) ON DELETE CASCADE,
   FOREIGN KEY(idProject) REFERENCES Project(idProject) ON DELETE CASCADE
);

CREATE TABLE ReportUser(
   idReporter INT,
   idReported INT,
   dateReport DATETIME,
   reason VARCHAR(50),
   description VARCHAR(140),
   PRIMARY KEY(idReporter, idReported, dateReport),
   FOREIGN KEY(idReporter) REFERENCES Account(idAccount) ON DELETE CASCADE,
   FOREIGN KEY(idReported) REFERENCES Account(idAccount) ON DELETE CASCADE
);

CREATE TABLE ReportComment(
   idReporter INT,
   idProject INT,
   idReported INT,
   dateComment DATETIME,
   reason VARCHAR(50),
   description VARCHAR(140),
   dateReport DATETIME,
   PRIMARY KEY(idReporter, idProject, idReported, dateComment),
   FOREIGN KEY(idReporter) REFERENCES Account(idAccount),
   FOREIGN KEY(idProject, idReported, dateComment) REFERENCES Commentary(idProject, idAccount, dateComment)
);
