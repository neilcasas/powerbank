CREATE DATABASE powerbank;
USE powerbank;

CREATE TABLE client (
	client_id CHAR(5) NOT NULL,
    client_name VARCHAR(50) NOT NULL,
    address VARCHAR(50) NOT NULL,
    phone_number CHAR(11) NOT NULL,
    email VARCHAR(50) NOT NULL,
    date_of_birth DATE NOT NULL,
    PRIMARY KEY (client_id)
);

CREATE TABLE account (
	acct_id CHAR(5) NOT NULL,
    client_id CHAR(5) NOT NULL,
    acct_type CHAR(8) NOT NULL,
    acct_balance DECIMAL(9,2) NOT NULL,
    PRIMARY KEY (acct_id),
    FOREIGN KEY (client_id) REFERENCES client(client_id)
);

CREATE TABLE savings_account (
	acct_id CHAR(5) NOT NULL,
    savings_interest_rate DECIMAL(4,3) NOT NULL,
    PRIMARY KEY(acct_id),
    FOREIGN KEY (acct_id) REFERENCES account(acct_id)
);

CREATE TABLE checking_account (
	acct_id CHAR(5) NOT NULL,
    overdraft_limit DECIMAL(9,2) NOT NULL,
    PRIMARY KEY(acct_id),
    FOREIGN KEY (acct_id) REFERENCES account(acct_id)
);

CREATE TABLE employee (
	employee_id CHAR(5) NOT NULL,
    employee_name VARCHAR(50) NOT NULL,
    employee_position VARCHAR(50) NOT NULL, 
    salary DECIMAL(9,2) NOT NULL,
    PRIMARY KEY (employee_id)
);

CREATE TABLE loan (
	loan_id CHAR(5) UNIQUE NOT NULL,
    client_id CHAR(5) NOT NULL,
    loan_type VARCHAR(50) NOT NULL,
    loan_amount DECIMAL(9,2) NOT NULL,
    loan_interest_rate DECIMAL(4,3) NOT NULL,
    loan_start_date DATE NOT NULL,
	loan_end_date DATE NOT NULL,
    employee_id CHAR(5) NOT NULL,
    PRIMARY KEY (loan_id),
    FOREIGN KEY (client_id) REFERENCES client(client_id),
    FOREIGN KEY (employee_id) REFERENCES employee(employee_id)
);

INSERT INTO client VALUES
    ("00001", "Jacob Lash", "43rd. St.", "09358681544", "jacoblash@email.com", "1988-03-11"),
    ("00002", "Dean Abrams", "44th. St.", "09541245567", "abrdean@email.com", "1968-11-03"),
    ("00003", "Elizabeth McGinnis", "51st. St.", "09614567898", "lizmcginnis@email.com", "2001-07-14"),
    ("00004", "Mark Bebop", "52nd. St.", "09789651211", "markbebop@email.com", "1996-07-04");

    
INSERT INTO account VALUES
	("00001", "00001", "SAVINGS", 481536.75),
    ("00002", "00001", "CHECKING", 12000.00),
    ("00003", "00002", "SAVINGS", 515978.25),
    ("00004", "00003", "CHECKING", 128000.50),
    ("00005", "00004", "SAVINGS", 80050.75),
    ("00006", "00004", "CHECKING", 150000.00);
    
INSERT INTO savings_account VALUES
	("00001", 0.05),
    ("00003", 0.025),
    ("00005", 0.015);

INSERT INTO checking_account VALUES
	("00002", 20000.00),
    ("00004",10000.00),
    ("00006", 100000.00);

INSERT INTO employee VALUES
	("23785", "Isagi Yoichi", "Junior Loan Officer", 215000.00),
    ("86572", "Seishiro Nagi", "Senior Loan Officer", 350000.00),
    ("72451", "Rin Itoshi", "Senior Loan Officer", 350000.00),
    ("56412", "Ego Jinpachi", "Bank Manager", 500000.00),
    ("71234", "Kylian Mbappe", "Head of Bank Operations", 1250000.00),
    ("33412", "Julian Loki", "Head of Marketing", 1000000.00),
    ("10000", "John Paurbanc", "Chief Executive Officer", 4000000.00);

INSERT INTO loan VALUES
	("00001", "00001", "BUSINESS", 1000000.00, 0.05, "2004/12/14", "2011/12/14", "23785"),
    ("00002", "00001", "CAR", 800000.00, 0.20, "2005/11/5", "2010/5/11", "86572"),
    ("00003", "00002", "BUSINESS", 1500000.00, 0.025, "2008/7/21", "2018/7/21", "23785"),
    ("00004", "00002", "HOUSING", 2000000.00, 0.07, "2008/4/20", "2028/4/20", "72451"),
    ("00005", "00003", "STUDENT", 300000.00, 0.05, "2011/03/05", "2015/03/05", "86572"),
    ("00006", "00004", "CAR", 600000.00, 0.18, "2003/09/30", "2008/09/30", "72451");



