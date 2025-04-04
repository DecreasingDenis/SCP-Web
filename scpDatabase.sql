CREATE DATABASE scpDatabase;
-- USE scpDatabase;

CREATE TABLE personale (

  user_ID INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(255) NOT NULL,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(255) NOT NULL,
  name VARCHAR(255) NOT NULL,
  surname VARCHAR(255) NOT NULL,
  clearance_Level ENUM('0', '1', '2', '3', '4', '5', 'O5') NOT NULL,
  birth_Date DATE NOT NULL,
  enroll_Date DATE NOT NULL,
  status ENUM('active', 'inactive', 'dead'),
  gender ENUM('other', 'male', 'female') NOT NULL,
  nationality VARCHAR(150),
  departmente ENUM('scientific', 'class-d', 'mobile task force', 'security', 'internal security', 'rapid response team', 'civil', 'administration') NOT NULL
  -- maybe more...
  
);

CREATE TABLE SCP (

  scp_Number INT PRIMARY KEY,
  danger_Class ENUM('safe', 'euclid', 'keter', 'unknown') NOT NULL,
  description TEXT,
  safery_Measures TEXT,
  status ENUM('contained', 'uncontained', 'unknown', 'undisclosed'),
  containment_Date DATE
  -- maybe more...

);

CREATE TABLE test (

  test_ID INT AUTO_INCREMENT PRIMARY KEY,
  scp INT NOT NULL,
  CONSTRAINT test_SCP FOREIGN KEY (scp) REFERENCES SCP(scp_Number),
  class_D INT NOT NULL,
  CONSTRAINT test_ClassD FOREIGN KEY (class_D) REFERENCES personale(user_ID),
  scientist INT NOT NULL,
  CONSTRAINT test_Scientist FOREIGN KEY (scientist) REFERENCES personale(user_ID),
  description TEXT,
  test_result TEXT,
  test_Date DATE NOT NULL,
  start_Time TIME,
  duration TIME, -- potrei toglierlo e fare si che sia solo un calcolo
  end_Time TIME
  -- maybe more...

);

CREATE TABLE report (

  report_ID INT AUTO_INCREMENT PRIMARY KEY,
  scp_Breached INT,
  CONSTRAINT report_SCP FOREIGN KEY (scp_Breached) REFERENCES SCP(scp_Number),
  reporter INT NOT NULL,
  CONSTRAINT report_personale FOREIGN KEY (reporter) REFERENCES personale(user_ID),
  indicent_Description TEXT,
  incident_Date DATE,
  start_Time TIME,
  duration TIME, -- potrei toglierlo e fare si che sia solo un calcolo
  end_Time TIME

);

-- non ancora scelte le regole di cancellazione e aggiornamento 
