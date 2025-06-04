CREATE DATABASE scpDatabase;
-- USE scpDatabase;

CREATE TABLE personale (

  user_ID INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(255) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  email VARCHAR(255) UNIQUE NOT NULL,
  name VARCHAR(255) NOT NULL,
  surname VARCHAR(255) NOT NULL,
  clearance_Level ENUM('0', '1', '2', '3', '4', '5', 'O5') NOT NULL,
  birth_Date DATE NOT NULL,
  enroll_Date DATE NOT NULL,
  status ENUM('active', 'inactive', 'dead'),
  gender ENUM('other', 'male', 'female') NOT NULL,
  nationality VARCHAR(150),
  department ENUM('scientific', 'class-d', 'mobile task force', 'security', 'internal security', 'rapid response team', 'civil', 'administration') NOT NULL
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
  CONSTRAINT test_SCP FOREIGN KEY (scp) REFERENCES SCP(scp_Number)
  ON UPDATE CASCADE
  ON DELETE RESTRICT,
  class_D INT NOT NULL,
  CONSTRAINT test_ClassD FOREIGN KEY (class_D) REFERENCES personale(user_ID)
  ON UPDATE CASCADE
  ON DELETE RESTRICT,
  scientist INT NOT NULL,
  CONSTRAINT test_Scientist FOREIGN KEY (scientist) REFERENCES personale(user_ID)
  ON UPDATE CASCADE
  ON DELETE RESTRICT,
  description TEXT,
  test_result TEXT,
  test_Date DATE NOT NULL,
  start_Time TIME,
  end_Time TIME
  -- maybe more...

);

CREATE TABLE report (

  report_ID INT AUTO_INCREMENT PRIMARY KEY,
  scp_Breached INT,
  CONSTRAINT report_SCP FOREIGN KEY (scp_Breached) REFERENCES SCP(scp_Number)
  ON UPDATE CASCADE
  ON DELETE SET NULL, -- se lo SCP viene cancellato, il report non viene cancellato ma il campo scp_Breached diventa NULL
  reporter INT NOT NULL,
  CONSTRAINT report_personale FOREIGN KEY (reporter) REFERENCES personale(user_ID)
  ON UPDATE CASCADE
  ON DELETE RESTRICT,
  incident_Description TEXT,
  incident_Date DATE,
  start_Time TIME,
  end_Time TIME

);

-- Root/O5 level (full access)
CREATE USER 'scp_admin'@'localhost' IDENTIFIED BY 'AdminO5';
GRANT ALL PRIVILEGES ON scpdatabase.* TO 'scp_admin'@'localhost';

-- High clearance (4-5)
CREATE USER 'scp_high'@'localhost' IDENTIFIED BY 'User45';
GRANT SELECT, INSERT, UPDATE ON scpdatabase.* TO 'scp_high'@'localhost';
-- No need to revoke something you've not given
-- REVOKE DROP, CREATE, ALTER, GRANT OPTION ON scp_database.* FROM 'scp_high'@'localhost';

-- Medium clearance (2-3)
CREATE USER 'scp_medium'@'localhost' IDENTIFIED BY 'User23';
GRANT SELECT ON scpdatabase.* TO 'scp_medium'@'localhost';
GRANT INSERT, UPDATE ON scpdatabase.test TO 'scp_medium'@'localhost';
GRANT INSERT ON scpdatabase.report TO 'scp_medium'@'localhost';

-- Low clearance (0-1)
CREATE USER 'scp_low'@'localhost' IDENTIFIED BY 'User01';
GRANT SELECT ON scpdatabase.personale TO 'scp_low'@'localhost';
-- Create a filtered view (run as root or admin)
CREATE VIEW scpdatabase.safe_euclid_scp AS
SELECT * FROM scpdatabase.scp
WHERE danger_Class IN ('safe', 'euclid');
-- Then grant access to that view
GRANT SELECT ON scpdatabase.safe_euclid_scp TO 'scp_low'@'localhost';

ALTER TABLE personale
ADD COLUMN public BOOL NOT NULL DEFAULT 0;