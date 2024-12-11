CREATE DATABASE powerbank;
USE powerbank;

CREATE TABLE client (
	client_id INT(5) NOT NULL AUTO_INCREMENT,
    client_name VARCHAR(50) NOT NULL,
    address VARCHAR(50) NOT NULL,
    phone_number CHAR(11) NOT NULL,
    email VARCHAR(50) NOT NULL,
    date_of_birth DATE NOT NULL,
    PRIMARY KEY (client_id)
);

CREATE TABLE account (
	acct_id INT(5) NOT NULL AUTO_INCREMENT,
    client_id INT(5) NOT NULL,
    acct_type CHAR(8) NOT NULL,
    acct_balance DECIMAL(9,2) NOT NULL,
    PRIMARY KEY (acct_id),
    FOREIGN KEY (client_id) REFERENCES client(client_id)
);

CREATE TABLE savings_account (
	acct_id INT(5) NOT NULL,
    savings_interest_rate DECIMAL(4,3) NOT NULL,
    PRIMARY KEY(acct_id),
    FOREIGN KEY (acct_id) REFERENCES account(acct_id)
);

CREATE TABLE checking_account (
	acct_id INT(5) NOT NULL,
    overdraft_limit DECIMAL(9,2) NOT NULL,
    PRIMARY KEY(acct_id),
    FOREIGN KEY (acct_id) REFERENCES account(acct_id)
);

CREATE TABLE employee (
	employee_id INT(5) NOT NULL AUTO_INCREMENT,
    employee_name VARCHAR(50) NOT NULL,
    employee_position VARCHAR(50) NOT NULL, 
    salary DECIMAL(9,2) NOT NULL,
    PRIMARY KEY (employee_id)
);

CREATE TABLE loan (
	loan_id INT(5) NOT NULL AUTO_INCREMENT,
    client_id INT(5) NOT NULL,
    loan_type VARCHAR(50) NOT NULL,
    loan_amount DECIMAL(9,2) NOT NULL,
    loan_interest_rate DECIMAL(4,3) NOT NULL,
    loan_start_date DATE NOT NULL,
	loan_end_date DATE NOT NULL,
    employee_id INT(5) NOT NULL,
    PRIMARY KEY (loan_id),
    FOREIGN KEY (client_id) REFERENCES client(client_id),
    FOREIGN KEY (employee_id) REFERENCES employee(employee_id)
);

INSERT INTO client VALUES
    (1, "Jacob Lash", "43rd. St.", "09358681544", "jacoblash@email.com", "1988-03-11"),
    (2, "Dean Abrams", "44th. St.", "09541245567", "abrdean@email.com", "1968-11-03"),
    (3, "Elizabeth McGinnis", "51st. St.", "09614567898", "lizmcginnis@email.com", "2001-07-14"),
    (4, "Mark Bebop", "52nd. St.", "09789651211", "markbebop@email.com", "1996-07-04");

INSERT INTO account VALUES
    (1, 1, "SAVINGS", 481536.75),
    (2, 1, "CHECKING", 12000.00),
    (3, 2, "SAVINGS", 515978.25),
    (4, 3, "CHECKING", 128000.50),
    (5, 4, "SAVINGS", 80050.75),
    (6, 4, "CHECKING", 150000.00);

INSERT INTO savings_account VALUES
    (1, 0.05),
    (3, 0.025),
    (5, 0.015);

INSERT INTO checking_account VALUES
    (2, 20000.00),
    (4, 10000.00),
    (6, 100000.00);

INSERT INTO employee VALUES
    (1, "Isagi Yoichi", "Junior Loan Officer", 215000.00),
    (2, "Seishiro Nagi", "Senior Loan Officer", 350000.00),
    (3, "Rin Itoshi", "Senior Loan Officer", 350000.00),
    (4, "Ego Jinpachi", "Bank Manager", 500000.00),
    (5, "Kylian Mbappe", "Head of Bank Operations", 1250000.00),
    (6, "Julian Loki", "Head of Marketing", 1000000.00),
    (7, "John Paurbanc", "Chief Executive Officer", 4000000.00);

INSERT INTO loan VALUES
    (1, 1, "BUSINESS", 1000000.00, 0.05, "2004-12-14", "2011-12-14", 1),
    (2, 1, "CAR", 800000.00, 0.20, "2005-11-05", "2010-05-11", 2),
    (3, 2, "BUSINESS", 1500000.00, 0.025, "2008-07-21", "2018-07-21", 3),
    (4, 2, "HOUSING", 2000000.00, 0.07, "2008-04-20", "2028-04-20", 4),
    (5, 3, "STUDENT", 300000.00, 0.05, "2011-03-05", "2015-03-05", 5),
    (6, 4, "CAR", 600000.00, 0.18, "2003-09-30", "2008-09-30", 6);


CREATE TABLE credentials (
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    client_id INT(5), -- Nullable if it's a client
    employee_id INT(5), -- Nullable if it's an employee
    role ENUM("client", "admin") NOT NULL, -- To differentiate between client and admin
    PRIMARY KEY (username),
    FOREIGN KEY (client_id) REFERENCES client(client_id) ON DELETE CASCADE,
    FOREIGN KEY (employee_id) REFERENCES employee(employee_id) ON DELETE CASCADE
);

INSERT INTO credentials (username, password, client_id, employee_id, role) VALUES
    ("jacoblash@email.com", "password1", 1, NULL, "client"),
    ("abrdean@email.com", "password2", 2, NULL, "client"), 
    ("lizmcginnis@email.com", "password3", 3, NULL, "client"), 
    ("markbebop@email.com", "password4", 4, NULL, "client"); 

INSERT INTO credentials (username, password, client_id, employee_id, role) VALUES
    ("admin", "admin123", NULL, 5, "admin"); 

